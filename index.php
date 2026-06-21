<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Food Ordering System</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(135deg, #ff6b6b, #feca57);
}

/* 🍔 背景图片层 */
body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80');
    background-size: cover;
    background-position: center;
    filter: blur(8px);
    opacity: 0.35;
    z-index: 0;
}

/* 🔵 floating shapes */
.circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    animation: float 6s infinite ease-in-out;
}

.circle1 { width: 120px; height: 120px; top: 10%; left: 10%; }
.circle2 { width: 80px; height: 80px; bottom: 15%; right: 15%; }
.circle3 { width: 60px; height: 60px; top: 60%; left: 80%; }

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

/* 📦 WHITE CENTER CARD (你要的重点) */
.box {
    position: relative;
    z-index: 1;
    width: 380px;
    padding: 40px;
    border-radius: 18px;

    background: #ffffff;   /* ⭐ 改成纯白 */
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    text-align: center;
}

/* 🍔 icon */
.logo {
    font-size: 55px;
}

h1 {
    margin: 10px 0;
    font-size: 22px;
    color: #333;
}

p {
    font-size: 14px;
    color: #666;
    margin-bottom: 25px;
}

/* 🔘 buttons */
.btn {
    display: block;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.login {
    background: #ff4757;
    color: white;
}

.login:hover {
    background: #e84118;
}

.register {
    background: #2ed573;
    color: white;
}

.register:hover {
    background: #1eae60;
}

.footer {
    margin-top: 15px;
    font-size: 11px;
    color: #999;
}

</style>
</head>

<body>

<!-- floating decorations -->
<div class="circle circle1"></div>
<div class="circle circle2"></div>
<div class="circle circle3"></div>

<div class="box">

    <div class="logo">🍔</div>

    <h1>Food Ordering System</h1>
    <p>Order delicious food anytime, anywhere</p>

    <a class="btn login" href="login.php">🔐 Login</a>
    <a class="btn register" href="register.php">📝 Register</a>

    <div class="footer">
        Database Security Project © 2026
    </div>

</div>

</body>
</html>