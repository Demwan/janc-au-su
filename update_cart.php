// <?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Debug raw input
$input = file_get_contents('php://input');
file_put_contents('log.txt', "Raw input: " . $input . "\n", FILE_APPEND);

// Decode JSON payload
$data = json_decode($input, true);

// Validate JSON structure
if (!isset($data['cart']) || !is_array($data['cart'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON format']);
    exit;
}

// Connect to database
$host = "sql106.infinityfree.com";
$username = "if0_37896700";
$password = "oNHqXJXQLpI2s";
$dbname = "if0_37896700_webshop";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Assume user is logged in and their ID is available
// Replace with your session or authentication logic
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Convert the cart array to JSON
$cartJson = json_encode($data['cart']);

// Update the user's cart in the database
$query = "UPDATE users SET cart = ? WHERE user_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param('si', $cartJson, $userId);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to execute statement: ' . $stmt->error]);
    exit;
}

// Close the database connection
$stmt->close();
$conn->close();

// Success response
echo json_encode(['success' => true]);
?>
