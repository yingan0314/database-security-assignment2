<?php

$serverName = "192.168.0.55";
$database = "food_ordering";
$username = "admin";  
$password = 'Pa$$w0rd';   

$conn = sqlsrv_connect($serverName, [
    "Database" => $database,
    "Uid" => $username,
    "PWD" => $password,
    "Encrypt" => "no",
    "TrustServerCertificate" => true
]);

if ($conn === false) {
    die("<pre>Connection failed:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

?>