<?php
include "db.php";
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
    color: green;
}
</style>
</head>

<body>

<div class="box">

    <h2>📝 Register</h2>

    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <a href="login.php">Already have account? Login</a>

    <!-- ⭐ Back button -->
    <a class="back" href="index.php">⬅ Back to Home</a>

<?php
if ($_POST) {

    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ⚠️ better version (safer)
    $stmt = $conn->prepare("INSERT INTO users (username,password) VALUES (?,?)");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();

    echo "<div class='msg'>Registered successfully!</div>";
}
?>

</div>

</body>
</html>