<?php

$isAuth = in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php'], true);

if (!$isAuth):
    $currentPage = basename($_SERVER['PHP_SELF']);
    $home = isset($_SESSION['userID']) ? 'home.php' : 'index.php';
?>

</div>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="footer-about">
            <a href="<?= htmlspecialchars($home) ?>" class="footer-brand">
                <i class="bi bi-shop-window"></i>
                Community Store
            </a>
            <p>Buy, sell and connect within your community.</p>
        </div>

        <div class="footer-links">
            <a href="<?= htmlspecialchars($home) ?>">Home</a>

            <?php if (isset($_SESSION['userID'])): ?>
                <a href="bulletin.php">Bulletin Board</a>
                <a href="notifications.php">Notifications</a>
                <a href="profile.php">Profile</a>
            <?php else: ?>
                <a href="../authentication/login.php">Login</a>
                <a href="../authentication/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        © <?= date('Y') ?> Community Store. All rights reserved.
    </div>
</footer>

<nav class="bottom-nav">
    <a href="<?= htmlspecialchars($home) ?>" class="<?= in_array($currentPage, ['index.php', 'home.php'], true) ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>

    <a href="<?= htmlspecialchars($home) ?>#products">
        <i class="bi bi-bag"></i>
        <span>Products</span>
    </a>

    <?php if (isset($_SESSION['userID'])): ?>
        <a href="cart.php" class="<?= $currentPage === 'cart.php' ? 'active' : '' ?>">
            <i class="bi bi-cart3"></i>
            <span>Cart</span>
        </a>

        <a href="bulletin.php" class="<?= $currentPage === 'bulletin.php' ? 'active' : '' ?>">
            <i class="bi bi-megaphone"></i>
            <span>Bulletin</span>
        </a>

        <a href="profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>
    <?php else: ?>
        <a href="../authentication/login.php">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Login</span>
        </a>
    <?php endif; ?>
</nav>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html>
