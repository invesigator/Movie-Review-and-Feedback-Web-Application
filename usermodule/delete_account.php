<?php
include "../components/header.php";
include "../components/database.php"; // Ensure this path is correct

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("location: login.php");
    exit;
}

// Get the database connection
$conn = getDatabaseConnection();

// Delete the user from the database
$statement = $conn->prepare("DELETE FROM users WHERE email = ?");
$statement->bind_param("s", $_SESSION["email"]);
$statement->execute();
$statement->close();

// Destroy the session to log the user out
session_destroy();

// Redirect to the home page or a goodbye message page
header("location: goodbye.php");
exit;
?>
