<?php
function getDatabaseConnection1() {
    $servername = "localhost";
    $username = "root";  // Your database username
    $password = "";      // Your database password
    $database = "umovie_db";

    try {
        // Create a new PDO instance and set error mode to exception
        $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
