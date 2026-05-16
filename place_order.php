<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================================================
   1. INPUT
========================================================= */
$card_number = $_POST['card_number'];
$card_holder = $_POST['card_holder'];
$expiry = $_POST['expiry'];

$clean = str_replace(' ', '', $card_number);
$last4 = substr($clean, -4);
$masked = "**** **** **** " . $last4;

list($month, $year) = explode('/', $expiry);

/* =========================================================
   2. START TRANSACTION (IMPORTANT)
========================================================= */
sqlsrv_begin_transaction($conn);

try {

    /* =====================================================
       3. SAVE CARD
    ===================================================== */
    $sql1 = "
    INSERT INTO user_cards 
    (user_id, card_holder, card_last4, card_masked, expiry_month, expiry_year)
    VALUES (?, ?, ?, ?, ?, ?)
    ";

    $params1 = [$user_id, $card_holder, $last4, $masked, $month, $year];

    $stmt1 = sqlsrv_query($conn, $sql1, $params1);

    if ($stmt1 === false) {
        throw new Exception("Card insert failed");
    }

    /* =====================================================
       4. GET CART ITEMS
    ===================================================== */
    $sql2 = "
    SELECT c.food_id, c.quantity, m.price
    FROM cart c
    JOIN menu m ON c.food_id = m.food_id
    WHERE c.user_id = ?
    ";

    $stmt2 = sqlsrv_query($conn, $sql2, [$user_id]);

    if ($stmt2 === false) {
        throw new Exception("Cart fetch failed");
    }

    /* =====================================================
       5. INSERT ORDERS
    ===================================================== */
    while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {

        $food_id = $row['food_id'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $total_price = $price * $quantity;

        $sql3 = "
        INSERT INTO orders 
        (user_id, food_id, quantity, total_price, order_date, status)
        VALUES (?, ?, ?, ?, GETDATE(), 'PAID')
        ";

        $params3 = [$user_id, $food_id, $quantity, $total_price];

        $stmt3 = sqlsrv_query($conn, $sql3, $params3);

        if ($stmt3 === false) {
            throw new Exception("Order insert failed");
        }
    }

    /* =====================================================
       6. CLEAR CART
    ===================================================== */
    $sql4 = "DELETE FROM cart WHERE user_id = ?";
    $stmt4 = sqlsrv_query($conn, $sql4, [$user_id]);

    if ($stmt4 === false) {
        throw new Exception("Cart delete failed");
    }

    /* =====================================================
       7. COMMIT
    ===================================================== */
    sqlsrv_commit($conn);

} catch (Exception $e) {

    sqlsrv_rollback($conn);

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
    margin-bottom:10px;
}

p{
    color:#555;
    margin-bottom:20px;
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
    font-weight:bold;
}

.btn:hover{
    background:#1e272e;
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