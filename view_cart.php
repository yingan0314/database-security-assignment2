<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* SQL Server query */
$sql = "
SELECT c.*, m.food_name, m.image, m.price
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
$hasData = false;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Cart</title>

<style>
/* keep ALL your CSS unchanged */
body{
    margin:0;
    font-family:Segoe UI,Arial;
    background:#f5f6fa;
}

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

.container{
    width:900px;
    margin:auto;
    padding:20px;
}

.card{
    display:flex;
    align-items:center;
    background:white;
    margin:15px 0;
    border-radius:16px;
    box-shadow:0 6px 15px rgba(0,0,0,0.08);
    overflow:hidden;
}

.card img{
    width:140px;
    height:110px;
    object-fit:cover;
}

.info{
    flex:1;
    padding:10px 15px;
}

.name{
    font-size:18px;
    font-weight:600;
}

.qty{
    font-size:13px;
    color:#777;
    margin-top:4px;
}

.price{
    width:120px;
    text-align:center;
    font-size:18px;
    font-weight:bold;
    color:#ff4757;
}

form{
    margin-right:15px;
}

button{
    background:#fff0f0;
    border:1px solid #ff6b6b;
    color:#ff6b6b;
    padding:8px 10px;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:#ff6b6b;
    color:white;
}

.empty{
    text-align:center;
    margin-top:80px;
    color:#888;
    font-size:18px;
}

.checkout{
    margin-top:25px;
    background:white;
    padding:20px;
    border-radius:16px;
    box-shadow:0 6px 15px rgba(0,0,0,0.08);
    text-align:center;
}

.total{
    font-size:22px;
    font-weight:bold;
    color:#2ed573;
}

.checkout button{
    margin-top:12px;
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#2ed573;
    color:white;
    font-size:16px;
    font-weight:bold;
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

<?php
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

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
<?php } else { ?>

<div class="checkout">
    <div class="total">TOTAL: RM <?php echo number_format($total,2); ?></div>

    <form method="POST" action="checkout.php">
        <button>Checkout 💳</button>
    </form>
</div>

<?php } ?>

</div>

</body>
</html>