<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'logusersites'); // Change to your database username
define('DB_PASSWORD', '01621616236Rsk!#!'); // Change to your database password
define('DB_NAME', 'login_system');

// Site configuration
define('SITE_NAME', 'Interactive Portal');
define('DEFAULT_BACKGROUND', '/assets/img/default-bg.jpg');

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db(DB_NAME);

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating users table: " . $conn->error);
}

// Create sites table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS sites (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(255) DEFAULT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating sites table: " . $conn->error);
}

// Create settings table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating settings table: " . $conn->error);
}

// Insert default admin user if none exists
$check_admin = "SELECT * FROM users WHERE role = 'admin' LIMIT 1";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    // Create a default admin user (username: admin, password: admin123)
    $default_username = 'admin';
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, role) VALUES ('$default_username', '$default_password', 'admin')";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating default admin user: " . $conn->error);
    }
    
    echo "<script>console.log('Default admin user created: admin/admin123');</script>";
}

// Insert millionaer site if it doesn't exist
$check_site = "SELECT * FROM sites WHERE url = '/millionaer/' LIMIT 1";
$result = $conn->query($check_site);

if ($result->num_rows == 0) {
    $sql = "INSERT INTO sites (name, url, description) VALUES ('Wer wird Millionär', '/millionaer/', 'Millionär Quiz Game')";
    if ($conn->query($sql) !== TRUE) {
        die("Error adding millionaer site: " . $conn->error);
    }
}

// Insert default background setting if it doesn't exist
$check_bg = "SELECT * FROM settings WHERE setting_name = 'background_image' LIMIT 1";
$result = $conn->query($check_bg);

if ($result->num_rows == 0) {
    $sql = "INSERT INTO settings (setting_name, setting_value) VALUES ('background_image', '" . DEFAULT_BACKGROUND . "')";
    if ($conn->query($sql) !== TRUE) {
        die("Error setting default background: " . $conn->error);
    }
}
?>