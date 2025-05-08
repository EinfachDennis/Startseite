<?php
// Initialize the session
session_start();

// Include configuration file
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_once "../includes/db.php";

// Redirect to login page if not logged in or not admin
requireAdmin();

// Process form submissions
$message = $error = "";

// Update background image
if (isset($_POST["update_background"])) {
    // Process background image if uploaded
    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/backgrounds/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES["background_image"]["name"]);
        $fileName = "background_" . uniqid() . '.' . $fileInfo['extension'];
        $target_file = $target_dir . $fileName;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["background_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["background_image"]["tmp_name"], $target_file)) {
                $background_path = "/uploads/backgrounds/" . $fileName;
                
                if (updateSetting($conn, "background_image", $background_path)) {
                    $message = "Background image updated successfully.";
                } else {
                    $error = "Error updating background image setting.";
                }
            } else {
                $error = "Error uploading background image.";
            }
        } else {
            $error = "File is not an image.";
        }
    } else {
        $error = "Please select an image file.";
    }
}

// Reset background image to default
if (isset($_POST["reset_background"])) {
    if (updateSetting($conn, "background_image", DEFAULT_BACKGROUND)) {
        $message = "Background image reset to default.";
    } else {
        $error = "Error resetting background image.";
    }
}

// Get current background image
$current_background = getBackgroundImage($conn);

$page_title = "System Settings";
$extra_css = ["/assets/css/admin.css"];
$extra_js = ["/assets/js/admin.js"];
include "../includes/header.php";
?>

<div class="admin-header">
    <h1>System Settings</h1>
    <a href="/admin.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Admin</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Background Settings -->
<div class="admin-card">
    <div class="card-header">
        <h2>Background Image</h2>
    </div>
    <div class="card-body">
        <div class="current-background">
            <h3>Current Background</h3>
            <img src="<?php echo $current_background; ?>" alt="Current Background" class="background-preview">
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="admin-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="background_image">Upload New Background Image</label>
                <input type="file" name="background_image" id="background_image" class="form-control-file" accept="image/*" required>
                <small class="form-text">Recommended size: 1920x1080 pixels or larger. JPG or PNG format.</small>
            </div>
            
            <div class="form-group">
                <button type="submit" name="update_background" class="btn btn-primary">Update Background</button>
                <button type="submit" name="reset_background" class="btn btn-warning">Reset to Default</button>
            </div>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>