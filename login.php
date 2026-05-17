<?php
session_start();
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = $_POST['username'];
    $p = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR']; // 👈 NEW: get IP address

    // SQL Server query
    $sql = "SELECT * FROM users WHERE username = ?";
    $params = [$u];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        // verify hashed password
        if (password_verify($p, $row['password'])) {

            // =========================
            // LOGIN SUCCESS LOG
            // =========================
            $log_sql = "INSERT INTO login_logs (username, status, ip_address) VALUES (?, 'SUCCESS', ?)";
            sqlsrv_query($conn, $log_sql, [$u, $ip]);

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];

            // role redirect
            if ($row['role'] == "admin") {
                header("Location: admin.php");
                exit();
            } else {
                header("Location: menu.php");
                exit();
            }

        } else {

            // =========================
            // LOGIN FAILED LOG
            // =========================
            $log_sql = "INSERT INTO login_logs (username, status, ip_address) VALUES (?, 'FAILED', ?)";
            sqlsrv_query($conn, $log_sql, [$u, $ip]);

            $msg = "❌ Wrong password";
        }

    } else {

        // =========================
        // USER NOT FOUND LOG
        // =========================
        $log_sql = "INSERT INTO login_logs (username, status, ip_address) VALUES (?, 'NOT_FOUND', ?)";
        sqlsrv_query($conn, $log_sql, [$u, $ip]);

        $msg = "❌ User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #ff6b6b, #feca57);
}

body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: url('https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=1200&q=80');
    background-size: cover;
    background-position: center;
    filter: blur(8px);
    opacity: 0.3;
    z-index: 0;
}

.box {
    position: relative;
    z-index: 1;
    width: 360px;
    padding: 35px;
    border-radius: 18px;
    background: white;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    text-align: center;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    background: #ff4757;
    color: white;
}

.msg {
    margin-top: 10px;
    font-size: 13px;
    color: red;
}
</style>
</head>

<body>

<div class="box">

    <h2>🔐 Login</h2>

    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <a href="register.php">No account? Register</a>
    <a class="back" href="index.php">⬅ Back to Home</a>

    <?php if ($msg != "") echo "<div class='msg'>$msg</div>"; ?>

</div>

</body>
</html>