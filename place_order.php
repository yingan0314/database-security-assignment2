<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================
   1. GET CARD INPUT
========================= */
$card_number = $_POST['card_number'];
$card_holder = $_POST['card_holder'];
$expiry = $_POST['expiry'];

/* clean card number */
$clean = str_replace(' ', '', $card_number);

/* get last 4 digits */
$last4 = substr($clean, -4);

/* masked format */
$masked = "**** **** **** " . $last4;

/* split expiry */
list($month, $year) = explode('/', $expiry);


/* =========================
   2. SAVE CARD (SAFE VERSION)
========================= */

$stmt = $conn->prepare("
INSERT INTO user_cards 
(user_id, card_holder, card_last4, card_masked, expiry_month, expiry_year)
VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssss",
    $user_id,
    $card_holder,
    $last4,
    $masked,
    $month,
    $year
);

$stmt->execute();


/* =========================
   3. UPDATE ORDERS (mark paid)
========================= */

$conn->query("
UPDATE orders 
SET status = 'PAID' 
WHERE user_id = $user_id
");

/* =========================
   4. CLEAR CART (optional)
========================= */

$conn->query("
DELETE FROM orders 
WHERE user_id = $user_id
");


/* =========================
   5. DONE
========================= */

echo "Payment successful!";
?>