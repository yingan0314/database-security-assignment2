<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================================================
   1. GET PAYMENT INPUT
========================================================= */
$card_number = $_POST['card_number'];
$card_holder = $_POST['card_holder'];
$expiry = $_POST['expiry'];

/* clean card number */
$clean = str_replace(' ', '', $card_number);

/* last 4 digits */
$last4 = substr($clean, -4);

/* masked format */
$masked = "**** **** **** " . $last4;

/* split expiry */
list($month, $year) = explode('/', $expiry);


/* =========================================================
   2. SAVE CARD (MASKED ONLY)
========================================================= */
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


/* =========================================================
   3. GET CART ITEMS
========================================================= */
$cartQuery = $conn->prepare("
SELECT c.food_id, c.quantity, m.price
FROM cart c
JOIN menu m ON c.food_id = m.food_id
WHERE c.user_id = ?
");

$cartQuery->bind_param("i", $user_id);
$cartQuery->execute();
$result = $cartQuery->get_result();


/* =========================================================
   4. INSERT ORDERS
   (FIXED: correct table structure)
========================================================= */
$orderStmt = $conn->prepare("
INSERT INTO orders 
(user_id, food_id, quantity, total_price, order_date, status)
VALUES (?, ?, ?, ?, NOW(), 'PAID')
");

while ($row = $result->fetch_assoc()) {

    $food_id = $row['food_id'];
    $quantity = $row['quantity'];
    $price = $row['price'];

    $total_price = $price * $quantity;

    $orderStmt->bind_param(
        "iiid",
        $user_id,
        $food_id,
        $quantity,
        $total_price
    );

    $orderStmt->execute();
}


/* =========================================================
   5. CLEAR CART
========================================================= */
$deleteCart = $conn->prepare("
DELETE FROM cart WHERE user_id = ?
");

$deleteCart->bind_param("i", $user_id);
$deleteCart->execute();


/* =========================================================
   6. SUCCESS MESSAGE
========================================================= */
echo "<h2>Payment Successful!</h2>";
echo "<p>Your order has been placed.</p>";
?>