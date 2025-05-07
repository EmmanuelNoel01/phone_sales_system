<?php
require 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
                        <h2 class="text-danger mt-3">Access Denied</h2>
                        <p class="lead">You don't have permission to access this page.</p>
                        <a href="login.php" class="btn btn-primary mt-3">
                            <i class="bi bi-box-arrow-in-right"></i> Return to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>