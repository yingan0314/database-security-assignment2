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

/* 1️⃣ Get food price safely */
$sql = "SELECT price FROM menu WHERE food_id = ?";
$params = [$food_id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$row) {
    die("Food not found");
}

$price = $row['price'];
$total = $price * $qty;

/* 2️⃣ Insert into orders safely */
$insert = "
INSERT INTO orders (user_id, food_id, quantity, total_price)
VALUES (?, ?, ?, ?)
";

$params2 = [$user_id, $food_id, $qty, $total];

$stmt2 = sqlsrv_query($conn, $insert, $params2);

if ($stmt2 === false) {
    die(print_r(sqlsrv_errors(), true));
}

/* 3️⃣ redirect back */
header("Location: menu.php");
exit();
?>