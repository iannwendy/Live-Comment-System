<?php
// Database configuration
$host = 'localhost';
$dbname = 'livecmt_db';
$username = 'root';
$password = '';

// Create database connection
try {
    // Connect to the database (assuming it already exists)
    // Database should be created manually using db.sql file
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
