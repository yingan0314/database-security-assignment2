<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT o.*, m.food_name
FROM orders o
JOIN menu m ON o.food_id = m.food_id
WHERE o.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Checkout</title>

<style>

body{
    font-family: Arial;
    background:#f4f4f4;
    margin:0;
}

.container{
    width:700px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:15px;
}

.item{
    display:flex;
    justify-content:space-between;
    padding:15px 0;
    border-bottom:1px solid #ddd;
}

.total{
    text-align:right;
    font-size:24px;
    margin-top:20px;
    font-weight:bold;
}

button{
    width:100%;
    padding:15px;
    background:#ff4757;
    color:white;
    border:none;
    border-radius:10px;
    font-size:18px;
    cursor:pointer;
    margin-top:20px;
}

button:hover{
    background:#ff3748;
}

</style>
</head>

<body>

<div class="container">

<h1>Checkout</h1>

<?php while($row = $result->fetch_assoc()) {

    $subtotal = $row['total_price'];
    $total += $subtotal;
?>

<div class="item">

    <div>
        <b><?php echo $row['food_name']; ?></b>
        <br>
        Qty: <?php echo $row['quantity']; ?>
    </div>

    <div>
        RM <?php echo number_format($subtotal,2); ?>
    </div>

</div>

<?php } ?>

<div class="total">
    Total: RM <?php echo number_format($total,2); ?>
</div>

<form action="place_order.php" method="POST">
    <button type="submit">
        Place Order
    </button>
</form>

</div>

</body>
</html>