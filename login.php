<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (verifyPassword($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            header("Location: " . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'staff/dashboard.php'));
            exit();
        }
    }

    $error = "Invalid email or password";
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Gadget Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
        background-image: url('/phone_sales_system/assets/img/bg.jpg'); 
        background-size: cover; 
        background-position: center;
        background-repeat: no-repeat; 
        color: white;
        height: 100vh;
        margin: 0;
    }
    .card {
        border-radius: 10px;
        background-color: rgba(3, 36, 107, 0.8); 
    }
    .form-control:focus { box-shadow: none; border-color: #80bdff; }
    .login-logo {
        display: block;
        margin: 20px auto;
        width: 150px;
        height: auto;
        border-radius: 10px;
    }

    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <img src="/phone_sales_system/assets/img/logo.jpg" alt="Gadget Store Logo" class="login-logo">

                    <div class="card-body p-4">
                        <h2 class="text-center mb-4 text-white">LogIn</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label class="form-label text-white">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-white">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                        </form>

                        <!-- <div class="text-center mt-3">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                            <p><a href="forgot_password.php">Forgot password?</a></p>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
