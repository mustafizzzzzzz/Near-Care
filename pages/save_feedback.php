<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the feedback from the POST request
    $feedback = $_POST['feedback'] ?? '';

    if (!empty($feedback)) {
        // Connect to your database
        $servername = 'localhost'; // Replace with your database host
        $username = 'root'; // Replace with your database username
        $password = ''; // Replace with your database password
        $dbname = 'hospital'; // Replace with your database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Prepare and execute SQL to insert feedback into the table
        $stmt = $conn->prepare('INSERT INTO user_feedback (feedback_text) VALUES (?)');
        $stmt->bind_param('s', $feedback);
        $stmt->execute();

        // Close connection
        $stmt->close();
        $conn->close();

        // Redirect to a success page or perform other actions
        header('Location: index.php');
        exit();
    }
}
?>
