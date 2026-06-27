<?php
session_start();
include("header.php");

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Session expired. Please login again.";
    header("Location: apply-card.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: apply-card.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* -------------------------
   GET INPUTS
--------------------------*/
$card_type        = $_POST['card_type'] ?? '';
$card_level       = $_POST['card_level'] ?? '';
$currency         = $_POST['currency'] ?? 'USD';
$daily_limit      = floatval($_POST['daily_limit'] ?? 0);
$card_holder_name = trim($_POST['card_holder_name'] ?? '');
$billing_address  = trim($_POST['billing_address'] ?? '');
$terms            = isset($_POST['terms_accepted']);

/* -------------------------
   VALIDATION
--------------------------*/
if (
    !$card_type || !$card_level || !$daily_limit ||
    !$card_holder_name || !$billing_address || !$terms
) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: apply-card.php");
    exit;
}

/* -------------------------
   CARD FEES
--------------------------*/
$fees = [
    'standard' => 5,
    'gold'     => 15,
    'platinum' => 25,
    'black'    => 50
];

$fee = $fees[$card_level] ?? 0;

if ($fee === 0) {
    $_SESSION['error'] = "Invalid card level selected.";
    header("Location: apply-card.php");
    exit;
}

/* -------------------------
   SAVE REQUEST
--------------------------*/
$stmt = $conn->prepare("
    INSERT INTO card_requests 
    (user_id, card_type, card_level, currency, daily_limit, card_holder_name, billing_address, fee)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssdssd",
    $user_id,
    $card_type,
    $card_level,
    $currency,
    $daily_limit,
    $card_holder_name,
    $billing_address,
    $fee
);

if (!$stmt->execute()) {
    $_SESSION['error'] = "Unable to submit card request. Please try again.";
    header("Location: apply-card.php");
    exit;
}

/* -------------------------
   SUCCESS
--------------------------*/
$_SESSION['success'] = "Card request submitted successfully. Our team will contact you once approved.";
header("Location: cards.php");
exit;
