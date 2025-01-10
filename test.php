<?php
// Database credentials
$servername = "janc-au-su-jancausu.d.aivencloud.com"; // Replace with your database server name
$username = "avnadmin";       // Replace with your database username
$password = "AVNS_OEvCMARrGprxYNdVy94";           // Replace with your database password
$dbname = "defaultdb";       // Replace with your database name (optional)

// Attempt to connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "true"; // Connection successful
}

// Close the connection (optional, since script ends immediately)
$conn->close();
?>
