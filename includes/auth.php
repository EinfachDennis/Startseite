<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

// Function to check if user is admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION["role"]) && $_SESSION["role"] === ROLE_ADMIN;
}

// Function to redirect user to login page if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        // Store the requested URL for redirecting after login
        $_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
        header("location: /index.php");
        exit;
    }
}

// Function to redirect user to dashboard if already logged in
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("location: /dashboard.php");
        exit;
    }
}

// Function to require admin privileges
function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        header("location: /dashboard.php");
        exit;
    }
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to get current background image
function getBackgroundImage($conn) {
    $sql = "SELECT setting_value FROM settings WHERE setting_name = 'background_image' LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["setting_value"];
    } else {
        return DEFAULT_BACKGROUND;
    }
}
?>