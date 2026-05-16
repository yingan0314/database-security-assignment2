<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT c.*, m.food_name, m.price
FROM cart c
JOIN menu m ON c.food_id = m.food_id
WHERE c.user_id = ?
";

$params = [$user_id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

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

input, select{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid #ccc;
    border-radius:10px;
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
</style>
</head>

<body>

<div class="container">

<h1>💳 Payment</h1>

<?php
$hasData = false;

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $hasData = true;

    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<div class="item">
    <div>
        <b><?php echo $row['food_name']; ?></b><br>
        Qty: <?php echo $row['quantity']; ?>
    </div>

    <div>
        RM <?php echo number_format($subtotal,2); ?>
    </div>
</div>

<?php } ?>

<?php if (!$hasData) { ?>
    <p>Your cart is empty.</p>
<?php } else { ?>

<div class="total">
    Total: RM <?php echo number_format($total,2); ?>
</div>

<?php } ?>

<div class="payment-box">

<form action="place_order.php" method="POST" onsubmit="return validatePaymentForm()">

    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

    <!-- PAYMENT METHOD -->
    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="">-- Select Payment Method --</option>
        <option value="Credit Card">Credit Card</option>
    </select>

    <!-- CARD NUMBER -->
    <label>Card Number</label>
    <input 
        type="text"
        name="card_number"
        id="card_number"
        placeholder="1234 5678 9012 3456"
        maxlength="19"
        inputmode="numeric"
        required
    >

    <!-- CARD HOLDER -->
    <label>Card Holder Name</label>
    <input 
        type="text"
        name="card_holder"
        id="card_holder"
        placeholder="John Doe"
        required
    >

    <!-- EXPIRY -->
    <label>Expiry Date</label>
    <input 
        type="text"
        name="expiry"
        id="expiry"
        placeholder="MM/YYYY"
        maxlength="7"
        required
    >

    <!-- CVV -->
    <label>CVV</label>
    <input 
        type="password"
        name="cvv"
        id="cvv"
        placeholder="123"
        maxlength="3"
        inputmode="numeric"
        required
    >

    <button type="submit">Pay Now</button>

</form>

</div>

</div>

<!-- ================= JS VALIDATION ================= -->
<script>

// auto format card number (xxxx xxxx xxxx xxxx)
document.getElementById("card_number").addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");
    value = value.substring(0, 16);

    let formatted = value.replace(/(.{4})/g, "$1 ").trim();
    e.target.value = formatted;
});

// auto format expiry MM/YYYY
document.getElementById("expiry").addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");

    if (value.length >= 3) {
        value = value.substring(0, 6);
        value = value.substring(0, 2) + "/" + value.substring(2);
    }

    e.target.value = value;
});

function validatePaymentForm() {

    // CARD NUMBER (strict 16 digits)
    let cardNumber = document.getElementById("card_number").value.replace(/\s/g, "");

    if (!/^\d{16}$/.test(cardNumber)) {
        alert("Card number must be exactly 16 digits.");
        return false;
    }

    // CARD HOLDER
    let cardHolder = document.getElementById("card_holder").value.trim();

    if (cardHolder.length < 3) {
        alert("Invalid card holder name.");
        return false;
    }

    // EXPIRY MM/YYYY
    let expiry = document.getElementById("expiry").value.trim();

    if (!/^(0[1-9]|1[0-2])\/\d{4}$/.test(expiry)) {
        alert("Expiry must be MM/YYYY format.");
        return false;
    }

    let parts = expiry.split("/");
    let month = parseInt(parts[0]);
    let year = parseInt(parts[1]);

    let now = new Date();
    let currentMonth = now.getMonth() + 1;
    let currentYear = now.getFullYear();

    if (year < currentYear || (year === currentYear && month < currentMonth)) {
        alert("Card is expired.");
        return false;
    }

    // CVV
    let cvv = document.getElementById("cvv").value.trim();

    if (!/^\d{3}$/.test(cvv)) {
        alert("CVV must be 3 digits.");
        return false;
    }

    return true;
}

</script>

</body>
</html>