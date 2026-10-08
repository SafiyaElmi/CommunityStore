<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Community Store</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>

<div class="auth-split">
    <div class="auth-form">
        <div class="auth-top-row">
            <a href="../frontend/index.php" class="back-arrow" aria-label="Back to home">
                <i class="bi bi-arrow-left"></i>
            </a>
            <a href="../frontend/index.php" class="brand">
                <i class="bi bi-shop-window"></i>
                Community Store
            </a>
        </div>

        <h2>Welcome back</h2>
        <p class="sub">Login to your Community Store account.</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger auto-dismiss">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success auto-dismiss">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="processLogin.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="userType">Login as</label>
                <select id="userType" name="user_type" class="form-select" required>
                    <option value="">Select account type</option>
                    <option value="Student">Student</option>
                    <option value="Vendor">Vendor</option>
                    <option value="Resident">Resident</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="email">Email</label>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input id="email" type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" class="btn-purple">Login</button>
        </form>

        <p class="text-center mt-4 mb-0 small muted">
            New here?
            <a href="register.php" class="text-purple fw-bold">Create an account</a>
        </p>
    </div>

    <div class="auth-art">
        <div class="art-copy">
            <h3>Buy, sell and connect<br>within your community.</h3>
            <p>A trusted marketplace for campus communities.</p>
        </div>
        <img class="art-img" src=../images/Electronics.jpg alt="">
    </div>
</div>

<script src="../assets/script.js"></script>
</body>
</html>
