<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* 🔥 关键：从 cart 读取，而不是 orders */
$stmt = $conn->prepare("
SELECT c.*, m.food_name, m.price
FROM cart c
JOIN menu m ON c.food_id = m.food_id
WHERE c.user_id = ?
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
<title>Payment</title>

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
    box-shadow:0 10px 20px rgba(0,0,0,0.1);
}

h1{
    margin-bottom:25px;
    color:#333;
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
    color:#ff4757;
}

.payment-box{
    margin-top:35px;
}

label{
    font-weight:bold;
    display:block;
    margin-top:15px;
    margin-bottom:8px;
}

input, select{
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
    font-size:15px;
    box-sizing:border-box;
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
    margin-top:30px;
}

button:hover{
    background:#ff3748;
}
</style>
</head>

<body>

<div class="container">

<h1>💳 Payment</h1>

<?php if ($result->num_rows == 0) { ?>
    <p>Your cart is empty.</p>
<?php } else { ?>

<?php while($row = $result->fetch_assoc()) {

    /* 🔥 correct subtotal calculation */
    $subtotal = $row['price'] * $row['quantity'];
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

<?php } ?>

<div class="payment-box">

<form action="place_order.php" method="POST">

    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="">-- Select Payment Method --</option>
        <option value="Credit Card">Credit Card</option>
    </select>

    <label>Card Number</label>
    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required>

    <label>Card Holder Name</label>
    <input type="text" name="card_holder" placeholder="John Doe" required>

    <label>Expiry Date</label>
    <input type="text" name="expiry" placeholder="MM/YY" required>

    <label>CVV</label>
    <input type="password" name="cvv" placeholder="123" required>

    <button type="submit">Pay Now</button>

</form>

</div>

</div>

</body>
</html>