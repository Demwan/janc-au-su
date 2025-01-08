<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: /login");
  exit();}

$user_id = $_SESSION['user_id'];

// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];
    $payment_provider = $_POST['payment_provider'];
    $cart_data = $_POST['cart_data'];

    $cart = json_decode($cart_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Invalid cart data");
    }

    // Check if the address already exists
    $stmt = $conn->prepare("SELECT address_id FROM addresses WHERE user_id = ? AND street = ? AND city = ? AND postal_code = ? AND country = ?");
    $stmt->bind_param("issss", $user_id, $address, $city, $postal_code, $country);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Address exists, fetch its ID
        $stmt->bind_result($address_id);
        $stmt->fetch();
    } else {
        // Address does not exist, insert a new one
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, street, city, postal_code, country) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $address, $city, $postal_code, $country);
        $stmt->execute();
        $address_id = $stmt->insert_id;
    }
    $stmt->close();

    $total_amount = 0;
    $order_items = [];

    foreach ($cart as $item) {
        $product_id = $item['productId'];
        $size = $item['size'];
        $quantity = $item['quantity'];

        // Fetch product price
        $product_query = "SELECT price FROM products WHERE id = ?";
        $product_stmt = $conn->prepare($product_query);
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product_stmt->store_result();
        $product_stmt->bind_result($price);
        $product_stmt->fetch();
        $product_stmt->close();

        if (!$price) {
            die("Product price not found for product ID: $product_id");
        }

        $total_amount += $price * $quantity;
        $order_items[] = ['product_id' => $product_id, 'quantity' => $quantity, 'price' => $price, 'size' => $size];
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, address_id, total_amount, payment_provider) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $address_id, $total_amount, $payment_provider);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    foreach ($order_items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiids", $order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart cookie
    setcookie('cart', '', time() - 3600, '/');

    // Redirect to thank you page
    header('Location: thank_you.php?order_id=' . $order_id);
    exit();

} else {
    echo "Invalid request method.";
}

$conn->close();
?>
