<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT o.*, m.food_name, m.image 
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
<title>My Orders</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial;
    background: #f4f4f4;
}

/* top bar */
.topbar {
    background: #ff4757;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
}

/* container */
.container {
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* order card */
.card {
    width: 650px;
    background: white;
    margin: 12px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    padding: 10px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.card:hover {
    transform: scale(1.02);
}

/* food image */
.card img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 12px;
    margin-right: 15px;
}

/* info */
.info {
    flex: 1;
}

.food-name {
    font-size: 18px;
    font-weight: bold;
}

.qty {
    font-size: 13px;
    color: #666;
}

/* price */
.price {
    font-size: 16px;
    font-weight: bold;
    color: #ff4757;
}

/* total box */
.total {
    width: 650px;
    background: linear-gradient(135deg, #2ed573, #1eae60);
    color: white;
    padding: 15px;
    margin-top: 20px;
    border-radius: 15px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
}

/* empty */
.empty {
    margin-top: 50px;
    font-size: 18px;
    color: #666;
}

</style>
</head>

<body>

<div class="topbar">
    <div>🧾 My Orders</div>
    <div>
        <a href="menu.php" style="color:white; text-decoration:none;">Menu</a> |
        <a href="logout.php" style="color:white; text-decoration:none;">Logout</a>
    </div>
</div>

<div class="container">

<?php
if ($result->num_rows == 0) {
    echo "<div class='empty'>No orders yet 🍽️</div>";
}

while ($row = $result->fetch_assoc()) {

    $subtotal = $row['total_price'];
    $total += $subtotal;
?>

<div class="card">

    <!-- 🍔 image -->
    <img src="<?php echo $row['image']; ?>">

    <!-- info -->
    <div class="info">
        <div class="food-name">
            <?php echo $row['food_name']; ?>
        </div>

        <div class="qty">
            Quantity: <?php echo $row['quantity']; ?>
        </div>
    </div>

    <!-- price -->
    <div class="price">
        RM <?php echo number_format($subtotal, 2); ?>
    </div>

</div>

<?php } ?>

<?php if ($result->num_rows > 0) { ?>
<div class="total">
    Total: RM <?php echo number_format($total, 2); ?>
</div>
<?php } ?>

</div>

</body>
</html>