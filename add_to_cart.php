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
   1. GET PRICE (SQL Server style)
===================================================== */
$sql = "SELECT price FROM menu WHERE food_id = ?";
$params = [$food_id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$total_price = $row['price'] * $qty;

/* =====================================================
   2. INSERT CART
===================================================== */
$sql2 = "
INSERT INTO cart (user_id, food_id, quantity, total_price)
VALUES (?, ?, ?, ?)
";

$params2 = [$user_id, $food_id, $qty, $total_price];

$stmt2 = sqlsrv_query($conn, $sql2, $params2);

if ($stmt2 === false) {
    die(print_r(sqlsrv_errors(), true));
}

/* redirect */
header("Location: menu.php");
exit();
?>