<?php
function getDatabaseConnection2() {
    $servername = "localhost";
    $username = "root";
    $password = "";  // Assuming no password for local development
    $database = "umovie_db";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
