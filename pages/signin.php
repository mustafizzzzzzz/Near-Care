<?php
$conn = new mysqli('localhost', 'root', '', 'hospital');

if (!$conn) {
    echo "Not connected";
} else {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!empty($email) && !empty($password)) {
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $query = $conn->query($sql);

            if ($query) {
                if ($query->num_rows > 0) {
                    $user = $query->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        // Start the session
                        session_start();

                        // Set the user_id session variable
                        $_SESSION['user_id'] = $user['id'];

                        echo "<script>alert('Login successful'); window.location.href = 'http://localhost/NEARCARE/pages/index.php?login';</script>";
                        exit();
                    } else {
                        echo "<script>alert('Login failed: Incorrect email or password');</script>";
                    }
                } else {
                    echo "<script>alert('Login failed: User not found');</script>";
                }
            } else {
                echo "<script>alert('Login failed: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Login failed: Empty email or password');</script>";
        }
    }

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style/signin.css">
    <script src="https://kit.fontawesome.com/739a118a56.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <img src="../images/log.png" >
        <div class="form-box">
            <h1 id="title">Sign In</h1>
            <form method="post" action="signin.php">
                <div class="input-group">
                    <div class="input-field">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" placeholder="Email" name="email" required>
                    </div>
                    <div class="input-field">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Password" name="password" autocomplete="off" required>
                    </div>
                    <!-- <p>Forgotten Password? <a href="forgot_password.php">Click Here</a></p> -->
                </div>
                <p id="admin">As an admin? <a href="admin_login.php">Admin Sign In</a></p>
                <div class="btn-field">
                    <button type="submit" id="signinBtn" name="login">Sign In</button>
                    <button type="button" id="signupBtn" class="disable">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        let signupBtn = document.getElementById("signupBtn");
        let signinBtn = document.getElementById("signinBtn");
        let title = document.getElementById("title");

        signinBtn.onclick = function () {
            title.innerHTML = "Sign In";
            signupBtn.classList.add("disable");
            signinBtn.classList.remove("disable");
        }

        signupBtn.onclick = function () {
            window.location.href = "signup.php";
        }
    </script>
</body>

</html>
