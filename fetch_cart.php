<?php
session_start();
$user_id = $_SESSION['user_id']; // Retrieve user_id from session

// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die('User is not logged in.');
}


$query = "SELECT cart FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_json);
$stmt->fetch();
$stmt->close();
$conn->close();

echo $cart_json; // Output the cart JSON
?>