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

.container {
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* card */
.card {
    width: 700px;
    background: white;
    margin: 12px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    padding: 10px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

/* image bigger */
.card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 12px;
    margin-right: 15px;
}

.info {
    flex: 1;
}

.name {
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

/* total */
.total {
    width: 700px;
    margin-top: 20px;
    background: linear-gradient(135deg, #2ed573, #1eae60);
    color: white;
    padding: 15px;
    border-radius: 15px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
}

.empty {
    margin-top: 50px;
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
    echo "<div class='empty'>Cart is empty 🛒</div>";
}

while ($row = $result->fetch_assoc()) {

    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<div class="card">

    <img src="<?php echo $row['image']; ?>">

    <div class="info">
        <div class="name"><?php echo $row['food_name']; ?></div>
        <div class="qty">Qty: <?php echo $row['quantity']; ?></div>
    </div>

    <div class="price">
        RM <?php echo number_format($subtotal,2); ?>
    </div>

</div>

<?php } ?>

<?php if ($result->num_rows > 0) { ?>
<div class="total">
    TOTAL: RM <?php echo number_format($total,2); ?>
</div>
<?php } ?>

</div>

</body>
</html>