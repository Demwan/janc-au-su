<?php
// Foutmeldingen inschakelen
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// If the connection was successful
echo 'Connected successfully to the database.';

// Close the connection
$conn->close();
?>
