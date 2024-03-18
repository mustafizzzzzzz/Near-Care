<?php
session_start();

// Check if the form is submitted
if (isset($_POST['login'])) {
    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'hospital');

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get data from the form
    $email = $_POST['admin_email'];
    $password = $_POST['admin_password'];

    // Retrieve the user from the database based on the entered email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the entered password against the stored hash
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session and redirect
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            } 
            exit();
        } else {
            echo "<script>alert('Login failed: Incorrect email or password');</script>";
        }
    } else {
        echo "User not found";
    }

    // Close the database connection
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
        <form action="admin_login.php" method="POST">
            <h2>Admin Login</h2>
            <div class="input-group">
                <label for="admin_email">Admin Email:</label>
                <input type="email" id="admin_email" name="admin_email" required>
            </div>
            <div class="input-group">
                <label for="admin_password">Password:</label>
                <input type="password" id="admin_password" name="admin_password" autocomplete="off" required>
            </div>
            <button type="submit" name="login">Login</button>
            <p>Don't have an account? <a href="admin_registration.php">Register</a></p>
        </form>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let signupBtn = document.getElementById("signupBtn");
        let signinBtn = document.getElementById("signinBtn");
        let title = document.getElementById("title");

        signinBtn.onclick = function () {
            title.innerHTML = "Admin Login";
            signupBtn.classList.add("disable");
            signinBtn.classList.remove("disable");
        }

        signupBtn.onclick = function () {
            window.location.href = "signup.php";
        }
    });
</script>
</body>

</html>
