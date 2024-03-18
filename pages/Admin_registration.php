<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    // Connect to the database (adjust the parameters accordingly)
    $conn = new mysqli('localhost', 'root', '', 'hospital');

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get data from the form
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);

    // Set the default role to 'admin' in the database
    $role = 'admin';

    // Use prepared statements to prevent SQL injection
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $admin_name, $admin_email, $admin_password, $role);

    if ($stmt->execute()) {
        echo "Admin registered successfully";

        $stmt->close();
        $conn->close();

        // Redirect to admin login page
        header('Location: admin_login.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 80%;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }

        .container h2 {
            color: #3498db;
            font-size: 30px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            color: #3498db;
            font-weight: bold;
            font-size: 18px;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #e74c3c;
            color: #ffffff;
            font-size: 18px;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c0392b;
        }

        p {
            font-size: 16px;
            text-align: center;
        }

        a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="admin_registration.php" method="POST">
            <h2>Admin Registration</h2>
            <div class="input-group">
                <label for="admin_name">Admin Name:</label>
                <input type="text" id="admin_name" name="admin_name" required>
            </div>
            <div class="input-group">
                <label for="admin_email">Admin Email:</label>
                <input type="email" id="admin_email" name="admin_email" required>
            </div>
            <div class="input-group">
                <label for="admin_password">Password:</label>
                <input type="password" id="admin_password" name="admin_password" required>
            </div>
            <button type="submit" name="register">Register</button>
            <p>Already have an account? <a href="admin_login.php">Login</a></p>
        </form>
    </div>
</body>
</html>

