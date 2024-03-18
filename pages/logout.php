<?php
// Start the session
session_start();

// Unset the user_id session variable
unset($_SESSION['user_id']);

// Destroy the session
session_destroy();

// Redirect to the home page or any other page after logout
header("Location: index.php");
exit();
?>
