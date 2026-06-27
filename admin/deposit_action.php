<?php
include("includes/header.php");
require_once __DIR__ . '/../mail/aws_ses_mailer.php';

$id     = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'fail'])) {
    header("Location: deposits.php");
    exit;
}

/* -------------------------
   FETCH TRANSACTION
--------------------------*/
$stmt = $conn->prepare("
    SELECT h.id, h.amount, h.status, h.client_id, h.username, h.email, h.tranx_id, h.details, h.type, h.description, h.created_at
    FROM history h
    WHERE h.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$tx = $stmt->get_result()->fetch_assoc();

if (!$tx || $tx['status'] !== 'Pending') {
    header("Location: deposits.php");
    exit;
}

/* -------------------------
   APPROVE
--------------------------*/
if ($action === 'approve') {

    // Credit balance
    $stmt = $conn->prepare("
        UPDATE users 
        SET total_balance = total_balance + ?
        WHERE id = ?
    ");
    $stmt->bind_param("di", $tx['amount'], $tx['client_id']);
    $stmt->execute();

    // Update history
    $stmt = $conn->prepare("
        UPDATE history 
        SET status = 'Completed'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $tx['status'] = 'Completed';
}

/* -------------------------
   FAIL
--------------------------*/
if ($action === 'fail') {
    $stmt = $conn->prepare("
        UPDATE history 
        SET status = 'Failed'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $tx['status'] = 'Failed';
}

if (!empty($tx['email'])) {
    $txEmail = afc_build_transaction_alert_email($tx, [
        'channel' => 'deposit',
        'event_label' => $action === 'approve' ? 'Deposit Approved' : 'Deposit Marked Failed',
    ]);

    $mailResult = afc_send_aws_raw_email([
        'to' => [$tx['email']],
        'subject' => $txEmail['subject'],
        'html_body' => $txEmail['html_body'],
        'text_body' => $txEmail['text_body'],
    ]);
    if (!$mailResult['success']) {
        error_log('AFC transaction mail failed (deposit action): ' . ($mailResult['error'] ?? 'unknown'));
    }
}

header("Location: deposits.php");
exit;
