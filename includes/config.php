<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$dbname = "phone_sales_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Create tables if not exists
$conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(100) NOT NULL,
        role ENUM('admin', 'staff') DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS phones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        brand VARCHAR(50) NOT NULL,
        model VARCHAR(50) NOT NULL,
        imei VARCHAR(20) UNIQUE NOT NULL,
        storage VARCHAR(20) NOT NULL,
        color VARCHAR(30) NOT NULL,
        `condition` ENUM('New', 'Refurbished', 'Used') NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        quantity INT DEFAULT 1,
        added_by INT,
        FOREIGN KEY (added_by) REFERENCES users(id),
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Insert admin user with plain text password
$conn->query("
    INSERT IGNORE INTO users (name, email, password, role)
    VALUES ('Admin', 'admin@phonesales.com', 'admin123', 'admin')
");
?>