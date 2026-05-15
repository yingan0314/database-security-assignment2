<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT c.*, m.food_name, m.image, m.price
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
<title>My Cart</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial;
    background: linear-gradient(to right, #f4f4f4, #e9f5ff);
}

/* top bar */
.topbar {
    background: #ff4757;
    color: white;
    padding: 18px;
    display: flex;
    justify-content: space-between;
    font-size: 18px;
}

/* container */
.container {
    padding: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* BIG CARD */
.card {
    width: 850px;
    background: white;
    margin: 15px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    padding: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    transition: 0.3s;
}

.card:hover {
    transform: scale(1.02);
}

/* image bigger */
.card img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 15px;
    margin-right: 20px;
}

/* info */
.info {
    flex: 1;
}

.name {
    font-size: 22px;
    font-weight: bold;
}

.qty {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.price {
    font-size: 20px;
    font-weight: bold;
    color: #ff4757;
}

/* buttons */
.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
}

.delete {
    background: #ff6b6b;
    color: white;
}

.delete:hover {
    background: #e84141;
}

/* checkout box */
.checkout {
    width: 850px;
    background: linear-gradient(135deg, #2ed573, #1eae60);
    color: white;
    padding: 20px;
    border-radius: 20px;
    text-align: center;
    margin-top: 20px;
}

.checkout button {
    margin-top: 10px;
    padding: 12px 25px;
    border: none;
    border-radius: 12px;
    background: white;
    color: #1eae60;
    font-weight: bold;
    cursor: pointer;
}

.empty {
    margin-top: 60px;
    font-size: 20px;
    color: #666;
}
</style>

</head>

<body>

<div class="topbar">
    <div>🛒 My Cart</div>
    <div>
        <a href="menu.php" style="color:white;">Menu</a> |
        <a href="logout.php" style="color:white;">Logout</a>
    </div>
</div>

<div class="container">

<?php
if ($result->num_rows == 0) {
    echo "<div class='empty'>Your cart is empty 🛒</div>";
}

while ($row = $result->fetch_assoc()) {

    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<div class="card">

    <img src="<?php echo $row['image']; ?>">

    <div class="info">
        <div class="name"><?php echo $row['food_name']; ?></div>
        <div class="qty">Quantity: <?php echo $row['quantity']; ?></div>
    </div>

    <div class="price">
        RM <?php echo number_format($subtotal,2); ?>
    </div>

    <form method="POST" action="remove_cart.php">
        <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
        <button class="btn delete">Delete</button>
    </form>

</div>

<?php } ?>

<?php if ($result->num_rows > 0) { ?>
<div class="checkout">
    TOTAL: RM <?php echo number_format($total,2); ?>

    <form method="POST" action="checkout.php">
        <button>Checkout 💳</button>
    </form>
</div>
<?php } ?>

</div>

</body>
</html>