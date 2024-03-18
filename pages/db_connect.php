<?php
$hostname = 'localhost'; // Replace with your database host
$username = 'root';      // Replace with your database username
$password = '';      // Replace with your database password
$database = 'hospital'; // Replace with your database name

// Create a new MySQLi instance
$conn = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the connection is successful, you can use the $conn object to query the database
// For example: $conn->query("SELECT * FROM your_table");

// Remember to close the database connection when you're done
// $conn->close();
?>
