<?php
session_start();
include "dbadmin.php";

/* =========================
   AUTHENTICATION + AUTHORIZATION
========================= */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: menu.php");
    exit();
}

/* =========================
   SESSION TIMEOUT (30 mins)
========================= */
$timeout = 600; // 30 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #1f1c2c, #928dab);
    min-height:100vh;
}

/* TOP BAR */
.topbar{
    background: rgba(0,0,0,0.25);
    backdrop-filter: blur(10px);
    color:white;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

.topbar .title{
    font-size:18px;
    font-weight:600;
}

.topbar a{
    color:white;
    text-decoration:none;
    margin-left:15px;
    padding:8px 12px;
    border-radius:8px;
    background: rgba(255,255,255,0.15);
    transition:0.3s;
}

.topbar a:hover{
    background:#ff4757;
}

/* WELCOME */
.welcome{
    text-align:center;
    color:white;
    margin-top:40px;
}

.welcome h1{
    font-size:28px;
    margin-bottom:5px;
}

.welcome p{
    opacity:0.8;
}

/* CONTAINER */
.container{
    display:flex;
    justify-content:center;
    flex-wrap:wrap;
    gap:25px;
    margin-top:50px;
}

/* CARD */
.card{
    width:260px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(12px);
    border:1px solid rgba(255,255,255,0.2);
    padding:25px;
    border-radius:18px;
    text-align:center;
    color:white;
    transition:0.3s;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

.card:hover{
    transform: translateY(-10px);
    background: rgba(255,255,255,0.18);
}

/* ICON */
.icon{
    font-size:40px;
    margin-bottom:10px;
}

/* TITLE */
.card h3{
    margin:10px 0;
    font-size:18px;
}

/* BUTTON */
.card a{
    display:inline-block;
    margin-top:15px;
    padding:10px 18px;
    background:#ff4757;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:500;
    transition:0.3s;
}

.card a:hover{
    background:#ff6b81;
}
</style>
</head>

<body>

<div class="topbar">
    <div class="title">👑 Admin Dashboard</div>
    <div>
        <a href="menu.php">User View</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="welcome">
    <h1>Welcome Back, Admin 👋</h1>
    <p>Manage your food ordering system</p>
</div>

<div class="container">

    <div class="card">
        <div class="icon">🍔</div>
        <h3>Food Management</h3>
        <p>Add, edit or delete menu items</p>
        <a href="admin_food.php">Open</a>
    </div>

    <div class="card">
        <div class="icon">🛒</div>
        <h3>Orders</h3>
        <p>View all customer orders</p>
        <a href="admin_orders.php">Open</a>
    </div>

    <div class="card">
        <div class="icon">👤</div>
        <h3>Users</h3>
        <p>Manage registered users</p>
        <a href="admin_users.php">Open</a>
    </div>
    <div class="card">
    <div class="icon">📊</div>
    <h3>Login Logs</h3>
    <p>View all login attempts (audit trail)</p>
    <a href="admin_logs.php">Open</a>
</div>

</div>

</body>
</html>