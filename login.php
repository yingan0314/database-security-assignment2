<?php
session_start();
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = $_POST['username'];
    $p = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($p, $row['password'])) {

            $_SESSION['user_id'] = $row['user_id'];
            header("Location: menu.php");
            exit();

        } else {
            $msg = "❌ Wrong password";
        }

    } else {
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

h2 {
    margin-bottom: 20px;
    color: #333;
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

button:hover {
    background: #e84118;
}

a {
    display: block;
    margin-top: 10px;
    text-decoration: none;
    color: #555;
    font-size: 13px;
}

a:hover {
    color: #ff4757;
}

.back {
    margin-top: 15px;
    display: inline-block;
    padding: 10px;
    border-radius: 8px;
    background: #2f3542;
    color: white;
    text-decoration: none;
    font-size: 13px;
}

.back:hover {
    background: #1e272e;
}

/* error message */
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