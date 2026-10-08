<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Listing.php';

if (isset($_SESSION['userID'])) {
    header('Location: home.php');
    exit();
}

$catModel = new Category();
$listingModel = new Listing();
$category = (int)($_GET['category'] ?? 0);
$search = trim($_GET['search'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$location = trim($_GET['location'] ?? '');
$categories = $catModel->getAll();
$listings = $listingModel->getAll($search, $category, $minPrice, $maxPrice, $location);

$pageTitle = 'Home';
require __DIR__ . '/../partials/header.php';
?>

<div class="top-bar">
    <div>
        <h1>Welcome to StudentMarket</h1>
        <p class="muted m-0 small">Buy &amp; sell with students on your campus</p>
    </div>
</div>

<div class="promo">
    <div>
        <span class="badge-pill">Community Sale</span>
        <h2>Up to 30% Off<br>On Deals</h2>
        <p>High performance. Best prices.</p>
    </div>
    <a href="../authentication/register.php" class="btn-promo">Get Started</a>
</div>

<div class="section-head"><h3>Categories</h3></div>
<div class="cat-grid">
<?php
$icons = ['bi-headphones','bi-book','bi-person-standing','bi-laptop','bi-cup-hot','bi-lamp','bi-controller','bi-person'];
$i = 0;
while ($cat = $categories->fetch_assoc()):
?>
    <a href="index.php?category=<?= (int)$cat['categoryID'] ?>#products" class="cat-item">
        <i class="bi <?= $icons[$i++ % count($icons)] ?>"></i>
        <span><?= htmlspecialchars($cat['categoryName']) ?></span>
    </a>
<?php endwhile; ?>
</div>

<div class="section-head" id="products"><h3>Featured Products</h3></div>
<form class="search-wrap" action="index.php" method="GET">
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
            <div class="rating"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($l['location']) ?></div><div class="price">R<?= number_format((float)$l['price'], 2) ?></div>
        </div>
    </a>
<?php endwhile; ?>
<?php if (!$found): ?><div style="grid-column:1/-1;"><div class="empty-state"><i class="bi bi-search"></i><p class="mt-2 muted">No products found.</p></div></div><?php endif; ?>
</div>

<div class="card-soft text-center" style="margin-top:26px;">
    <h4 style="font-weight:800;">Want to sell something?</h4>
    <p class="muted small">Create a free account to list items.</p>
    <a href="../authentication/register.php" class="btn-purple" style="max-width:240px;margin:auto;">Create Account</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>