<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/CartItem.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/OrderItem.php';
require_once __DIR__ . '/../classes/Payment.php';
require_once __DIR__ . '/../classes/Notification.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$userID = (int)$_SESSION['userID'];
$cartModel = new Cart();
$cart = $cartModel->getByUser($userID);
if (!$cart) { header('Location: cart.php'); exit(); }

$ciModel = new CartItem();
$items = $ciModel->getByCart((int)$cart['cartID']);
if ($items->num_rows === 0) { header('Location: cart.php'); exit(); }

$total = 0;
$rows = [];
while ($r = $items->fetch_assoc()) {
    $total += (float)$r['price'] * (int)$r['quantity'];
    $rows[] = $r;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'] ?? 'Cash';
    $allowedMethods = ['Card', 'EFT', 'Cash', 'PayFast', 'SnapScan'];

    if (!in_array($method, $allowedMethods, true)) {
        $_SESSION['error'] = 'Please select a valid payment method.';
        header('Location: checkout.php');
        exit();
    }

    try {
        $conn->begin_transaction();

        $lockedListings = [];
        $check = $conn->prepare('SELECT listingID, userID, price, status FROM listing WHERE listingID = ? FOR UPDATE');
        foreach ($rows as $r) {
            $listingID = (int)$r['listingID'];
            $check->bind_param('i', $listingID);
            $check->execute();
            $listing = $check->get_result()->fetch_assoc();

            if (!$listing || $listing['status'] !== 'Available') {
                throw new RuntimeException('One of the items in your cart is no longer available.');
            }
            if ((int)$listing['userID'] === $userID) {
                throw new RuntimeException('You cannot purchase your own listing.');
            }
            $lockedListings[$listingID] = $listing;
        }
        $check->close();

        $orderModel = new Order();
        if (!$orderModel->create('Pending', $userID)) {
            throw new RuntimeException('Unable to create the order.');
        }
        $orderID = (int)$conn->insert_id;

        $orderItemModel = new OrderItem();
        $notificationModel = new Notification();
        $sellerIDs = [];

        foreach ($rows as $r) {
            $listingID = (int)$r['listingID'];
            $quantity = (int)$r['quantity'];
            $price = (float)$lockedListings[$listingID]['price'];

            if (!$orderItemModel->create($quantity, $price, $orderID, $listingID)) {
                throw new RuntimeException('Unable to create an order item.');
            }

            $update = $conn->prepare("UPDATE listing SET status = 'Reserved' WHERE listingID = ? AND status = 'Available'");
            $update->bind_param('i', $listingID);
            $update->execute();
            if ($update->affected_rows !== 1) {
                $update->close();
                throw new RuntimeException('An item became unavailable during checkout.');
            }
            $update->close();

            $sellerIDs[(int)$lockedListings[$listingID]['userID']] = true;
        }

        $payModel = new Payment();
        if (!$payModel->create($total, $method, 'Pending', $orderID)) {
            throw new RuntimeException('Unable to create the payment record.');
        }

        foreach ($rows as $r) {
            if (!$ciModel->deleteForUser((int)$r['cartItemID'], $userID)) {
                throw new RuntimeException('Unable to clear the cart.');
            }
        }

        $notificationModel->create('Your order #' . $orderID . ' was placed and is pending payment.', $userID);
        foreach (array_keys($sellerIDs) as $sellerID) {
            $notificationModel->create('A new order #' . $orderID . ' was placed for one of your listings.', (int)$sellerID);
        }

        $conn->commit();
        $_SESSION['success'] = 'Order #' . $orderID . ' placed successfully. Payment is pending.';
        header('Location: home.php');
        exit();
    } catch (Throwable $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header('Location: checkout.php');
        exit();
    }
}

$pageTitle = 'Checkout';
require __DIR__ . '/../partials/header.php';
?>

<div class="top-bar">
    <a href="cart.php" class="icon-btn"><i class="bi bi-arrow-left"></i></a>
    <h1 class="m-0">Secure Checkout</h1>
    <span style="width:42px;"></span>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="section-head"><h3>Review Your Order</h3></div>

<?php foreach ($rows as $r): ?>
    <div class="d-flex justify-content-between mb-2">
        <span><?= htmlspecialchars($r['title']) ?> × <?= (int)$r['quantity'] ?></span>
        <strong>R<?= number_format((float)$r['price'] * (int)$r['quantity'], 2) ?></strong>
    </div>
<?php endforeach; ?>

<hr>
<div class="d-flex justify-content-between mb-4">
    <span>Order Total:</span>
    <strong class="price">R<?= number_format($total, 2) ?></strong>
</div>

<form method="POST">
    <label class="fw-bold small mb-2 d-block">Payment Method</label>
    <select name="method" class="form-select mb-3" required>
        <option value="Card">Card</option>
        <option value="EFT">EFT</option>
        <option value="Cash">Cash</option>
        <option value="PayFast">PayFast</option><option value="SnapScan">SnapScan</option>
    </select>

    <div class="alert alert-info small">
        Payment is recorded as <strong>Accepted</strong>. PayFast and SnapScan are supported payment choices; live gateway credentials are configured separately.
    </div>

    <button class="btn-purple"><i class="bi bi-lock"></i> Place Order</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>