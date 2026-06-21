<?php
session_start();
include "db.php";

/* 1️⃣ check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* 2️⃣ validate input */
if (!isset($_POST['cart_id'])) {
    header("Location: view_cart.php");
    exit();
}

$cart_id = $_POST['cart_id'];

/* 3️⃣ MySQL Delete */
$sql = "DELETE FROM cart WHERE cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();

/* 4️⃣ redirect back */
header("Location: view_cart.php");
exit();
?>