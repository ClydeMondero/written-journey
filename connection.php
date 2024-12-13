<?php
$host = "localhost:3306";
$username = "root";
$password = "";
$database = "journal";

// Create the database connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check if connection failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
