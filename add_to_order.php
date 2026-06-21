<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$food_id = $_POST['food_id'];
$qty = $_POST['qty'];

/* 1️⃣ Get food price safely - MySQL Version */
$sql = "SELECT price FROM menu WHERE food_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $food_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Food not found");
}

$price = $row['price'];
$total = $price * $qty;

/* 2️⃣ Insert into orders safely - MySQL Version */
$insert = "INSERT INTO orders (user_id, food_id, quantity, total_price) VALUES (?, ?, ?, ?)";
$stmt2 = $conn->prepare($insert);
$stmt2->bind_param("iiid", $user_id, $food_id, $qty, $total);
$stmt2->execute();

/* 3️⃣ redirect back */
header("Location: menu.php");
exit();
?>