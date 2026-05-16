<?php

$serverName = "192.168.0.146";
$database = "food_ordering";
$username = "sa";   // 或 sa
$password = 'Pa$$w0rd';    // 改成你的

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