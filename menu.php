<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Food Menu</title>

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

.topbar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    margin-left: 10px;
}

/* filter */
.filter {
    text-align: center;
    margin: 15px;
}

.filter a {
    display: inline-block;
    padding: 10px 15px;
    margin: 5px;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    background: white;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    transition: 0.3s;
}

/* ⭐ active category */
.filter a.active {
    background: #ff4757;
    color: white;
}

/* hover */
.filter a:hover {
    background: #ff4757;
    color: white;
}

/* container */
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    padding: 10px;
}

/* card */
.card {
    width: 250px;
    background: white;
    margin: 15px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.content {
    padding: 15px;
}

.food-name {
    font-size: 18px;
    font-weight: bold;
}

.price {
    color: #ff4757;
    font-weight: bold;
    margin: 5px 0;
}

.desc {
    font-size: 12px;
    color: #666;
    margin-bottom: 10px;
}

input {
    width: 50px;
    padding: 5px;
    margin-bottom: 8px;
}

button {
    width: 100%;
    padding: 10px;
    border: none;
    background: #2ed573;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
}

button:hover {
    background: #1eae60;
}

/* no result */
.empty {
    text-align: center;
    margin-top: 50px;
    color: #666;
}

</style>
</head>

<body>

<div class="topbar">
    <div>🍔 Food Menu</div>
    <div>
        <a href="view_cart.php">My Orders</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- FILTER -->
<div class="filter">
    <a class="<?php if($category=='all') echo 'active'; ?>" href="menu.php?category=all">All</a>
    <a class="<?php if($category=='rice') echo 'active'; ?>" href="menu.php?category=rice">Rice</a>
    <a class="<?php if($category=='noodle') echo 'active'; ?>" href="menu.php?category=noodle">Noodle</a>
    <a class="<?php if($category=='western') echo 'active'; ?>" href="menu.php?category=western">Western</a>
    <a class="<?php if($category=='drink') echo 'active'; ?>" href="menu.php?category=drink">Drink</a>
</div>

<div class="container">

<?php
// SQL
if ($category == 'all') {
    $stmt = $conn->prepare("SELECT * FROM menu");
} else {
    $stmt = $conn->prepare("SELECT * FROM menu WHERE category = ?");
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='empty'>No food found 🍽️</div>";
}

while ($row = $result->fetch_assoc()) {
?>

<div class="card">

    <img src="<?php echo $row['image']; ?>">

    <div class="content">

        <div class="food-name"><?php echo $row['food_name']; ?></div>

        <div class="price">RM <?php echo $row['price']; ?></div>

        <div class="desc"><?php echo $row['description']; ?></div>

        <form method="POST" action="add_to_cart.php">
            <input type="hidden" name="food_id" value="<?php echo $row['food_id']; ?>">

            Qty:
            <input type="number" name="qty" value="1" min="1">

            <button type="submit">Add to Order</button>
        </form>

    </div>

</div>

<?php } ?>

</div>

</body>
</html>