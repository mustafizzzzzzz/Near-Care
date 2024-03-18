<?php
// Establish database connection

if (isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];

    // Validate token and update password
    $sql = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $hashed_password, $token);
    $stmt->execute();

    // Check if the password was updated successfully
    if ($stmt->affected_rows > 0) {
        // Password updated, redirect to login page or confirmation page
        header("Location: signup.php");
        exit();
    } else {
        echo "Password update failed. Invalid token or user.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Your CSS and other meta tags -->
    <style>
        /* Basic CSS for the forgot_password.php page */

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f2f2f2;
}

.container {
    width: 80%;
    max-width: 500px;
    margin: 50px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: rgb(75, 122, 242);
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 10px;
}

input[type="email"],
button {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

button {
    background-color: #4caf50;
    color: #fff;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}

    </style>
</head>
<body>
<div class="container">
        <h1>Reset Password</h1>
        <form method="post" action="update_password.php">
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <label for="password">Enter new password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    </div>
</body>
</html>
