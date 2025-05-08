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

// Delete site
if (isset($_POST["delete_site"]) && !empty($_POST["site_id"])) {
    $site_id = $_POST["site_id"];
    
    if (deleteSite($conn, $site_id)) {
        $message = "Site deleted successfully.";
    } else {
        $error = "Error deleting site.";
    }
}

// Add new site
if (isset($_POST["add_site"])) {
    $name = sanitizeInput($_POST["name"]);
    $url = sanitizeInput($_POST["url"]);
    $description = sanitizeInput($_POST["description"]);
    
    // Make sure URL starts with /
    if (substr($url, 0, 1) !== '/') {
        $url = '/' . $url;
    }
    
    // Make sure URL ends with /
    if (substr($url, -1) !== '/') {
        $url .= '/';
    }
    
    // Process icon if uploaded
    $icon = null;
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/icons/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES["icon"]["name"]);
        $fileName = uniqid() . '.' . $fileInfo['extension'];
        $target_file = $target_dir . $fileName;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["icon"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file)) {
                $icon = "/uploads/icons/" . $fileName;
            } else {
                $error = "Error uploading icon.";
            }
        } else {
            $error = "File is not an image.";
        }
    }
    
    // Validate input
    if (empty($name) || empty($url)) {
        $error = "Please fill all required fields.";
    } else {
        // Check if URL already exists
        $check_sql = "SELECT id FROM sites WHERE url = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $url);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Site URL already exists.";
        } else {
            if (addSite($conn, $name, $url, $description, $icon)) {
                $message = "Site added successfully.";
            } else {
                $error = "Error adding site.";
            }
        }
    }
}

// Edit site
if (isset($_POST["edit_site"])) {
    $edit_id = $_POST["edit_id"];
    $edit_name = sanitizeInput($_POST["edit_name"]);
    $edit_url = sanitizeInput($_POST["edit_url"]);
    $edit_description = sanitizeInput($_POST["edit_description"]);
    
    // Make sure URL starts with /
    if (substr($edit_url, 0, 1) !== '/') {
        $edit_url = '/' . $edit_url;
    }
    
    // Make sure URL ends with /
    if (substr($edit_url, -1) !== '/') {
        $edit_url .= '/';
    }
    
    // Process icon if uploaded
    $edit_icon = null;
    if (isset($_FILES['edit_icon']) && $_FILES['edit_icon']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/icons/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES["edit_icon"]["name"]);
        $fileName = uniqid() . '.' . $fileInfo['extension'];
        $target_file = $target_dir . $fileName;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["edit_icon"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["edit_icon"]["tmp_name"], $target_file)) {
                $edit_icon = "/uploads/icons/" . $fileName;
            } else {
                $error = "Error uploading icon.";
            }
        } else {
            $error = "File is not an image.";
        }
    }
    
    // Validate input
    if (empty($edit_name) || empty($edit_url)) {
        $error = "Please fill all required fields.";
    } else {
        // Check if URL already exists (for other sites)
        $check_sql = "SELECT id FROM sites WHERE url = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $edit_url, $edit_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Site URL already exists.";
        } else {
            if (updateSite($conn, $edit_id, $edit_name, $edit_url, $edit_description, $edit_icon)) {
                $message = "Site updated successfully.";
            } else {
                $error = "Error updating site.";
            }
        }
    }
}

// Get all sites
$sites = getSites($conn);

$page_title = "Manage Sites";
$extra_css = ["/assets/css/admin.css"];
$extra_js = ["/assets/js/admin.js"];
include "../includes/header.php";
?>

<div class="admin-header">
    <h1>Website Management</h1>
    <a href="/admin.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Admin</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Add Site Form -->
<div class="admin-card">
    <div class="card-header">
        <h2>Add New Website</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="admin-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Site Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="url">Site URL (relative path)</label>
                <input type="text" name="url" id="url" class="form-control" required placeholder="/example/">
                <small class="form-text">Path to the site directory, e.g. /millionaer/</small>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="icon">Icon (optional)</label>
                <input type="file" name="icon" id="icon" class="form-control-file" accept="image/*">
            </div>
            
            <div class="form-group">
                <button type="submit" name="add_site" class="btn btn-primary">Add Website</button>
            </div>
        </form>
    </div>
</div>

<!-- Site List -->
<div class="admin-card">
    <div class="card-header">
        <h2>Website List</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($sites)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sites as $site): ?>
                            <tr>
                                <td><?php echo $site["id"]; ?></td>
                                <td>
                                    <?php if (!empty($site["icon"])): ?>
                                        <img src="<?php echo htmlspecialchars($site["icon"]); ?>" alt="Icon" class="site-icon-small">
                                    <?php else: ?>
                                        <i class="fas fa-link site-icon-small"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($site["name"]); ?></td>
                                <td><?php echo htmlspecialchars($site["url"]); ?></td>
                                <td><?php echo htmlspecialchars($site["description"]); ?></td>
                                <td class="actions">
                                    <button class="btn btn-sm btn-edit" onclick="editSite(
                                        <?php echo $site['id']; ?>, 
                                        '<?php echo htmlspecialchars($site['name']); ?>', 
                                        '<?php echo htmlspecialchars($site['url']); ?>', 
                                        '<?php echo htmlspecialchars($site['description']); ?>'
                                    )">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    <form method="post" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this site?');">
                                        <input type="hidden" name="site_id" value="<?php echo $site["id"]; ?>">
                                        <button type="submit" name="delete_site" class="btn btn-sm btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No sites found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Site Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Website</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="admin-form" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" id="edit_id">
            
            <div class="form-group">
                <label for="edit_name">Site Name</label>
                <input type="text" name="edit_name" id="edit_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit_url">Site URL (relative path)</label>
                <input type="text" name="edit_url" id="edit_url" class="form-control" required>
                <small class="form-text">Path to the site directory, e.g. /millionaer/</small>
            </div>
            
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea name="edit_description" id="edit_description" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_icon">Icon (optional, leave blank to keep current)</label>
                <input type="file" name="edit_icon" id="edit_icon" class="form-control-file" accept="image/*">
            </div>
            
            <div class="form-group">
                <button type="submit" name="edit_site" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// Get the modal
var modal = document.getElementById("editModal");
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Function to open edit modal with site data
function editSite(id, name, url, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_url').value = url;
    document.getElementById('edit_description').value = description;
    
    modal.style.display = "block";
}
</script>

<?php include "../includes/footer.php"; ?>