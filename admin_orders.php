<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$timeout = 600;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$_SESSION['last_activity'] = time();
/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: menu.php");
    exit();
}

/* =========================
   GET ORDERS (SQL SERVER)
========================= */
$sql = "
SELECT 
    o.order_id,
    o.user_id,
    u.username,
    o.food_id,
    m.food_name,
    o.quantity,
    o.total_price,
    o.status,
    o.order_date
FROM orders o
JOIN users u ON o.user_id = u.user_id
JOIN menu m ON o.food_id = m.food_id
ORDER BY o.order_id DESC
";

$stmt = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Orders</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    color:white;
}

/* TOPBAR */
.topbar{
    background: rgba(0,0,0,0.3);
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    backdrop-filter: blur(10px);
}

.topbar a{
    color:white;
    text-decoration:none;
    margin-left:10px;
    padding:8px 12px;
    border-radius:8px;
    background: rgba(255,255,255,0.15);
}

.topbar a:hover{
    background:#ff4757;
}

/* CONTAINER */
.container{
    width:95%;
    max-width:1200px;
    margin:40px auto;
    background: rgba(255,255,255,0.08);
    padding:25px;
    border-radius:15px;
    backdrop-filter: blur(10px);
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:14px;
    text-align:center;
}

th{
    background:#ff4757;
    font-size:14px;
}

tr{
    border-bottom:1px solid rgba(255,255,255,0.1);
}

tr:hover{
    background: rgba(255,255,255,0.08);
}

/* STATUS */
.status{
    padding:5px 10px;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
}

.PAID{
    background:#2ed573;
}

.PENDING{
    background:#ffa502;
}

.CANCELLED{
    background:#ff4757;
}
</style>
</head>

<body>

<div class="topbar">
    <div>🛒 Order Management</div>
    <div>
        <a href="admin.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">

<h2>All Orders</h2>

<table>
<tr>
    <th>Order ID</th>
    <th>User</th>
    <th>Food</th>
    <th>Qty</th>
    <th>Total (RM)</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
?>

<tr>
    <td><?= $row['order_id'] ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= $row['food_name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td>RM <?= number_format($row['total_price'], 2) ?></td>

    <td>
        <span class="status <?= $row['status'] ?>">
            <?= $row['status'] ?>
        </span>
    </td>

    <td>
        <?php 
        if ($row['order_date']) {
            echo $row['order_date']->format('Y-m-d H:i:s');
        }
        ?>
    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>