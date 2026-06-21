<?php
session_start();
include "db.php";  // ⚠️ 改成 db.php

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

/* SECURITY CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: menu.php");
    exit();
}

$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// ============================
// MySQL Version - Filter Query
// ============================
if ($statusFilter == 'all') {
    $sql = "SELECT * FROM login_logs ORDER BY attempt_time DESC";  // ⚠️ login_time → attempt_time
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM login_logs WHERE status = ? ORDER BY attempt_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $statusFilter);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login Audit Logs</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg,#1f1c2c,#928dab);
    color:white;
}

/* TOP BAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 30px;
    background:rgba(0,0,0,0.3);
    backdrop-filter: blur(10px);
}

.topbar a{
    color:white;
    text-decoration:none;
    padding:8px 14px;
    border-radius:8px;
    background:rgba(255,255,255,0.15);
}

.topbar a:hover{
    background:#ff4757;
}

/* TITLE */
.title{
    text-align:center;
    margin-top:25px;
    font-size:26px;
    font-weight:600;
}

/* FILTER */
.filter{
    text-align:center;
    margin:20px;
}

.filter a{
    margin:5px;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    background:rgba(255,255,255,0.15);
}

.filter a.active{
    background:#ff4757;
}

/* TABLE CARD */
.container{
    width:90%;
    margin:20px auto;
    background:rgba(255,255,255,0.12);
    backdrop-filter: blur(12px);
    padding:20px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:12px;
}

th{
    background:#ff4757;
    padding:12px;
}

td{
    padding:12px;
    text-align:center;
    background:rgba(255,255,255,0.08);
}

tr:hover td{
    background:rgba(255,255,255,0.18);
}

/* BADGE */
.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.success{ background:#2ed573; }
.failed{ background:#ff4757; }
.notfound{ background:#ffa502; }

</style>
</head>

<body>

<div class="topbar">
    <div>📊 Login Security Dashboard</div>
    <a href="admin.php">⬅ Back</a>
</div>

<div class="title">Login Audit Logs</div>

<div class="filter">
    <a href="admin_logs.php" class="<?php if($statusFilter=='all') echo 'active'; ?>">All</a>
    <a href="admin_logs.php?status=SUCCESS" class="<?php if($statusFilter=='SUCCESS') echo 'active'; ?>">Success</a>
    <a href="admin_logs.php?status=FAILED" class="<?php if($statusFilter=='FAILED') echo 'active'; ?>">Failed</a>
    <a href="admin_logs.php?status=NOT_FOUND" class="<?php if($statusFilter=='NOT_FOUND') echo 'active'; ?>">Not Found</a>
</div>

<div class="container">

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Status</th>
        <th>IP Address</th>
        <th>Time</th>
    </tr>

<?php while ($row = $result->fetch_assoc()) { 

    $status = strtolower($row['status']);
?>

<tr>
    <td><?php echo $row['id']; ?></td>  <!-- ⚠️ log_id → id -->
    <td><?php echo $row['username']; ?></td>

    <td>
        <?php if($status == 'success'){ ?>
            <span class="badge success">SUCCESS</span>
        <?php } elseif($status == 'failed'){ ?>
            <span class="badge failed">FAILED</span>
        <?php } else { ?>
            <span class="badge notfound">NOT FOUND</span>
        <?php } ?>
    </td>

    <td><?php echo $row['ip_address']; ?></td>
    <td><?php echo $row['attempt_time']; ?></td>  <!-- ⚠️ login_time → attempt_time，去掉 format() -->
</tr>

<?php } ?>

</table>

</div>

</body>
</html>