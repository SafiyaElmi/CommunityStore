<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/BulletinPost.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$bulletinModel = new BulletinPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createPost'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $postType = $_POST['postType'] ?? 'Announcement';

    if (
        $title !== '' &&
        $content !== '' &&
        in_array($postType, ['Announcement', 'Event', 'Service'], true)
    ) {
        $bulletinModel->create(
            $title,
            $content,
            $postType,
            (int)$_SESSION['userID']
        );

        $_SESSION['success'] = 'Bulletin post published.';
        header('Location: bulletin.php');
        exit();
    }

    $_SESSION['error'] = 'Please complete the bulletin post.';
}

if (isset($_GET['delete'])) {
    $bulletinModel->delete(
        (int)$_GET['delete'],
        (int)$_SESSION['userID']
    );

    header('Location: bulletin.php');
    exit();
}

$posts = $bulletinModel->getAll();

$pageTitle = 'Community Bulletin';
require __DIR__ . '/../partials/header.php';
?>

<div class="page-heading">
    <a href="home.php" class="icon-btn" aria-label="Back to home">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div>
        <h1>Community Bulletin</h1>
        <p>Share announcements, events and services with the community.</p>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success auto-dismiss">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger auto-dismiss">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card-soft form-card bulletin-create">
    <div class="section-head mt-0">
        <div>
            <h3>Create a Post</h3>
            <p class="muted small mb-0">Add something useful for the community.</p>
        </div>
    </div>

    <form method="POST">
        <input type="hidden" name="createPost" value="1">

        <div class="form-section">
            <label for="title" class="form-label">Post Title</label>
            <input id="title" name="title" class="form-control" placeholder="Post title" required>
        </div>

        <div class="form-section">
            <label for="postType" class="form-label">Post Type</label>
            <select id="postType" name="postType" class="form-select">
                <option value="Announcement">Announcement</option>
                <option value="Event">Event</option>
                <option value="Service">Service</option>
            </select>
        </div>

        <div class="form-section">
            <label for="content" class="form-label">Content</label>
            <textarea id="content" name="content" class="form-control" rows="5" placeholder="Write your announcement, event or service..." required></textarea>
        </div>

        <button type="submit" class="btn-purple">
            <i class="bi bi-megaphone"></i>
            Post to Bulletin
        </button>
    </form>
</div>

<div class="section-head">
    <div>
        <h3>Community Posts</h3>
    </div>
</div>

<div class="bulletin-list">
    <?php while ($post = $posts->fetch_assoc()): ?>
        <article class="card-soft bulletin-card">
            <div class="bulletin-card-head">
                <div>
                    <h4><?= htmlspecialchars($post['title']) ?></h4>
                    <span class="badge bg-light text-dark">
                        <?= htmlspecialchars($post['postType']) ?>
                    </span>
                </div>
            </div>

            <p class="muted bulletin-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </p>

            <div class="bulletin-meta">
                <span>
                    By <?= htmlspecialchars($post['firstName'] . ' ' . $post['lastName']) ?>
                </span>
                <span>
                    <?= date('d M Y H:i', strtotime($post['postDate'])) ?>
                </span>
            </div>

            <?php if ((int)$post['userID'] === (int)$_SESSION['userID']): ?>
                <a
                    class="delete-link"
                    href="?delete=<?= (int)$post['bulletinID'] ?>"
                    onclick="return confirm('Delete this post?')"
                >
                    Delete post
                </a>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
