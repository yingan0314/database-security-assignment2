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
    header("Location: cart.php");
    exit();
}

$cart_id = $_POST['cart_id'];

/* 3️⃣ SQL Server delete */
$sql = "DELETE FROM cart WHERE cart_id = ?";
$params = [$cart_id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

/* 4️⃣ redirect back */
header("Location: view_cart.php");
exit();
?>