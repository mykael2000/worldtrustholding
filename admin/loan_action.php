<?php
include("../connection.php");
session_start();
require_once __DIR__ . '/../mail/aws_ses_mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: loan_list.php");
    exit;
}

$loanId = $_POST['loan_id'];
$action = $_POST['action'] ?? '';

if (!$loanId || !in_array($action, ['approve', 'reject'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: loan_list.php");
    exit;
}

/* FETCH LOAN */
$stmt = $conn->prepare("
    SELECT l.loan_id, l.user_id, l.amount, l.status, u.username, u.email
    FROM loans l
    JOIN users u ON u.id = l.user_id
    WHERE loan_id = ?
");
$stmt->bind_param("s", $loanId);
$stmt->execute();
$loan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$loan || $loan['status'] !== 'Pending') {
    $_SESSION['error'] = "Loan already processed or not found.";
    header("Location: loan_list.php");
    exit;
}

/* START TRANSACTION */
$conn->begin_transaction();

try {

    if ($action === 'approve') {

        /* UPDATE LOAN */
        $stmt = $conn->prepare("
            UPDATE loans 
            SET status = 'Approved'
            WHERE loan_id = ?
        ");
        $stmt->bind_param("s", $loanId);
        $stmt->execute();
        $stmt->close();

        /* CREDIT USER BALANCE */
        $stmt = $conn->prepare("
            UPDATE users
            SET total_balance = total_balance + ?
            WHERE id = ?
        ");
        $stmt->bind_param("di", $loan['amount'], $loan['user_id']);
        $stmt->execute();
        $stmt->close();

        /* OPTIONAL: LOG TO HISTORY */
        $tranx_id = 'LN' . strtoupper(bin2hex(random_bytes(5)));

        $stmt = $conn->prepare("
            INSERT INTO history
            (client_id, tranx_id, amount, type, status, description, created_at)
            VALUES (?, ?, ?, 'Credit', 'Completed', 'Loan Disbursement', NOW())
        ");
        $stmt->bind_param(
            "isd",
            $loan['user_id'],
            $tranx_id,
            $loan['amount']
        );
        $stmt->execute();
        $stmt->close();

        if (!empty($loan['email'])) {
            $txEmail = afc_build_transaction_alert_email([
                'username' => $loan['username'],
                'email' => $loan['email'],
                'tranx_id' => $tranx_id,
                'amount' => $loan['amount'],
                'details' => 'Loan Disbursement',
                'type' => 'Credit',
                'status' => 'Completed',
                'description' => 'Loan Disbursement',
                'created_at' => date('Y-m-d H:i:s'),
            ], [
                'channel' => 'loan',
                'event_label' => 'Loan Approved and Disbursed',
            ]);

            $mailResult = afc_send_aws_raw_email([
                'to' => [$loan['email']],
                'subject' => $txEmail['subject'],
                'html_body' => $txEmail['html_body'],
                'text_body' => $txEmail['text_body'],
            ]);
            if (!$mailResult['success']) {
                error_log('AFC transaction mail failed (loan action): ' . ($mailResult['error'] ?? 'unknown'));
            }
        }

    } else {

        /* REJECT LOAN */
        $stmt = $conn->prepare("
            UPDATE loans 
            SET status = 'Rejected'
            WHERE loan_id = ?
        ");
        $stmt->bind_param("s", $loanId);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    $_SESSION['success'] = "Loan successfully {$action}ed.";

} catch (Exception $e) {

    $conn->rollback();
    $_SESSION['error'] = "Operation failed. Please try again.";
}

header("Location: loan_list.php");
exit;
