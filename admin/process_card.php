<?php
include("includes/header.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: card_requests.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT cr.*, u.total_balance
    FROM card_requests cr
    JOIN users u ON u.id = cr.user_id
    WHERE cr.id = ? AND cr.status = 'Pending'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    $_SESSION['error'] = "Request not found.";
    header("Location: card_requests.php");
    exit;
}

/* -------------------------
   REJECT
--------------------------*/
if (isset($_POST['reject'])) {
    $conn->query("UPDATE card_requests SET status='Rejected' WHERE id=$id");
    $_SESSION['success'] = "Card request rejected.";
    header("Location: card_requests.php");
    exit;
}

/* -------------------------
   APPROVE
--------------------------*/
if (isset($_POST['approve'])) {

    if ($request['total_balance'] < $request['fee']) {
        $_SESSION['error'] = "User has insufficient balance.";
        header("Location: card_requests.php");
        exit;
    }

    /* Generate Card */
    $card_number = "4" . rand(100000000000000,999999999999999);
    $cvv = rand(100,999);
    $expiry = date("m/Y", strtotime("+3 years"));

    $conn->begin_transaction();

    try {
        // Deduct balance
        $conn->query("
            UPDATE users 
            SET total_balance = total_balance - {$request['fee']}
            WHERE id = {$request['user_id']}
        ");

        // Create card
        $stmt = $conn->prepare("
            INSERT INTO cards 
            (user_id, card_type, card_level, currency, daily_limit, card_number, expiry_date, cvv)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssdsss",
            $request['user_id'],
            $request['card_type'],
            $request['card_level'],
            $request['currency'],
            $request['daily_limit'],
            $card_number,
            $expiry,
            $cvv
        );
        $stmt->execute();

        // Update request
        $conn->query("UPDATE card_requests SET status='Approved' WHERE id=$id");

        $conn->commit();
        $_SESSION['success'] = "Card approved and issued successfully.";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Approval failed.";
    }

    header("Location: card_requests.php");
    exit;
}
