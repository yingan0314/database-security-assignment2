<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================================================
   INPUT
========================================================= */
$card_number = $_POST['card_number'];
$card_holder = $_POST['card_holder'];
$card_type = $_POST['card_type'];
$expiry = $_POST['expiry'];
$cvv = $_POST['cvv'];

/* =========================================================
   VALIDATION
========================================================= */
$clean = preg_replace('/\s+/', '', $card_number);

if (!ctype_digit($clean)) {
    die("Invalid card number");
}

if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
    die("Invalid CVV");
}

if (!str_contains($expiry, '/')) {
    die("Invalid expiry format");
}

list($month, $year) = explode('/', $expiry);

if ($month < 1 || $month > 12) {
    die("Invalid expiry month");
}

$last4 = substr($clean, -4);

/* =========================================================
   TRANSACTION START - MySQL Version
========================================================= */
$conn->begin_transaction();

try {

    /* SAVE CARD - MySQL Version */
    $sql1 = "INSERT INTO user_cards (CustomerID, CardType, CardNumber, CardLast4, ExpMonth, ExpYear)
             VALUES (?, ?, ?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("isssii", $user_id, $card_type, $clean, $last4, $month, $year);
    $stmt1->execute();

    /* CART ITEMS - MySQL Version */
    $sql2 = "SELECT c.food_id, c.quantity, m.price
             FROM cart c
             JOIN menu m ON c.food_id = m.food_id
             WHERE c.user_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    /* ORDERS - MySQL Version */
    while ($row = $result2->fetch_assoc()) {

        $food_id = $row['food_id'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $total_price = $price * $quantity;

        $sql3 = "INSERT INTO orders (user_id, food_id, quantity, total_price, order_date, status)
                 VALUES (?, ?, ?, ?, NOW(), 'PAID')";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("iiid", $user_id, $food_id, $quantity, $total_price);
        $stmt3->execute();
    }

    /* CLEAR CART - MySQL Version */
    $sql4 = "DELETE FROM cart WHERE user_id = ?";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("i", $user_id);
    $stmt4->execute();

    $conn->commit();

} catch (Exception $e) {

    $conn->rollback();
    die("❌ Payment failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payment Success</title>

<style>
body{
    margin:0;
    font-family:Segoe UI,Arial;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg,#2ed573,#1eae60);
}

.box{
    background:white;
    padding:40px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 20px 40px rgba(0,0,0,0.2);
    width:350px;
}

h1{
    color:#2ed573;
}

p{
    color:#555;
}

.loading{
    width:40px;
    height:40px;
    border:4px solid #eee;
    border-top:4px solid #2ed573;
    border-radius:50%;
    margin:20px auto;
    animation:spin 1s linear infinite;
}

@keyframes spin{
    0%{transform:rotate(0deg);}
    100%{transform:rotate(360deg);}
}

.btn{
    display:inline-block;
    margin-top:10px;
    padding:10px 20px;
    background:#2f3542;
    color:white;
    text-decoration:none;
    border-radius:10px;
}
</style>

</head>

<body>

<div class="box">

    <h1>✅ Payment Successful</h1>
    <p>Your order has been placed successfully.</p>

    <div class="loading"></div>

    <p>Redirecting to menu...</p>

    <a class="btn" href="menu.php">Go Now</a>

</div>

<script>
setTimeout(() => {
    window.location.href = "menu.php";
}, 3000);
</script>

</body>
</html>