<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ============================
// MySQL Version - Cart Query
// ============================
$sql = "
SELECT c.*, m.food_name, m.price
FROM cart c
JOIN menu m ON c.food_id = m.food_id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
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

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background:#f4f6fb;
    color:#222;
}

/* PAGE WRAPPER */
.wrapper{
    width:92%;
    max-width:1200px;
    margin:40px auto;
    display:flex;
    gap:28px;
    align-items:flex-start;
}

/* LEFT CART */
.cart{
    flex:1.2;
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
    border:1px solid #eee;
}

/* RIGHT PAYMENT */
.payment{
    width:340px;
    position:sticky;
    top:20px;
    background:#fff;
    border-radius:18px;
    padding:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
    border:1px solid #eee;
}

/* HEADER */
h1{
    font-size:20px;
    margin:0 0 5px 0;
}

.subtitle{
    font-size:13px;
    color:#888;
    margin-bottom:20px;
}

/* ITEM */
.item{
    display:flex;
    justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid #f0f0f0;
}

.item b{
    font-size:15px;
}

/* TOTAL */
.total{
    margin-top:20px;
    padding-top:15px;
    border-top:2px solid #f0f0f0;
    font-size:20px;
    font-weight:600;
    color:#ff6b00;
    text-align:right;
}

/* INPUT STYLE */
label{
    font-size:12px;
    color:#666;
    display:block;
    margin-top:10px;
}

input, select{
    width:100%;
    padding:9px 10px;
    margin-top:6px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:13px;
    outline:none;
    box-sizing:border-box;
    transition:0.2s;
}

input:focus, select:focus{
    border-color:#ff6b00;
    box-shadow:0 0 0 3px rgba(255,107,0,0.1);
}

/* BUTTON */
button{
    width:100%;
    margin-top:18px;
    padding:12px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#ff6b00,#ff8c2a);
    color:white;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:0.25s;
    box-shadow:0 10px 20px rgba(255,107,0,0.2);
}

button:hover{
    transform:translateY(-2px);
}

/* CARD NUMBER SPACING */
#card_number{
    letter-spacing:1px;
}

/* EMPTY */
.empty{
    color:#999;
    font-size:14px;
}

/* RESPONSIVE */
@media(max-width:900px){
    .wrapper{
        flex-direction:column;
    }
    .payment{
        width:100%;
        position:relative;
        top:0;
    }
}

</style>
</head>

<body>

<div class="wrapper">

<!-- CART -->
<div class="cart">

<h1>🛒 Order Summary</h1>
<div class="subtitle">Check your items before payment</div>

<?php
$hasData = false;

// ============================
// MySQL Version - Fetch Rows
// ============================
while ($row = $result->fetch_assoc()) {

    $hasData = true;
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<div class="item">
    <div>
        <b><?= $row['food_name'] ?></b><br>
        <small style="color:#888;">Qty: <?= $row['quantity'] ?></small>
    </div>

    <div>
        RM <?= number_format($subtotal,2) ?>
    </div>
</div>

<?php } ?>

<?php if (!$hasData) { ?>
    <p class="empty">Your cart is empty.</p>
<?php } else { ?>
    <div class="total">
        Total RM <?= number_format($total,2) ?>
    </div>
<?php } ?>

</div>

<!-- PAYMENT -->
<div class="payment">

<h1>💳 Payment</h1>
<div class="subtitle">Secure checkout</div>

<form action="place_order.php" method="POST" onsubmit="return validatePaymentForm()">

<input type="hidden" name="total_amount" value="<?= $total ?>">

<label>Payment Method</label>
<select name="payment_method" required>
    <option value="">Select</option>
    <option>Credit Card</option>
</select>

<label>Card Type</label>
<select name="card_type" required>
    <option value="">Select</option>
    <option>VISA</option>
    <option>MASTERCARD</option>
    <option>AMEX</option>
</select>

<label>Card Number</label>
<input type="text" name="card_number" id="card_number" maxlength="19" required>

<label>Card Holder</label>
<input type="text" name="card_holder" id="card_holder" required>

<label>Expiry (MM/YYYY)</label>
<input type="text" name="expiry" id="expiry" maxlength="7" required>

<label>CVV</label>
<input type="password" name="cvv" id="cvv" maxlength="3" required>

<button type="submit">Pay RM <?= number_format($total,2) ?></button>

</form>

</div>

</div>

<script>

// format card number
document.getElementById("card_number").addEventListener("input", e=>{
    let v = e.target.value.replace(/\D/g,"").substring(0,16);
    e.target.value = v.replace(/(.{4})/g,"$1 ").trim();
});

// format expiry
document.getElementById("expiry").addEventListener("input", e=>{
    let v = e.target.value.replace(/\D/g,"").substring(0,6);
    if(v.length>=3) v = v.substring(0,2)+"/"+v.substring(2);
    e.target.value = v;
});

// validation
function validatePaymentForm(){
    let card = document.getElementById("card_number").value.replace(/\s/g,"");
    if(!/^\d{16}$/.test(card)) return alert("Invalid card"), false;

    let name = document.getElementById("card_holder").value.trim();
    if(name.length<3) return alert("Invalid name"), false;

    let exp = document.getElementById("expiry").value.trim();
    if(!/^(0[1-9]|1[0-2])\/\d{4}$/.test(exp)) return alert("Invalid expiry"), false;

    let cvv = document.getElementById("cvv").value.trim();
    if(!/^\d{3}$/.test(cvv)) return alert("Invalid CVV"), false;

    return true;
}

</script>

</body>
</html>