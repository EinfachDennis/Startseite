<?php
// Get all users
function getUsers($conn) {
    $sql = "SELECT id, username, role, created_at FROM users ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $users = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Get user by ID
function getUserById($conn, $id) {
    $sql = "SELECT id, username, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

// Add new user
function addUser($conn, $username, $password, $role) {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $role);
    
    return $stmt->execute();
}

// Update user
function updateUser($conn, $id, $username, $role, $password = null) {
    if (!empty($password)) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $role, $hashed_password, $id);
    } else {
        // Update without changing password
        $sql = "UPDATE users SET username = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $role, $id);
    }
    
    return $stmt->execute();
}

// Delete user
function deleteUser($conn, $id) {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

// Get all sites
function getSites($conn) {
    $sql = "SELECT * FROM sites ORDER BY name ASC";
    $result = $conn->query($sql);
    
    $sites = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sites[] = $row;
        }
    }
    
    return $sites;
}

// Weitere Funktionen (getSiteById, addSite, updateSite, deleteSite, updateSetting, getSetting)...
?>