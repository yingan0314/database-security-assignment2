<?php
include "db.php";

$msg = "";

/* =========================
   PASSWORD VALIDATION (SERVER SIDE)
========================= */
function validatePassword($password) {

    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "8+ characters";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "1 uppercase";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "1 lowercase";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "1 number";
    }

    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "1 special char";
    }

    return $errors;
}

/* =========================
   REGISTER PROCESS
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = trim($_POST['username']);
    $p1 = $_POST['password'];
    $p2 = $_POST['confirm_password'];

    if ($p1 !== $p2) {
        $msg = "❌ Passwords do not match!";
    } else {

        $errors = validatePassword($p1);

        if (!empty($errors)) {
            $msg = "❌ Password must have: " . implode(", ", $errors);
        } else {

            $sql = "SELECT user_id FROM users WHERE username = ?";
            $check = sqlsrv_query($conn, $sql, [$u]);

            if ($check === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC)) {
                $msg = "❌ Username already taken!";
            } else {

                $hash = password_hash($p1, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')";
                sqlsrv_query($conn, $sql, [$u, $hash]);

                header("Location: login.php");
                exit();
            }
        }
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
    background: #2ed573;
    color: white;
}

.msg {
    margin-top: 10px;
    color: red;
    font-size: 13px;
}

/* live boxes */
#pw-msg, #cp-msg {
    text-align: left;
    font-size: 12px;
    margin-top: 5px;
}
</style>
</head>

<body>

<div class="box">

    <h2>📝 Register</h2>

    <form method="POST">

        <input name="username" placeholder="Username" required>

        <!-- PASSWORD -->
        <div style="position:relative;">
            <input id="password" name="password" type="password" placeholder="Password" required>

            <span id="togglePw"
                  style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
                👁️
            </span>
        </div>

        <div id="pw-msg"></div>

        <!-- CONFIRM PASSWORD -->
        <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm Password" required>

        <div id="cp-msg"></div>

        <button type="submit">Register</button>
    </form>

    <?php if ($msg != "") echo "<div class='msg'>$msg</div>"; ?>

</div>

<!-- =========================
   JS (LIVE VALIDATION + CONFIRM + EYE)
========================= -->
<script>

const pw = document.getElementById("password");
const msg = document.getElementById("pw-msg");

const cp = document.getElementById("confirm_password");
const cpMsg = document.getElementById("cp-msg");

const toggle = document.getElementById("togglePw");

/* PASSWORD RULE CHECK */
pw.addEventListener("input", function () {

    let v = pw.value;
    let output = [];

    output.push(v.length >= 8 ? "✔ 8+ characters" : "❌ 8+ characters");
    output.push(/[A-Z]/.test(v) ? "✔ uppercase" : "❌ uppercase");
    output.push(/[a-z]/.test(v) ? "✔ lowercase" : "❌ lowercase");
    output.push(/[0-9]/.test(v) ? "✔ number" : "❌ number");
    output.push(/[\W_]/.test(v) ? "✔ special char" : "❌ special char");

    msg.innerHTML = output.join("<br>");

    checkMatch();
});

/* CONFIRM PASSWORD CHECK */
cp.addEventListener("input", checkMatch);

function checkMatch() {

    if (cp.value.length === 0) {
        cpMsg.innerHTML = "";
        return;
    }

    if (cp.value === pw.value) {
        cpMsg.innerHTML = "✔ Password match";
        cpMsg.style.color = "green";
    } else {
        cpMsg.innerHTML = "❌ Password not match";
        cpMsg.style.color = "red";
    }
}

/* SHOW / HIDE PASSWORD */
toggle.addEventListener("click", function () {

    if (pw.type === "password") {
        pw.type = "text";
        toggle.textContent = "🔒";
    } else {
        pw.type = "password";
        toggle.textContent = "👁️";
    }
});

</script>

</body>
</html>