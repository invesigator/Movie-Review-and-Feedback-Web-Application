<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Check if the "Remember Me" cookie exists and delete it
if (isset($_COOKIE['remember_me'])) {
    // Set the cookie expiration to a past time to delete it
    setcookie('remember_me', '', time() - 3600, '/');
}

// Redirect to the home page
header("location: ../index.php");
exit;
?>
