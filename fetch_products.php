<?php
// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the products table
$sql = "SELECT id, name, description, price, image_url FROM products";
$result = $conn->query($sql);

// Initialize an array to store product data
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close the connection
$conn->close();

// Return data as JSON
echo json_encode($data);
?>
