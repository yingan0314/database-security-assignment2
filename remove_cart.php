<?php
session_start();
include "db.php";

$cart_id = $_POST['cart_id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();

header("Location: view_cart.php");
exit();
?>