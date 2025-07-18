<?php
require '../includes/config.php';
require '../includes/auth.php';

// Only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = '';
$user_id = $_GET['id'] ?? 0;
// Allow admin to edit any user, but users can only edit themselves
if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $user_id) {
    header("Location: ../errors/403.php");
    exit();
}
// Fetch user data
$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $change_password = isset($_POST['change_password']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (!in_array($role, ['admin', 'staff'])) $errors[] = "Invalid role selected";
    
    if ($change_password) {
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
        if ($password !== $confirm_password) $errors[] = "Passwords don't match";
    }
    
    // Check if email exists (excluding current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already in use by another account";
    }
    $stmt->close();
    
    // If no errors, update user
    if (empty($errors)) {
        if ($change_password) {
            $hashed_password = hashPassword($password);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $role, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $role, $user_id);
        }
        
        if ($stmt->execute()) {
            $success = "User updated successfully!";
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        } else {
            $errors[] = "Update failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; }
        .form-control:focus { box-shadow: none; border-color: #80bdff; }
    </style>
</head>
<body>
 <?php require '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Edit User: <?= htmlspecialchars($user['name']) ?></h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="change_password" id="changePassword">
                                    <label class="form-check-label" for="changePassword">Change Password</label>
                                </div>
                                
                                <div id="passwordFields" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" name="confirm_password" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="manage_users.php" class="btn btn-outline-secondary">Back to Users</a>
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide password fields when checkbox is clicked
        document.getElementById('changePassword').addEventListener('change', function() {
            const passwordFields = document.getElementById('passwordFields');
            passwordFields.style.display = this.checked ? 'block' : 'none';
            
            // Make password fields required if checkbox is checked
            const passwordInputs = passwordFields.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.required = this.checked;
            });
        });
    </script>
</body>
</html>