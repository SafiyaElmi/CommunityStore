<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Notification.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$userID = (int)$_SESSION['userID'];
$model = new Notification();
$list = $model->getByUser($userID);

$pageTitle = 'Notifications';
require __DIR__ . '/../partials/header.php';
?>

<div class="top-bar">
    <a href="home.php" class="icon-btn"><i class="bi bi-arrow-left"></i></a>
    <h1 class="m-0">Notifications</h1>
    <span style="width:42px;"></span>
</div>

<?php if ($list->num_rows === 0): ?>
    <div class="empty-state"><i class="bi bi-bell"></i><p class="mt-3 muted">No notifications yet.</p></div>
<?php else: ?>
    <?php while ($n = $list->fetch_assoc()): ?>
        <div class="notif">
            <div class="n-icon"><i class="bi bi-bell"></i></div>
            <div class="n-body">
                <h6><?= htmlspecialchars($n['message']) ?></h6>
                <p>Notification</p>
                <span class="n-time"><?= date('d M · H:i', strtotime($n['notificationDate'])) ?></span>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>