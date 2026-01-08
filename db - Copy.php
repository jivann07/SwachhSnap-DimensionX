<?php
$servername = "localhost";
$username = "root";
$password = "admin"; // <--- We put the password here!
$dbname = "swachhsnap";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>