<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['food_id'], $_POST['qty'])) {
    header("Location: menu.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$food_id = $_POST['food_id'];
$qty = $_POST['qty'];

/* get price */
$stmt = $conn->prepare("SELECT price FROM menu WHERE food_id=?");
$stmt->bind_param("i", $food_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_price = $row['price'] * $qty;

/* insert cart */
$stmt = $conn->prepare("
INSERT INTO cart (user_id, food_id, quantity, total_price)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param("iiid", $user_id, $food_id, $qty, $total_price);
$stmt->execute();

/* redirect back */
header("Location: menu.php");
exit();
?>