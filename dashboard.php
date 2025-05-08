<?php
// Initialize the session
session_start();

// Include configuration file
require_once "includes/config.php";
require_once "includes/auth.php";
require_once "includes/db.php";

// Redirect to login page if not logged in
if (!isLoggedIn()) {
    header("location: index.php");
    exit;
}

// Get available sites
$sites = getSites($conn);

$page_title = "Dashboard";
$extra_css = ["/assets/css/dashboard.css"];
$extra_js = ["/assets/js/dashboard.js"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - Interactive Portal" : "Interactive Portal"; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <?php if (isset($extra_css)): foreach ($extra_css as $css): ?>
    <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endforeach; endif; ?>
    <style>
        :root {
            --bg-image: url('/assets/img/default-bg.jpg');
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <header>
            <nav>
                <div class="logo">
                    <h1>Interactive Portal</h1>
                </div>
                <div class="menu">
                    <a href="/dashboard.php">Dashboard</a>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <a href="/admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="/logout.php">Logout</a>
                </div>
            </nav>
        </header>
        <main>
            <div class="dashboard-welcome">
                <h1>Willkommen, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
                <p>Wähle eine Funktion, die du besuchen möchtest:</p>
            </div>

            <?php if (!empty($sites)): ?>
            <div class="sites-container">
                <?php foreach ($sites as $site): ?>
                    <a href="<?php echo htmlspecialchars($site["url"]); ?>" class="site-card">
                        <div class="site-icon">
                            <?php if (!empty($site["icon"])): ?>
                                <img src="<?php echo htmlspecialchars($site["icon"]); ?>" alt="<?php echo htmlspecialchars($site["name"]); ?>">
                            <?php else: ?>
                                <i class="fas fa-link"></i>
                            <?php endif; ?>
                        </div>
                        <div class="site-info">
                            <h3><?php echo htmlspecialchars($site["name"]); ?></h3>
                            <p><?php echo htmlspecialchars($site["description"]); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="no-sites">
                <p>Keine Websites verfügbar. Bitte kontaktiere einen Administrator.</p>
            </div>
            <?php endif; ?>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Interactive Portal. All rights reserved.</p>
        </footer>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <?php if (isset($extra_js)): foreach ($extra_js as $js): ?>
    <script src="<?php echo $js; ?>"></script>
    <?php endforeach; endif; ?>
</body>
</html>