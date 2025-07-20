<?php
require '../includes/config.php';
require '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../errors/403.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php?error=Invalid user ID");
    exit();
}

$user_id = intval($_GET['id']);

// Prevent deleting your own account
if ($user_id == $_SESSION['user_id']) {
    header("Location: manage_users.php?error=You cannot delete your own account");
    exit();
}

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        header("Location: manage_users.php?success=User deleted successfully");
    } else {
        header("Location: manage_users.php?error=Delete failed: " . urlencode($stmt->error));
    }
    $stmt->close();
} else {
    header("Location: manage_users.php?error=Failed to prepare delete statement");
}
$conn->close();
?>
