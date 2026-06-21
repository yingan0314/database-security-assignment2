<?php
// ============================================
// Database Configuration - MySQL Version
// For AWS RDS Deployment (Assignment 2)
// ============================================

// Local XAMPP Testing
$host = "localhost";
$database = "fooddb";
$username = "root";       
$password = "";            // XAMPP 默认空密码

// AWS RDS Deployment (上线时取消注释下面这行)
// $host = "your-rds-endpoint.amazonaws.com";
// $username = "admin";
// $password = "Admin123456!";

// Create MySQL Connection
$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set Character Set to UTF-8 (支持中文)
$conn->set_charset("utf8");

// Uncomment for debugging
// echo "Database connected successfully!";
?>