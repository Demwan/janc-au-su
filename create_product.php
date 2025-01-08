<?php
// Foutmeldingen inschakelen
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
require_once 'db_connect.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verkrijg formuliergegevens
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $image_url = $conn->real_escape_string($_POST['image_url']);


    // SQL-query om het product in te voegen
    $sql = "INSERT INTO products (name, description, price, image_url) 
            VALUES ('$name', '$description', '$price', '$image_url')";

    if ($conn->query($sql) === TRUE) {
        echo "Product added successfully!";
        // Redirect naar de productlijst of hoofdpagina na succes
        header("Location: products.html");
        exit(); // Zorg ervoor dat je exit() aanroept na de header redirect
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>