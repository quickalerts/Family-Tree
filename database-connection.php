<?php
$host = "localhost";
$user = "root";  // Default user for MySQL
$password = "";  // No password by default
$dbname = "family";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
