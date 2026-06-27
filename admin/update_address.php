<?php
include("includes/header.php");

$id       = intval($_POST['id']);
$currency = trim($_POST['currency']);
$network  = trim($_POST['network']);
$address  = trim($_POST['address']);
$status   = $_POST['is_active'];

$stmt = $conn->prepare("
    UPDATE deposit_accounts
    SET currency=?, network=?, address=?, is_active=?
    WHERE id=?
");
$stmt->bind_param("ssssi", $currency, $network, $address, $status, $id);
$stmt->execute();

header("Location: address.php");
exit;
