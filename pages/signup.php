<?php
$conn = new mysqli('localhost', 'root', '', 'hospital');

if (!$conn) {
    echo "Not connected";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Near Care - signup</title>
    <link rel="stylesheet" href="./style/signup.css">
    <script src="https://kit.fontawesome.com/739a118a56.js" crossorigin="anonymous"></script>
</head>

<body>
    <div>
        <?php
        if (isset($_POST['create'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Hash the password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                header('location:signin.php?create');
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        ?>
    </div>

    <div class="container">
        <img src="../images/log.png" >
        <div class="form-box">
            <h1 id="title">Sign Up</h1>
            <form action="signup.php" method="post">
                <div class="input-group">
                    <div class="input-field" id="namefield">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" placeholder="Name" name="name" required>
                    </div>
                    <div class="input-field">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" placeholder="Email" name="email" required>
                    </div>
                    <div class="input-field">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Password" name="password" autocomplete="off" required>
                    </div>
                    <p>Already have an account?<a href="signin.php">Sign In</a></p>
                </div>
                <div class="btn-field">
                    <button type="submit" id="signupBtn" name="create">Sign Up</button>
                    <button type="button" id="signinBtn" class="disable">Sign In</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        let signupBtn = document.getElementById("signupBtn");
        let signinBtn = document.getElementById("signinBtn");
        let namefield = document.getElementById("namefield");
        let title = document.getElementById("title");

        signupBtn.onclick = function () {
            namefield.style.maxHeight = "60px";
            title.innerHTML = "Sign Up";
            signupBtn.classList.remove("disable");
            signinBtn.classList.add("disable");
        }

        signinBtn.onclick = function () {
            window.location.href = "signin.php";
        }

        
    </script>
</body>
</html>
