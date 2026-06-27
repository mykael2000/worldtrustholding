<?php
include("includes/header.php");

$ticket_id = intval($_POST['ticket_id']);
$status = $_POST['status'];

$stmt = $conn->prepare("
    UPDATE support_tickets SET status = ? WHERE id = ?
");
$stmt->bind_param("si", $status, $ticket_id);
$stmt->execute();

header("Location: view_ticket.php?id=".$ticket_id);
exit;
