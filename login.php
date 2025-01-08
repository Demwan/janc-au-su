<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Start session
session_start();

// Connect to database
require_once 'db_connect.php';

try {
    // Establish a connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to find the user by email
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);

    try {
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and passwords match
        if ($user && password_verify($password, $user['password_hash'])) {
            // Start session and store user data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];

            echo "Login successful! Redirecting...";
            // Redirect to a dashboard or homepage
            header("Location: dashboard.php");
            exit();
        } else {
            die("Invalid email or password.");
        }
    } catch (PDOException $e) {
        die("An error occurred: " . $e->getMessage());
    }
} else {
    echo "Invalid request.";
}
?>
