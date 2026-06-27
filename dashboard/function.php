<?php
include('../connection.php');


session_start();
ob_start();


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if(empty($_SESSION['user_id'])){
    header("location: ../login.php"); 
    exit();
}

$user_id = $_SESSION["user_id"];
// Ensure the database connection is valid.
if (!$conn) {
    die("Database connection failed.");
}

$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id); // "i" for integer type
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

$user_email = $user["email"];

$userId = $user['id'];


// Active cards
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM cards 
    WHERE user_id = ? AND status = 'Active'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($activeCards);
$stmt->fetch();
$stmt->close();

// Pending applications
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM card_requests 
    WHERE user_id = ? AND status = 'Pending'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($pendingCards);
$stmt->fetch();
$stmt->close();

// Total card balance (if cards draw from main balance, just show user balance)
$totalCardBalance = $user['total_balance'];

// Fetch issued cards
$stmt = $conn->prepare("
    SELECT * FROM cards 
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cards = $stmt->get_result();

?>