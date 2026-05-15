<?php
include "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = trim($_POST['username']);
    $p1 = $_POST['password'];
    $p2 = $_POST['confirm_password'];

    // 1. password check
    if ($p1 !== $p2) {
        $msg = "❌ Passwords do not match!";
    } else {

        // 2. check username exist
        $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check->bind_param("s", $u);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = "❌ Username already taken!";
        } else {

            // 3. insert user
            $hash = password_hash($p1, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $u, $hash);
            $stmt->execute();

            // 4. redirect login
            header("Location: login.php");
            exit();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Register</title>

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

/* background image blur */
body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: url('https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=1200&q=80');
    background-size: cover;
    background-position: center;
    filter: blur(8px);
    opacity: 0.3;
    z-index: 0;
}

/* card */
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
    background: #2ed573;
    color: white;
    transition: 0.3s;
}

button:hover {
    background: #1eae60;
}

a {
    display: block;
    margin-top: 10px;
    text-decoration: none;
    color: #555;
    font-size: 13px;
}

a:hover {
    color: #2ed573;
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

.msg {
    margin-top: 10px;
    font-size: 13px;
    color: red;
}
</style>
</head>

<body>

<div class="box">

    <h2>📝 Register</h2>

    <form method="POST">
        <input name="username" placeholder="Username" required>

        <input name="password" type="password" placeholder="Password" required>

        <input name="confirm_password" type="password" placeholder="Confirm Password" required>

        <button type="submit">Register</button>
    </form>

    <a href="login.php">Already have account? Login</a>
    <a class="back" href="index.php">⬅ Back to Home</a>

    <?php if ($msg != "") echo "<div class='msg'>$msg</div>"; ?>

</div>

</body>
</html>