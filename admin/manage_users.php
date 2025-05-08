<?php
// Initialize the session
session_start();

// Include configuration file
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_once "../includes/db.php";

// Redirect to login page if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    header("location: /index.php");
    exit;
}

// Process form submissions
$message = $error = "";

// Add User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);
    
    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        // Add user
        if (addUser($conn, $username, $password, $role)) {
            $message = "User added successfully.";
        } else {
            $error = "Error adding user.";
        }
    }
}

// Edit User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_user"])) {
    $id = $_POST["edit_id"];
    $username = trim($_POST["edit_username"]);
    $role = trim($_POST["edit_role"]);
    $password = trim($_POST["edit_password"]);
    
    // Check if username already exists (except for this user)
    $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        // Update user
        if (updateUser($conn, $id, $username, $role, $password)) {
            $message = "User updated successfully.";
        } else {
            $error = "Error updating user.";
        }
    }
}

// Delete User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user"])) {
    $id = $_POST["user_id"];
    
    // Prevent deleting your own account
    if ($id == $_SESSION["id"]) {
        $error = "You cannot delete your own account.";
    } else {
        // Delete user
        if (deleteUser($conn, $id)) {
            $message = "User deleted successfully.";
        } else {
            $error = "Error deleting user.";
        }
    }
}

// Get all users
$users = getUsers($conn);

$page_title = "Manage Users";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - Interactive Portal" : "Interactive Portal"; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
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
                    <a href="/admin.php">Admin</a>
                    <a href="/logout.php">Logout</a>
                </div>
            </nav>
        </header>
        <main>
            <div class="admin-header">
                <h1>User Management</h1>
                <a href="/admin.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Admin</a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Add User Form -->
            <div class="admin-card">
                <div class="card-header">
                    <h2>Add New User</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="admin-form">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User List -->
            <div class="admin-card">
                <div class="card-header">
                    <h2>User List</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($users)): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user["id"]; ?></td>
                                            <td><?php echo htmlspecialchars($user["username"]); ?></td>
                                            <td><span class="badge badge-<?php echo $user["role"] === 'admin' ? 'admin' : 'user'; ?>"><?php echo ucfirst($user["role"]); ?></span></td>
                                            <td><?php echo date("M j, Y", strtotime($user["created_at"])); ?></td>
                                            <td class="actions">
                                                <button class="btn btn-sm btn-edit" onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                
                                                <?php if ($user["id"] != $_SESSION["id"]): // Prevent deleting your own account ?>
                                                <form method="post" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-sm btn-delete">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No users found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Edit User</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="admin-form">
                        <input type="hidden" name="edit_id" id="edit_id">
                        
                        <div class="form-group">
                            <label for="edit_username">Username</label>
                            <input type="text" name="edit_username" id="edit_username" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_role">Role</label>
                            <select name="edit_role" id="edit_role" class="form-control">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_password">New Password (leave blank to keep current)</label>
                            <input type="password" name="edit_password" id="edit_password" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Interactive Portal. All rights reserved.</p>
        </footer>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="/assets/js/main.js"></script>
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

        // Function to open edit modal with user data
        function editUser(id, username, role) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_password').value = '';
            
            modal.style.display = "block";
        }
    </script>
</body>
</html>