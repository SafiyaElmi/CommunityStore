<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Listing.php';
require_once __DIR__ . '/../classes/Order.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$userID = (int)$_SESSION['userID'];

$userModel = new User();
$user = $userModel->getById($userID);

if (!$user) {
    header('Location: ../authentication/logout.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'update_name') {

        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');

        if ($firstName !== '' && $lastName !== '') {

            $stmt = $conn->prepare(
                "UPDATE `user`
                 SET firstName = ?, lastName = ?
                 WHERE userID = ?"
            );

            $stmt->bind_param(
                'ssi',
                $firstName,
                $lastName,
                $userID
            );

            $stmt->execute();
            $stmt->close();

            header('Location: profile.php');
            exit();
        }
    }

    if ($action === 'update_email') {

        $email = trim($_POST['email'] ?? '');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $stmt = $conn->prepare(
                "UPDATE `user`
                 SET email = ?
                 WHERE userID = ?"
            );

            $stmt->bind_param(
                'si',
                $email,
                $userID
            );

            $stmt->execute();
            $stmt->close();

            header('Location: profile.php');
            exit();
        }
    }

    if ($action === 'delete_account') {

        $stmt = $conn->prepare(
            "DELETE FROM `user`
             WHERE userID = ?"
        );

        $stmt->bind_param('i', $userID);

        if ($stmt->execute()) {

            $stmt->close();

            session_unset();
            session_destroy();

            header('Location: ../authentication/login.php');
            exit();

        } else {

            $stmt->close();

            header('Location: profile.php');
            exit();
        }
    }
}

$user = $userModel->getById($userID);

if (!$user) {
    header('Location: ../authentication/logout.php');
    exit();
}

$listingModel = new Listing();

$myListings = $listingModel->getByUser($userID);

$listingCount = $myListings->num_rows;

$orderModel = new Order();

$myOrders = $orderModel->getByUser($userID);

$orderCount = $myOrders->num_rows;

$reviewStmt = $conn->prepare(
    'SELECT COUNT(*) AS total
     FROM review
     WHERE userID = ?'
);

$reviewStmt->bind_param('i', $userID);

$reviewStmt->execute();

$reviewCount = (int)$reviewStmt
    ->get_result()
    ->fetch_assoc()['total'];

$reviewStmt->close();


$pageTitle = 'Profile';

require __DIR__ . '/../partials/header.php';

?>

<div class="profile-page">

    <section class="profile-hero">

        <div class="profile-actions">

            <a
                href="home.php"
                class="icon-btn profile-action-btn"
            >
                <i class="bi bi-arrow-left"></i>
            </a>

            <a
                href="../authentication/logout.php"
                class="icon-btn profile-action-btn"
            >
                <i class="bi bi-box-arrow-right"></i>
            </a>

        </div>

        <div class="profile-main">

            <div class="profile-avatar">
                <i class="bi bi-person"></i>
            </div>


            <div class="profile-details">

                <div
                    class="profile-edit-row"
                    id="nameDisplay"
                    data-firstname="<?= htmlspecialchars($user['firstName']) ?>"
                    data-lastname="<?= htmlspecialchars($user['lastName']) ?>"
                >

                    <h2>
                        <?= htmlspecialchars(
                            $user['firstName'] . ' ' . $user['lastName']
                        ) ?>
                    </h2>

                    <button
                        type="button"
                        class="profile-edit-btn edit-name-btn"
                        title="Edit name"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                </div>

                <div
                    class="profile-edit-row"
                    id="emailDisplay"
                    data-email="<?= htmlspecialchars($user['email']) ?>"
                >

                    <p>
                        <?= htmlspecialchars($user['email']) ?>
                    </p>

                    <button
                        type="button"
                        class="profile-edit-btn edit-email-btn"
                        title="Edit email"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                </div>

                <span class="profile-type-badge">
                    <?= htmlspecialchars($user['userType']) ?>
                </span>

                <div class="profile-delete-area">

                    <form method="POST" action="profile.php">

                        <input
                            type="hidden"
                            name="action"
                            value="delete_account"
                        >

                        <button
                            type="submit"
                            class="delete-account-btn confirm-delete"
                        >
                            <i class="bi bi-trash"></i>
                            Delete Account
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </section>

    <section class="stats-row">

        <div class="stat">

            <strong>
                <?= $listingCount ?>
            </strong>

            <span>
                Listings
            </span>

        </div>

        <div class="stat">

            <strong>
                <?= $orderCount ?>
            </strong>

            <span>
                Orders
            </span>

        </div>

        <div class="stat">

            <strong>
                <?= $reviewCount ?>
            </strong>

            <span>
                Reviews
            </span>

        </div>

        <div class="stat">

            <strong>
                <?= htmlspecialchars($user['userType']) ?>
            </strong>

            <span>
                Account Type
            </span>

        </div>

    </section>

    <section class="menu-list profile-menu">

        <a
            class="menu-item"
            href="#orders"
        >

            <div class="mi-icon">
                <i class="bi bi-bag"></i>
            </div>

            <div class="mi-text">

                <strong>
                    My Orders
                </strong>

                <span>
                    View all your orders (<?= $orderCount ?>)
                </span>

            </div>

            <i class="bi bi-chevron-right mi-arrow"></i>

        </a>

        <a
            class="menu-item"
            href="create_listing.php"
        >

            <div class="mi-icon">
                <i class="bi bi-plus-circle"></i>
            </div>

            <div class="mi-text">

                <strong>
                    Create Listing
                </strong>

                <span>
                    Sell a product or service
                </span>

            </div>

            <i class="bi bi-chevron-right mi-arrow"></i>

        </a>

        <a
            class="menu-item"
            href="#listings"
        >

            <div class="mi-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div class="mi-text">

                <strong>
                    My Listings
                </strong>

                <span>
                    Manage the items you sell (<?= $listingCount ?>)
                </span>

            </div>

            <i class="bi bi-chevron-right mi-arrow"></i>

        </a>

        <a
            class="menu-item"
            href="#reviews"
        >

            <div class="mi-icon">
                <i class="bi bi-star"></i>
            </div>

            <div class="mi-text">

                <strong>
                    Reviews
                </strong>

                <span>
                    Your product reviews (<?= $reviewCount ?>)
                </span>

            </div>

            <i class="bi bi-chevron-right mi-arrow"></i>

        </a>

        <a
            class="menu-item"
            href="notifications.php"
        >

            <div class="mi-icon">
                <i class="bi bi-question-circle"></i>
            </div>

            <div class="mi-text">

                <strong>
                    Help &amp; Support
                </strong>

                <span>
                    View notifications and updates.
                </span>

            </div>

            <i class="bi bi-chevron-right mi-arrow"></i>

        </a>

        <a
            class="menu-item danger"
            href="../authentication/logout.php"
        >

            <div class="mi-icon">
                <i class="bi bi-box-arrow-right"></i>
            </div>

            <div class="mi-text">

                <strong>
                    Logout
                </strong>

                <span>
                    Sign out from your account
                </span>

            </div>

            <i class="bi bi-chevron-right mi-arrow"></i>

        </a>

    </section>

    <section
        id="orders"
        class="profile-section"
    >

        <div class="section-head">

            <h3>
                My Orders
            </h3>

        </div>

        <?php if ($orderCount === 0): ?>

            <div class="empty-state compact-empty">

                <i class="bi bi-bag"></i>

                <p class="mt-2 muted mb-0">
                    You have no orders yet.
                </p>

            </div>

        <?php else: ?>

            <div class="profile-list">

                <?php while ($order = $myOrders->fetch_assoc()): ?>

                    <div class="card-soft profile-record">

                        <div>

                            <strong>
                                Order #<?= (int)$order['orderID'] ?>
                            </strong>

                            <div class="small muted mt-1">

                                <?= date(
                                    'd M Y',
                                    strtotime($order['orderDate'])
                                ) ?>

                            </div>

                        </div>

                        <span class="status-pill">

                            <?= htmlspecialchars(
                                $order['status']
                            ) ?>

                        </span>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </section>

    <section
        id="listings"
        class="profile-section"
    >

        <div class="section-head">

            <h3>
                My Listings
            </h3>

            <a href="create_listing.php">
                Add listing
            </a>

        </div>

        <?php if ($listingCount === 0): ?>

            <div class="empty-state compact-empty">

                <i class="bi bi-box-seam"></i>

                <p class="mt-2 muted mb-0">
                    You have no listings yet.
                </p>

            </div>

        <?php else: ?>

            <div class="profile-list">

                <?php while ($listing = $myListings->fetch_assoc()): ?>

                    <div class="card-soft profile-record listing-record">

                        <div class="record-main">

                            <strong>
                                <?= htmlspecialchars(
                                    $listing['title']
                                ) ?>
                            </strong>

                            <div class="small muted mt-1">

                                R<?= number_format(
                                    (float)$listing['price'],
                                    2
                                ) ?>

                                ·

                                <?= htmlspecialchars(
                                    $listing['location']
                                ) ?>

                            </div>

                            <?php if (
                                !empty($listing['salePrice']) &&
                                !empty($listing['saleType'])
                            ): ?>

                                <?php if (
                                    $listing['saleType'] === 'Recurring'
                                ): ?>

                                    <div class="small text-purple mt-1">

                                        R<?= number_format(
                                            (float)$listing['salePrice'],
                                            2
                                        ) ?>

                                        every

                                        <?= htmlspecialchars(
                                            $listing['recurringWeek']
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $listing['recurringDay']
                                        ) ?>

                                    </div>

                                <?php elseif (
                                    $listing['saleType'] === 'One-time'
                                ): ?>

                                    <div class="small text-purple mt-1">

                                        R<?= number_format(
                                            (float)$listing['salePrice'],
                                            2
                                        ) ?>

                                        from

                                        <?= date(
                                            'd M Y',
                                            strtotime(
                                                $listing['saleStart']
                                            )
                                        ) ?>

                                        to

                                        <?= date(
                                            'd M Y',
                                            strtotime(
                                                $listing['saleEnd']
                                            )
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                            <?php endif; ?>

                        </div>

                        <div class="record-actions">

                            <span class="status-pill">

                                <?= htmlspecialchars(
                                    $listing['status']
                                ) ?>

                            </span>

                            <div>

                                <a
                                    class="small text-purple fw-bold"
                                    href="listing.php?id=<?= (int)$listing['listingID'] ?>"
                                >
                                    View
                                </a>

                                <span class="muted">
                                    ·
                                </span>

                                <a
                                    class="small text-purple fw-bold"
                                    href="edit_listing.php?id=<?= (int)$listing['listingID'] ?>"
                                >
                                    Edit
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </section>

    <section
        id="reviews"
        class="profile-section"
    >

        <div class="section-head">

            <h3>
                My Reviews
            </h3>

        </div>

        <div class="card-soft review-summary">

            <div class="mi-icon">
                <i class="bi bi-star"></i>
            </div>

            <div>

                <strong>

                    <?= $reviewCount ?>

                    review<?= $reviewCount === 1 ? '' : 's' ?>

                </strong>

                <p class="muted mb-0">

                    Your reviews are linked to the products you
                    have reviewed.

                </p>

            </div>

        </div>

    </section>

</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>