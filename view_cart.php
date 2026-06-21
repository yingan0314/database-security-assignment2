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
SELECT c.*, m.food_name, m.image, m.price
FROM cart c
JOIN menu m ON c.food_id = m.food_id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$hasData = false;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Cart</title>

<style>

/* ================= TOPBAR (KEEP) ================= */
.topbar{
    background:#ff4757;
    color:white;
    padding:15px 20px;
    display:flex;
    justify-content:space-between;
    font-weight:bold;
}

.topbar a{
    color:white;
    text-decoration:none;
    margin-left:10px;
}

/* ================= BACKGROUND ================= */
body{
    margin:0;
    font-family:'Segoe UI', Arial;
    background:#f6f7fb;
}

/* ================= LAYOUT ================= */
.container{
    width:1050px;
    margin:30px auto;
    display:flex;
    gap:25px;
}

/* LEFT */
.cart-list{
    flex:2;
}

/* RIGHT */
.summary{
    flex:1;
    position:sticky;
    top:20px;
    height:fit-content;
}

/* ================= CART CARD ================= */
.card{
    display:flex;
    align-items:center;
    background:white;
    margin-bottom:14px;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    transition:0.25s;
}

.card:hover{
    transform:translateY(-4px);
    box-shadow:0 12px 30px rgba(0,0,0,0.12);
}

.card img{
    width:135px;
    height:105px;
    object-fit:cover;
}

/* TEXT AREA */
.info{
    flex:1;
    padding:14px 16px;
}

.name{
    font-size:17px;
    font-weight:800;
    color:#222;
}

.qty{
    margin-top:4px;
    font-size:13px;
    color:#888;
}

/* PRICE */
.price{
    width:110px;
    text-align:center;
    font-size:16px;
    font-weight:800;
    color:#ff4757;
}

/* REMOVE */
button{
    margin-right:12px;
    background:transparent;
    border:1.8px solid #ff4757;
    color:#ff4757;
    padding:7px 11px;
    border-radius:12px;
    font-weight:700;
    cursor:pointer;
    transition:0.2s;
}

button:hover{
    background:#ff4757;
    color:white;
}

/* ================= SUMMARY (MODERN CARD) ================= */
.summary-box{
    background:white;
    border-radius:22px;
    padding:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.summary-title{
    font-size:18px;
    font-weight:800;
    margin-bottom:10px;
}

.subtext{
    font-size:13px;
    color:#888;
    margin-bottom:20px;
}

/* TOTAL BIG VISUAL */
.total{
    font-size:28px;
    font-weight:900;
    color:#2ed573;
    margin:15px 0;
}

/* CTA BUTTON */
.checkout-btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg,#ff4757,#ff6b81);
    color:white;
    font-size:15px;
    font-weight:900;
    cursor:pointer;
    transition:0.2s;
    box-shadow:0 8px 18px rgba(255,71,87,0.3);
}

.checkout-btn:hover{
    transform:scale(1.02);
}

/* EMPTY */
.empty{
    text-align:center;
    margin-top:90px;
    color:#aaa;
    font-size:18px;
}

</style>
</head>

<body>

<div class="topbar">
    <div>🛒 My Cart</div>
    <div>
        <a href="menu.php">Menu</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">

<!-- LEFT -->
<div class="cart-list">

<?php
// ============================
// MySQL Version - Fetch Rows
// ============================
while ($row = $result->fetch_assoc()) {

    $hasData = true;

    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<div class="card">

    <img src="<?php echo $row['image']; ?>">

    <div class="info">
        <div class="name"><?php echo $row['food_name']; ?></div>
        <div class="qty">Quantity: <?php echo $row['quantity']; ?></div>
    </div>

    <form method="POST" action="remove_cart.php">
        <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
        <button>Remove</button>
    </form>

    <div class="price">
        RM <?php echo number_format($subtotal,2); ?>
    </div>

</div>

<?php } ?>

<?php if (!$hasData) { ?>
    <div class="empty">Your cart is empty 🛒</div>
<?php } ?>

</div>

<!-- RIGHT -->
<?php if ($hasData) { ?>
<div class="summary">

    <div class="summary-box">

        <div class="summary-title">Order Summary</div>
        <div class="subtext">Almost there — review before payment</div>

        <div class="total">
            RM <?php echo number_format($total,2); ?>
        </div>

        <form method="POST" action="checkout.php">
            <button class="checkout-btn">Checkout Now 💳</button>
        </form>

    </div>

</div>
<?php } ?>

</div>

</body>
</html>