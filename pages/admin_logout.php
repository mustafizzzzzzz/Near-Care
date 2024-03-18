<?php
// Start the session
session_start();

// Destroy the session to log out
session_destroy();

// Redirect to the login page (adjust the path as needed)
header("Location: admin_login.php");
exit();
?>
