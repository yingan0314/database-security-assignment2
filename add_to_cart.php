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

/* =====================================================
   1. GET PRICE - MySQL Version
===================================================== */
$sql = "SELECT price FROM menu WHERE food_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $food_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_price = $row['price'] * $qty;

/* =====================================================
   2. INSERT CART - MySQL Version
===================================================== */
$sql2 = "INSERT INTO cart (user_id, food_id, quantity, total_price) VALUES (?, ?, ?, ?)";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("iiid", $user_id, $food_id, $qty, $total_price);
$stmt2->execute();

/* redirect */
header("Location: menu.php");
exit();
?>