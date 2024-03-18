<?php
// Add a session_start() to the top to start the session
session_start();

// Check if the user is already logged in
if(isset($_SESSION['user_id'])) {
    // If logged in, destroy the session and redirect to the login page
    session_destroy();
    header("location: signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Near Care - Sign Out</title>
    <link rel="stylesheet" href="./style/signin.css">
    <script src="https://kit.fontawesome.com/739a118a56.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <div class="form-box">
            <h1 id="title">Sign Out</h1>
            <form method="post" action="signout.php"> <!-- Use a separate PHP file for sign-out -->
                <div class="btn-field">
                    <button type="submit" id="signoutBtn" name="logout">Sign Out</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        let signoutBtn = document.getElementById("signoutBtn");
        let title = document.getElementById("title");

        signoutBtn.onclick = function () {
            title.innerHTML = "Sign Out";
        }
    </script>
</body>

</html>
