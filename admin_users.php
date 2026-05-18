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
/* SECURITY CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: menu.php");
    exit();
}

/* =========================
   DELETE USER
========================= */
if (isset($_GET['delete'])) {

    $uid = $_GET['delete'];

    $sql = "DELETE FROM users WHERE user_id = ?";
    $params = [$uid];

    sqlsrv_query($conn, $sql, $params);

    header("Location: admin_users.php");
    exit();
}

/* =========================
   GET USERS (SQL SERVER)
========================= */
$sql = "SELECT user_id, username, role, created_at FROM users ORDER BY user_id DESC";
$stmt = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin - Users</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #141e30, #243b55);
    color:white;
}

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

.container{
    width:90%;
    max-width:1000px;
    margin:40px auto;
    background: rgba(255,255,255,0.08);
    padding:25px;
    border-radius:15px;
    backdrop-filter: blur(10px);
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:15px;
    text-align:center;
}

th{
    background:#ff4757;
}

tr{
    border-bottom:1px solid rgba(255,255,255,0.1);
}

tr:hover{
    background: rgba(255,255,255,0.08);
}

.role{
    padding:5px 10px;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
}

.admin{
    background:#ff6b81;
}

.customer{
    background:#2ed573;
}

.delete{
    padding:6px 10px;
    background:#ff4757;
    color:white;
    text-decoration:none;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="topbar">
    <div>👤 User Management</div>
    <div>
        <a href="admin.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">

<h2>Registered Users</h2>

<table>
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Role</th>
    <th>Created At</th>
    <th>Action</th>
</tr>

<?php
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
?>

<tr>
    <td><?= $row['user_id'] ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>

    <td>
        <span class="role <?= $row['role'] ?>">
            <?= $row['role'] ?>
        </span>
    </td>

    <td>
        <?php 
        if ($row['created_at']) {
            echo $row['created_at']->format('Y-m-d H:i:s');
        }
        ?>
    </td>

    <td>
        <?php if ($row['role'] != 'admin') { ?>
            <a class="delete"
               href="admin_users.php?delete=<?= $row['user_id'] ?>"
               onclick="return confirm('Delete this user?')">
               Delete
            </a>
        <?php } else { ?>
            <b>Protected</b>
        <?php } ?>
    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>