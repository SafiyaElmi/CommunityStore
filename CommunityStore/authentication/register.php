<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account · Community Store</title>

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

        <h2>Create your account</h2>
        <p class="sub">Choose the account type you will use on Community Store.</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger auto-dismiss">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="processRegister.php" method="POST">
            <div class="mb-3">
                <label for="firstName">First Name</label>
                <input id="firstName" type="text" name="first_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="lastName">Last Name</label>
                <input id="lastName" type="text" name="last_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="registerEmail">Email</label>
                <input id="registerEmail" type="email" name="email" class="form-control" placeholder="name@cput.ac.za" required>
            </div>

            <div class="mb-3">
                <label for="registerType">Account Type</label>
                <select id="registerType" name="user_type" class="form-select" required>
                    <option value="Student">Student</option>
                    <option value="Vendor">Vendor</option>
                    <option value="Resident">Resident</option>
                </select>
                <small class="form-help">Students and residents can also sell items.</small>
            </div>

            <div class="mb-3">
                <label for="registerPassword">Password</label>
                <input id="registerPassword" type="password" name="password" class="form-control" minlength="6" required>
            </div>

            <button type="submit" class="btn-purple">Create Account</button>
        </form>

        <p class="text-center mt-4 mb-0 small muted">
            Already have an account?
            <a href="login.php" class="text-purple fw-bold">Login</a>
        </p>
    </div>

    <div class="auth-art">
        <div class="art-copy">
            <h3>Buy, sell and connect<br>within your community.</h3>
            <p>A trusted marketplace for campus communities.</p>
        </div>
        <img class="art-img" src=../images/Market.jpg alt="">
    </div>
</div>

<script src="../assets/script.js"></script>
</body>
</html>
