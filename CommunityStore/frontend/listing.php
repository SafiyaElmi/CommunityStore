<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Listing.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/CartItem.php';
require_once __DIR__ . '/../classes/Review.php';

$listingID = (int)($_GET['id'] ?? 0);

$listingModel = new Listing();
$listing = $listingModel->getById($listingID);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitReview']) && isset($_SESSION['userID'])) {

    $reviewRating = (int)($_POST['rating'] ?? 0);
    $reviewComment = trim($_POST['comment'] ?? '');

    if ($reviewRating >= 1 && $reviewRating <= 5 && $reviewComment !== '') {

        $reviewWriter = new Review();

        if ($reviewWriter->create(
            $reviewRating,
            $reviewComment,
            (int)$_SESSION['userID'],
            $listingID
        )) {
            $_SESSION['success'] = 'Review submitted successfully.';
        } else {
            $_SESSION['error'] = 'Unable to submit your review.';
        }

    } else {
        $_SESSION['error'] = 'Please choose a rating and enter a comment.';
    }

    header('Location: listing.php?id=' . $listingID);
    exit();
}

if (!$listing) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addToCart'])) {

    if (!isset($_SESSION['userID'])) {
        header('Location: ../authentication/login.php');
        exit();
    }

    $userID = (int)$_SESSION['userID'];

    if ($userID === (int)$listing['userID']) {
        $_SESSION['error'] = 'You cannot add your own listing to your cart.';
        header('Location: listing.php?id=' . $listingID);
        exit();
    }

    if ($listing['status'] !== 'Available') {
        $_SESSION['error'] = 'This listing is no longer available.';
        header('Location: listing.php?id=' . $listingID);
        exit();
    }

    $cartModel = new Cart();
    $cart = $cartModel->getByUser($userID);

    if (!$cart) {
        $cartModel->create($userID);
        $cart = $cartModel->getByUser($userID);
    }

    $ciModel = new CartItem();

    if (!$ciModel->create(
        1,
        (int)$cart['cartID'],
        $listingID
    )) {
        $_SESSION['error'] = 'Unable to add this item to your cart.';
        header('Location: listing.php?id=' . $listingID);
        exit();
    }

    $_SESSION['success'] = 'Added to cart.';
    header('Location: cart.php');
    exit();
}

$reviewModel = new Review();
$reviews = $reviewModel->getByListing($listingID);
$reviewCount = $reviews->num_rows;

$ratingStmt = $conn->prepare(
    'SELECT COALESCE(AVG(rating), 0) AS avgRating
     FROM review
     WHERE listingID = ?'
);

$ratingStmt->bind_param('i', $listingID);
$ratingStmt->execute();

$rating = $ratingStmt->get_result()->fetch_assoc();

$ratingStmt->close();

$pageTitle = $listing['title'];

require __DIR__ . '/../partials/header.php';
?>

<div class="listing-page">

    <div class="top-bar">
        <a href="javascript:history.back()" class="icon-btn">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']); ?>
            <?php unset($_SESSION['error']); ?>
        </div>

    <?php endif; ?>

    <div class="listing-image">

        <?php if (!empty($listing['imageURL'])): ?>

            <img
                src="<?= htmlspecialchars($listing['imageURL']) ?>"
                alt="<?= htmlspecialchars($listing['title']) ?>"
            >

        <?php else: ?>

            <div class="no-img">
                <i class="bi bi-image"></i>
            </div>

        <?php endif; ?>

    </div>

    <h2 class="listing-title">
        <?= htmlspecialchars($listing['title']) ?>
    </h2>

    <div class="rating listing-rating text-warning small">

        ★★★★★

        <span class="muted">
            (
            <?= number_format((float)$rating['avgRating'], 1) ?>
            ·
            <?= (int)$reviewCount ?>
            reviews
            )
        </span>

    </div>

    <div class="listing-price">
        <span class="price fs-4">
            R<?= number_format((float)$listing['price'], 2) ?>
        </span>
    </div>

    <div class="small muted listing-location">

        <i class="bi bi-geo-alt"></i>

        <?= htmlspecialchars($listing['location']) ?>

    </div>

    <div class="row g-2 listing-details">

        <div class="col-6">

            <div class="card-soft d-flex align-items-center gap-2">

                <i class="bi bi-cpu text-purple"></i>

                <div>

                    <div class="small fw-bold">
                        <?= htmlspecialchars($listing['type']) ?>
                    </div>

                    <div class="muted" style="font-size:.72rem;">
                        Condition
                    </div>

                </div>

            </div>

        </div>

        <div class="col-6">

            <div class="card-soft d-flex align-items-center gap-2">

                <i class="bi bi-tag text-purple"></i>

                <div>

                    <div class="small fw-bold">
                        <?= htmlspecialchars($listing['categoryName']) ?>
                    </div>

                    <div class="muted" style="font-size:.72rem;">
                        Category
                    </div>

                </div>

            </div>

        </div>

    </div>

    <p class="muted listing-description">
        <?= nl2br(htmlspecialchars($listing['description'])) ?>
    </p>

    <div class="summary listing-summary">

        <div class="row-line">

            <span>Seller</span>

            <strong>
                <?= htmlspecialchars(
                    $listing['firstName'] . ' ' . $listing['lastName']
                ) ?>
            </strong>

        </div>

        <div class="row-line">

            <span>Status</span>

            <strong>
                <?= htmlspecialchars($listing['status']) ?>
            </strong>

        </div>

    </div>

    <div class="listing-actions">

        <?php if (
            isset($_SESSION['userID']) &&
            (int)$_SESSION['userID'] !== (int)$listing['userID'] &&
            $listing['status'] === 'Available'
        ): ?>

            <form method="POST">

                <button
                    name="addToCart"
                    class="btn-purple"
                >
                    <i class="bi bi-cart-plus"></i>
                    Add to Cart
                </button>

            </form>

        <?php elseif (!isset($_SESSION['userID'])): ?>

            <a
                href="../authentication/login.php"
                class="btn-purple"
            >
                Login to Buy
            </a>

        <?php endif; ?>

    </div>

    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success auto-dismiss">

            <?= htmlspecialchars($_SESSION['success']); ?>

            <?php unset($_SESSION['success']); ?>

        </div>

    <?php endif; ?>

    <?php if (
        isset($_SESSION['userID']) &&
        (int)$_SESSION['userID'] !== (int)$listing['userID']
    ): ?>

        <div class="card-soft review-card">

            <h3>Leave a Review</h3>

            <form method="POST">

                <input
                    type="hidden"
                    name="submitReview"
                    value="1"
                >


                <select
                    name="rating"
                    class="form-select mb-2"
                    required
                >

                    <option value="">
                        Rating
                    </option>

                    <option value="5">
                        5 - Excellent
                    </option>

                    <option value="4">
                        4 - Good
                    </option>

                    <option value="3">
                        3 - Average
                    </option>

                    <option value="2">
                        2 - Poor
                    </option>

                    <option value="1">
                        1 - Very poor
                    </option>

                </select>

                <textarea
                    name="comment"
                    class="form-control mb-2"
                    rows="3"
                    placeholder="Write your feedback..."
                    required
                ></textarea>

                <button class="btn-purple">
                    Submit Review
                </button>

            </form>

        </div>

    <?php endif; ?>

    <div class="section-head reviews-section">

        <h3>
            Reviews
        </h3>

    </div>

    <?php if ($reviewCount === 0): ?>

        <p class="muted">
            No reviews yet.
        </p>

    <?php else: ?>

        <?php while ($review = $reviews->fetch_assoc()): ?>

            <div class="card-soft review-item">

                <div class="d-flex justify-content-between">

                    <strong>
                        <?= htmlspecialchars(
                            $review['firstName'] . ' ' . $review['lastName']
                        ) ?>
                    </strong>

                    <span class="rating">
                        ★★★★★
                    </span>

                </div>

                <div class="small text-warning">

                    <?= str_repeat(
                        '★',
                        max(
                            0,
                            min(
                                5,
                                (int)$review['rating']
                            )
                        )
                    ) ?>

                </div>

                <?php if (!empty($review['comment'])): ?>

                    <p class="mb-0 mt-1 muted">

                        <?= nl2br(
                            htmlspecialchars($review['comment'])
                        ) ?>

                    </p>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
