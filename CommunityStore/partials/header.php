<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);
$isAuth = in_array($current, ['login.php', 'register.php'], true);
$home = isset($_SESSION['userID']) ? 'home.php' : 'index.php';
$userType = $_SESSION['userType'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' · Community Store' : 'Community Store' ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body data-user-type="<?= htmlspecialchars($userType) ?>">

<?php if (!$isAuth): ?>

<nav class="top-nav">
    <div class="top-nav-inner">
        <a href="<?= htmlspecialchars($home) ?>" class="brand">
            <i class="bi bi-shop-window"></i>
            Community Store
        </a>

        <div class="nav-links">
            <a href="<?= htmlspecialchars($home) ?>" class="<?= in_array($current, ['index.php', 'home.php'], true) ? 'active' : '' ?>">
                Home
            </a>

            <?php if (isset($_SESSION['userID'])): ?>
                <a href="bulletin.php" class="<?= $current === 'bulletin.php' ? 'active' : '' ?>">
                    Bulletin
                </a>

                <a href="notifications.php" class="<?= $current === 'notifications.php' ? 'active' : '' ?>">
                    Notifications
                </a>

                <a href="create_listing.php"
                   class="role-function <?= $current === 'create_listing.php' ? 'active' : '' ?>"
                   data-roles="Student,Resident,Vendor">
                    Sell Item
                </a>

                <a href="profile.php" class="<?= $current === 'profile.php' ? 'active' : '' ?>">
                    Profile
                </a>
            <?php endif; ?>
        </div>

        <form class="nav-search" action="<?= htmlspecialchars($home) ?>" method="GET">
            <i class="bi bi-search"></i>
            <input
                type="search"
                name="search"
                placeholder="Search products..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            >
        </form>

        <div class="nav-icons">
            <?php if (isset($_SESSION['userID'])): ?>
                <a href="cart.php" class="icon-btn" title="Cart">
                    <i class="bi bi-cart3"></i>
                </a>

                <a href="profile.php" class="icon-btn dark" title="Profile">
                    <i class="bi bi-person"></i>
                </a>
            <?php else: ?>
                <a href="../authentication/register.php" class="btn-purple nav-login">
                    Register
                </a>
                <a href="../authentication/login.php" class="btn-purple nav-login">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="app-shell">

<?php endif; ?>
