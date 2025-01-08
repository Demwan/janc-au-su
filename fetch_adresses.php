<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User is not logged in']);
    http_response_code(401);  // Unauthorized
    exit();
}

// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user_id from session
$user_id = $_SESSION['user_id'];

// Fetch addresses for the user
$stmt = $conn->prepare("SELECT street, city, postal_code, country FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$addresses = [];
while ($row = $result->fetch_assoc()) {
    $addresses[] = $row;
}

echo json_encode($addresses);
$stmt->close();
$conn->close();
?>
