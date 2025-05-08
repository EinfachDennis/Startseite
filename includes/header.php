<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
require_once "auth.php";

// Get current background image
$background_image = getBackgroundImage($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - " . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <?php if (isset($extra_css)): foreach ($extra_css as $css): ?>
    <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endforeach; endif; ?>
    <style>
        :root {
            --bg-image: url('<?php echo $background_image; ?>');
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <header>
            <?php if (isLoggedIn()): ?>
            <nav>
                <div class="logo">
                    <h1><?php echo SITE_NAME; ?></h1>
                </div>
                <div class="menu">
                    <a href="/dashboard.php">Dashboard</a>
                    <?php if (isAdmin()): ?>
                    <a href="/admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="/logout.php">Logout</a>
                </div>
            </nav>
            <?php endif; ?>
        </header>
        <main>