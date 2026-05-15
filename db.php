<?php
$conn = new mysqli("localhost", "root", "", "food_ordering");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>