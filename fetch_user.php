<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
        echo "<script> location.href='new_url'; </script>";
        exit;
}


// Fetch user data
$user_id = $_SESSION['user_id']; // Retrieve user_id from session
$stmt = $conn->prepare('SELECT first_name, last_name FROM users WHERE user_id = ?');
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);
$stmt->close();
$conn->close();
?>
