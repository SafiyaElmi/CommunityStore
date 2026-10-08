<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Listing.php';
require_once __DIR__ . '/../classes/Review.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$catModel = new Category();
$listingModel = new Listing();
$reviewModel = new Review();

$category = (int)($_GET['category'] ?? 0);
$search = trim($_GET['search'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$location = trim($_GET['location'] ?? '');
$categories = $catModel->getAll();
$listings = $listingModel->getAll($search, $category, $minPrice, $maxPrice, $location);

$firstName = $_SESSION['firstName'] ?? 'there';
$pageTitle = 'Home';
require __DIR__ . '/../partials/header.php';
?>

<div class="top-bar">
    <div>
        <h1>Hi, <?= htmlspecialchars($firstName) ?></h1>
        <p class="muted m-0 small">
            <?php if (($_SESSION['userType'] ?? '') === 'Vendor'): ?>
                Manage your listings and marketplace activity.
            <?php elseif (($_SESSION['userType'] ?? '') === 'Student'): ?>
                Browse, buy and sell with your community.
            <?php else: ?>
                Browse, buy and sell within your community.
            <?php endif; ?>
        </p>
    </div>
    <a href="../authentication/logout.php" class="btn-purple nav-login">
        Logout
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success auto-dismiss"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="promo">
    <div>
        <span class="badge-pill">Community Sale</span>
        <h2>Up to 30% Off<br>On Deals</h2>
        <p>High performance. Best prices.</p>
    </div>
    <a href="#products" class="btn-promo">Shop Now</a>
</div>

<div class="section-head">
    <div>
        <h3>Quick Access</h3>
        <p class="muted small mb-0">Choose what you want to do today.</p>
    </div>
</div>

<div class="cat-grid quick-access-grid">
    <a href="profile.php#orders" class="cat-item">
        <i class="bi bi-bag-check"></i>
        <span>My Orders</span>
    </a>

    <a href="create_listing.php" class="cat-item role-function" data-roles="Student,Resident,Vendor">
        <i class="bi bi-plus-circle"></i>
        <span>Sell Item</span>
    </a>

    <a href="bulletin.php" class="cat-item">
        <i class="bi bi-megaphone"></i>
        <span>Bulletin Board</span>
    </a>

    <a href="notifications.php" class="cat-item">
        <i class="bi bi-bell"></i>
        <span>Notifications</span>
    </a>

    <a href="profile.php#listings" class="cat-item role-function" data-roles="Vendor">
        <i class="bi bi-box-seam"></i>
        <span>My Listings</span>
    </a>
</div>

<div class="role-welcome role-function" data-roles="Vendor">
    <div>
        <strong>Vendor tools</strong>
        <p>Manage your listings and keep track of your marketplace activity.</p>
    </div>
    <a href="profile.php#listings" class="btn-ghost">Manage Listings</a>
</div>

<div class="section-head"><h3>Categories</h3></div>
<div class="cat-grid">
    <?php
    $icons = ['bi-headphones','bi-book','bi-person-standing','bi-laptop','bi-cup-hot','bi-lamp','bi-controller','bi-person'];
    $i = 0;
    while ($cat = $categories->fetch_assoc()):
    ?>
        <a href="home.php?category=<?= (int)$cat['categoryID'] ?>#products" class="cat-item">
            <i class="bi <?= $icons[$i++ % count($icons)] ?>"></i>
            <span><?= htmlspecialchars($cat['categoryName']) ?></span>
        </a>
    <?php endwhile; ?>
</div>

<div class="section-head" id="products"><h3>Featured Products</h3></div>
<form class="search-wrap" action="home.php" method="GET">
    <input class="form-control" type="search" name="search" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>">
    <?php if ($category): ?><input type="hidden" name="category" value="<?= $category ?>"><?php endif; ?>
    <input class="form-control" type="number" step="0.01" name="min_price" placeholder="Min R" value="<?= htmlspecialchars($minPrice) ?>">
    <input class="form-control" type="number" step="0.01" name="max_price" placeholder="Max R" value="<?= htmlspecialchars($maxPrice) ?>">
    <select class="form-select location-select" name="location">
        <option value="">All locations</option>
        <?php
        $locationOptions = $listingModel->getLocations();
        while ($locationRow = $locationOptions->fetch_assoc()):
            $locationName = $locationRow['location'];
        ?>
            <option value="<?= htmlspecialchars($locationName) ?>" <?= $location === $locationName ? 'selected' : '' ?>>
                <?= htmlspecialchars($locationName) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button class="btn-purple search-btn"><i class="bi bi-search"></i></button>
</form>

<div class="product-grid">
<?php $found = false; while ($l = $listings->fetch_assoc()): $found = true; ?>
    <a href="listing.php?id=<?= (int)$l['listingID'] ?>" class="product-card">
        <div class="product-thumb">
            <?php if (!empty($l['imageURL'])): ?>
                <img src="<?= htmlspecialchars($l['imageURL']) ?>" alt="<?= htmlspecialchars($l['title']) ?>">
            <?php else: ?>
                <div class="no-img"><i class="bi bi-image"></i></div>
            <?php endif; ?>
        </div>
        <div class="product-body">
            <h4><?= htmlspecialchars($l['title']) ?></h4>
            <?php
            $statsStmt = $conn->prepare('SELECT COALESCE(AVG(rating),0) AS avgRating, COUNT(*) AS reviewCount FROM review WHERE listingID = ?');
            $listingID = (int)$l['listingID'];
            $statsStmt->bind_param('i', $listingID);
            $statsStmt->execute();
            $stats = $statsStmt->get_result()->fetch_assoc();
            $statsStmt->close();
            ?>
            <div class="rating">★★★★★ <span>(<?= number_format((float)$stats['avgRating'], 1) ?> · <?= (int)$stats['reviewCount'] ?> reviews)</span></div>
            <div class="rating"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($l['location']) ?></div><div class="price">R<?= number_format((float)$l['price'], 2) ?></div>
        </div>
    </a>
<?php endwhile; ?>
<?php if (!$found): ?>
    <div style="grid-column:1/-1;"><div class="empty-state"><i class="bi bi-search"></i><p class="mt-2 muted">No products found.</p></div></div>
<?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>