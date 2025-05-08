<?php
// Initialize the session
session_start();

// Include configuration file
require_once "includes/config.php";
require_once "includes/auth.php";

// Redirect to login page if not logged in or not admin
requireAdmin();

$page_title = "Admin Dashboard";
$extra_css = ["/assets/css/admin.css"];
$extra_js = ["/assets/js/admin.js"];
include "includes/header.php";
?>

<div class="admin-header">
    <h1>Admin Dashboard</h1>
    <p>Manage your website content and users</p>
</div>

<div class="admin-panels">
    <div class="admin-panel">
        <div class="panel-header">
            <i class="fas fa-users"></i>
            <h2>User Management</h2>
        </div>
        <div class="panel-body">
            <p>Add, edit, or delete users. Assign admin privileges.</p>
            <a href="/admin/manage_users.php" class="btn btn-primary">Manage Users</a>
        </div>
    </div>
    
    <div class="admin-panel">
        <div class="panel-header">
            <i class="fas fa-globe"></i>
            <h2>Website Management</h2>
        </div>
        <div class="panel-body">
            <p>Add or remove websites that users can access from the dashboard.</p>
            <a href="/admin/manage_sites.php" class="btn btn-primary">Manage Sites</a>
        </div>
    </div>
    
    <div class="admin-panel">
        <div class="panel-header">
            <i class="fas fa-cog"></i>
            <h2>Settings</h2>
        </div>
        <div class="panel-body">
            <p>Update system settings, background images, and more.</p>
            <a href="/admin/settings.php" class="btn btn-primary">Manage Settings</a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>