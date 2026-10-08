<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Listing.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$listingModel = new Listing();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $type = $_POST['type'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $categoryID = (int)($_POST['categoryID'] ?? 0);

    if (
        $title === '' ||
        $price <= 0 ||
        !in_array($type, ['New', 'Fresh', 'Used', 'N/A'], true) ||
        $location === '' ||
        $categoryID <= 0
    ) {
        $_SESSION['error'] = 'Please complete all listing details.';
        header('Location: create_listing.php');
        exit();
    }

    $imageURL = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'The image could not be uploaded.';
            header('Location: create_listing.php');
            exit();
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif'
        ];

        $mimeType = mime_content_type($file['tmp_name']);

        if (!isset($allowedTypes[$mimeType])) {
            $_SESSION['error'] = 'Please upload a JPG, PNG, WEBP or GIF image.';
            header('Location: create_listing.php');
            exit();
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'The image must be smaller than 5 MB.';
            header('Location: create_listing.php');
            exit();
        }

        $uploadDirectory = __DIR__ . '/../uploads/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $filename = uniqid('listing_', true) . '.' . $allowedTypes[$mimeType];
        $target = $uploadDirectory . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $_SESSION['error'] = 'The image could not be saved.';
            header('Location: create_listing.php');
            exit();
        }

        $imageURL = '../uploads/' . $filename;
    }

    if (
        $listingModel->create(
            $title,
            $description,
            $price,
            $type,
            $imageURL,
            $location,
            'Available',
            (int)$_SESSION['userID'],
            $categoryID
        )
    ) {
        $_SESSION['success'] = 'Listing created successfully.';
        header('Location: profile.php#listings');
        exit();
    }

    $_SESSION['error'] = 'Unable to create listing.';
}

$categories = (new Category())->getAll();

$pageTitle = 'Create Listing';
require __DIR__ . '/../partials/header.php';
?>

<div class="page-heading">
    <a href="home.php" class="icon-btn" aria-label="Back to home">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1>Create Listing</h1>
        <p>List a product or service for the community.</p>
    </div>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger auto-dismiss">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card-soft form-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <label for="title" class="form-label">Title</label>
            <input id="title" name="title" class="form-control" required>
        </div>

        <div class="form-section">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="row g-3 form-section">
            <div class="col-md-6">
                <label for="price" class="form-label">Price</label>
                <input id="price" name="price" type="number" min="0.01" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="type" class="form-label">Condition</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="" selected disabled>Select condition</option>
                    <option value="New">New</option>
                    <option value="New">Fresh</option>
                    <option value="Used">Used</option>
                    <option value="Used">N/A</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <label for="categoryID" class="form-label">Category</label>
            <select id="categoryID" name="categoryID" class="form-select" required>
                <option value="">Select category</option>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <option value="<?= (int)$category['categoryID'] ?>">
                        <?= htmlspecialchars($category['categoryName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-section">
            <label for="location" class="form-label">Location</label>
            <input id="location" name="location" class="form-control" required>
        </div>

        <div class="form-section">
            <label for="image" class="form-label">Product Image</label>
            <input id="image" name="image" type="file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
            <small class="form-help">Images are saved in the uploads folder. Maximum size: 5 MB.</small>
        </div>

        <button type="submit" class="btn-purple">
            <i class="bi bi-plus-circle"></i>
            Publish Listing
        </button>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
