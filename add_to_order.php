<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$food_id = $_POST['food_id'];
$qty = $_POST['qty'];

$food = $conn->query("SELECT * FROM menu WHERE food_id=$food_id")->fetch_assoc();

$total = $food['price'] * $qty;

$conn->query("INSERT INTO orders (user_id, food_id, quantity, total_price)
VALUES ($user_id, $food_id, $qty, $total)");

header("Location: menu.php");
?>