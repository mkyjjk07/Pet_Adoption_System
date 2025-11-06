<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "pet_adoption";

$conn = new mysqli($host, $username, $password, $database,3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
