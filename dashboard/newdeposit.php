<?php
include("header.php");
require_once __DIR__ . '/../mail/aws_ses_mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: deposits.php");
    exit;
}

$amount = floatval($_POST['amount'] ?? 0);
$paymentMethod = trim($_POST['payment_method'] ?? '');

if ($amount <= 0 || !$paymentMethod) {
    $_SESSION['error'] = "Invalid deposit request.";
    header("Location: deposits.php");
    exit;
}

/* -------------------------
   FETCH USER
--------------------------*/
$stmt = $conn->prepare("
    SELECT id, username, email 
    FROM users 
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: deposit.php");
    exit;
}

/* -------------------------
   FETCH DEPOSIT ADDRESS
--------------------------*/
$stmt = $conn->prepare("
    SELECT currency, network, address
    FROM deposit_accounts
    WHERE method = ? AND is_active = 1
    LIMIT 1
");
$stmt->bind_param("s", $paymentMethod);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();

if (!$account) {
    $_SESSION['error'] = "Deposit method unavailable.";
    header("Location: deposits.php");
    exit;
}

/* -------------------------
   BUILD DETAILS (JSON)
--------------------------*/
$details = json_encode([
    'method'   => $paymentMethod,
    'currency' => $account['currency'],
    'network'  => $account['network'],
    'address'  => $account['address']
]);
$description = $account['currency'].':'.$account['address'];
$tranx_id = 'TX' . strtoupper(bin2hex(random_bytes(6)));

/* -------------------------
   INSERT INTO HISTORY
--------------------------*/
$stmt = $conn->prepare("
    INSERT INTO history
    (client_id, username, email, tranx_id, amount, details, type, status, description, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'Credit', 'Pending', ?, NOW())
");

$stmt->bind_param(
    "isssdss",
    $user['id'],
    $user['username'],
    $user['email'],
    $tranx_id,
    $amount,
    $details,
    $description
);

$stmt->execute();

$txEmail = afc_build_transaction_alert_email([
    'username' => $user['username'],
    'email' => $user['email'],
    'tranx_id' => $tranx_id,
    'amount' => $amount,
    'details' => $details,
    'type' => 'Credit',
    'status' => 'Pending',
    'description' => $description,
    'created_at' => date('Y-m-d H:i:s'),
], [
    'channel' => 'deposit',
    'event_label' => 'Deposit Request Submitted',
]);

$mailResult = afc_send_aws_raw_email([
    'to' => [$user['email']],
    'subject' => $txEmail['subject'],
    'html_body' => $txEmail['html_body'],
    'text_body' => $txEmail['text_body'],
]);
if (!$mailResult['success']) {
    error_log('AFC transaction mail failed (new deposit): ' . ($mailResult['error'] ?? 'unknown'));
}

/* -------------------------
   STORE FOR CONFIRM PAGE
--------------------------*/
$_SESSION['deposit_confirm'] = [
    'amount'   => $amount,
    'method'   => $paymentMethod,
    'currency' => $account['currency'],
    'network'  => $account['network'],
    'address'  => $account['address'],
    'tranx_id' => $tranx_id
];

header("Location: deposit_confirm.php");
exit;
