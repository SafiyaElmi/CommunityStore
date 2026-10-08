<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../classes/CartItem.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$userID = (int)$_SESSION['userID'];
$cartModel = new Cart();
$cart = $cartModel->getByUser($userID);

if (!$cart) {
    $cartModel->create($userID);
    $cart = $cartModel->getByUser($userID);
}

$ciModel = new CartItem();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateQuantity'])) {
        $cartItemID = (int)($_POST['cartItemID'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $ciModel->updateForUser($cartItemID, $userID, $quantity);
        $_SESSION['success'] = 'Cart updated.';
        header('Location: cart.php');
        exit();
    }

    if (isset($_POST['removeItem'])) {
        $cartItemID = (int)($_POST['cartItemID'] ?? 0);
        $ciModel->deleteForUser($cartItemID, $userID);
        $_SESSION['success'] = 'Item removed from cart.';
        header('Location: cart.php');
        exit();
    }
}

if (isset($_GET['remove'])) {
    $ciModel->deleteForUser((int)$_GET['remove'], $userID);
    $_SESSION['success'] = 'Item removed from cart.';
    header('Location: cart.php');
    exit();
}

$items = $ciModel->getByCart((int)$cart['cartID']);
$pageTitle = 'Cart';
require __DIR__ . '/../partials/header.php';
?>

<div class="top-bar">
    <a href="home.php" class="icon-btn"><i class="bi bi-arrow-left"></i></a>
    <h1 class="m-0">Cart Items (<span><?= (int)$items->num_rows ?></span>)</h1>
    <span style="width:42px;"></span>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success auto-dismiss"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if ($items->num_rows === 0): ?>
    <div class="empty-state">
        <i class="bi bi-cart3"></i>
        <p class="mt-3 muted">Your cart is empty.</p>
        <a href="home.php" class="btn-purple" style="max-width:220px;margin:auto;">Browse Products</a>
    </div>
<?php else: ?>
    <?php $total = 0; while ($it = $items->fetch_assoc()): $sub = (float)$it['price'] * (int)$it['quantity']; $total += $sub; ?>
        <div class="cart-row">
            <div class="cart-thumb">
                <?php if (!empty($it['imageURL'])): ?>
                    <img src="<?= htmlspecialchars($it['imageURL']) ?>" alt="">
                <?php else: ?>
                    <div class="no-img" style="height:100%;display:grid;place-items:center;color:#b6b6c2;"><i class="bi bi-image"></i></div>
                <?php endif; ?>
            </div>
            <div class="cart-info">
                <h5><?= htmlspecialchars($it['title']) ?></h5>
                <div class="price">R<?= number_format((float)$it['price'], 2) ?></div>
                <?php if ($it['status'] !== 'Available'): ?><small class="text-danger">Currently <?= htmlspecialchars($it['status']) ?></small><?php endif; ?>
            </div>
            <form method="POST" class="qty" style="display:flex;align-items:center;gap:4px;">
                <input type="hidden" name="cartItemID" value="<?= (int)$it['cartItemID'] ?>">
                <input type="hidden" name="updateQuantity" value="1">
                <button data-step="down" type="button" aria-label="Decrease quantity">−</button>
                <input class="qty-value" name="quantity" type="number" min="1" value="<?= (int)$it['quantity'] ?>" aria-label="Quantity">
                <button data-step="up" type="button" aria-label="Increase quantity">+</button>
            </form>
            <form method="POST" style="margin:0;">
                <input type="hidden" name="cartItemID" value="<?= (int)$it['cartItemID'] ?>">
                <button name="removeItem" type="submit" class="muted" style="border:0;background:none;font-size:1.1rem;" aria-label="Remove item">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    <?php endwhile; ?>

    <div class="summary mt-4">
        <div class="row-line"><span>Subtotal</span><strong>R<?= number_format($total, 2) ?></strong></div>
        <div class="row-line"><span>Delivery Fee</span><strong class="text-success">R0.00</strong></div>
        <div class="row-line total"><span>Total</span><span>R<?= number_format($total, 2) ?></span></div>
    </div>

    <a href="checkout.php" class="btn-purple mt-3"><i class="bi bi-lock"></i> Checkout</a>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>