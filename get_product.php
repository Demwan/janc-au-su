<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "Invalid product ID"]);
    exit();
}
$product_id = intval($_GET['id']);

// Query the database to fetch product details, including the available sizes
$sql = "SELECT id, name, description, price, image_url, available_sizes FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Database query failed: " . $conn->error]);
    exit();
}

// Return the product or an error if not found
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    // Extract available sizes from the available_sizes column (which is a comma-separated string)
    $sizes = explode(',', $product['available_sizes']);

    // Return the product details, including the sizes
    $product['sizes'] = $sizes;

    echo json_encode($product);
} else {
    echo json_encode(["error" => "Product not found"]);
}

$conn->close();
?>
