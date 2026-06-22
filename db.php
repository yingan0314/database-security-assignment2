<?php
$host = "place your RDSEndpoint";
$database = "fooddb";
$username = "admin";
$password = "Admin123456!";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
