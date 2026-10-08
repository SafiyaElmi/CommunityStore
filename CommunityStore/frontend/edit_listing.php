<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Listing.php';

if (!isset($_SESSION['userID'])) {
    header('Location: ../authentication/login.php');
    exit();
}

$listingID = (int)($_GET['id'] ?? $_POST['listingID'] ?? 0);

$listingModel = new Listing();
$listing = $listingModel->getById($listingID);

if (!$listing || (int)$listing['userID'] !== (int)$_SESSION['userID']) {
    header('Location: profile.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);

    $type = $_POST['type'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $status = $_POST['status'] ?? 'Available';
    $categoryID = (int)($_POST['categoryID'] ?? 0);

    $saleType = $_POST['saleType'] ?? '';

    $salePriceInput = trim($_POST['salePrice'] ?? '');

    $salePrice = $salePriceInput !== ''
        ? (float)$salePriceInput
        : null;

    $saleStart = trim($_POST['saleStart'] ?? '');
    $saleEnd = trim($_POST['saleEnd'] ?? '');

    $recurringDay = $_POST['recurringDay'] ?? '';
    $recurringWeek = $_POST['recurringWeek'] ?? '';

    if ($saleType === '') {

        $salePrice = null;
        $saleStart = null;
        $saleEnd = null;
        $recurringDay = null;
        $recurringWeek = null;

    } elseif ($saleType === 'One-time') {

        if ($salePrice === null || $salePrice <= 0) {

            $_SESSION['error'] =
                'Please enter a valid sale price.';

        } elseif ($saleStart === '' || $saleEnd === '') {

            $_SESSION['error'] =
                'Please select a sale start and end date.';

        } elseif ($saleEnd < $saleStart) {

            $_SESSION['error'] =
                'The sale end date cannot be before the start date.';

        } else {

            $recurringDay = null;
            $recurringWeek = null;
        }

    } elseif ($saleType === 'Recurring') {

        if ($salePrice === null || $salePrice <= 0) {

            $_SESSION['error'] =
                'Please enter a valid sale price.';

        } elseif ($recurringDay === '' || $recurringWeek === '') {

            $_SESSION['error'] =
                'Please select the recurring day and occurrence.';

        } else {

            $saleStart = null;
            $saleEnd = null;
        }

    } else {

        $_SESSION['error'] =
            'Please select a valid sale type.';
    }

    if (
        !isset($_SESSION['error']) &&
        (
            $title === '' ||
            $price <= 0 ||
            !in_array($type, ['New', 'Used'], true) ||
            !in_array($status, ['Available', 'Reserved', 'Sold'], true) ||
            $location === '' ||
            $categoryID <= 0
        )
    ) {
        $_SESSION['error'] =
            'Please complete all listing details.';

    } elseif (
        !isset($_SESSION['error']) &&
        !$listingModel->locationExists($location)
    ) {

        $_SESSION['error'] =
            'Please select a location from the available locations.';

    } else {

        $imageURL = $listing['imageURL'] ?? '';

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {

                $_SESSION['error'] =
                    'The image could not be uploaded.';

            } else {

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif'
                ];

                $mimeType =
                    mime_content_type($file['tmp_name']);

                if (!isset($allowedTypes[$mimeType])) {

                    $_SESSION['error'] =
                        'Please upload a JPG, PNG, WEBP or GIF image.';

                } elseif ($file['size'] > 5 * 1024 * 1024) {

                    $_SESSION['error'] =
                        'The image must be smaller than 5 MB.';

                } else {

                    $uploadDirectory =
                        __DIR__ . '/../uploads/';

                    if (!is_dir($uploadDirectory)) {

                        mkdir(
                            $uploadDirectory,
                            0755,
                            true
                        );
                    }

                    $filename =
                        uniqid('listing_', true) .
                        '.' .
                        $allowedTypes[$mimeType];

                    $target =
                        $uploadDirectory . $filename;

                    if (
                        move_uploaded_file(
                            $file['tmp_name'],
                            $target
                        )
                    ) {

                        $imageURL =
                            '../uploads/' . $filename;

                    } else {

                        $_SESSION['error'] =
                            'The image could not be saved.';
                    }
                }
            }
        }

        if (!isset($_SESSION['error'])) {

            $listingModel->update(
                $listingID,
                $title,
                $description,
                $price,

                $salePrice,
                $saleStart,
                $saleEnd,
                $saleType !== '' ? $saleType : null,
                $recurringDay,
                $recurringWeek,

                $type,
                $imageURL,
                $location,
                $status,
                $categoryID
            );

            $_SESSION['success'] =
                'Listing updated successfully.';

            header(
                'Location: profile.php#listings'
            );

            exit();
        }
    }
}

$categories = (new Category())->getAll();
$locations = $listingModel->getLocations();

$pageTitle = 'Edit Listing';

require __DIR__ . '/../partials/header.php';
?>

<div class="page-heading">

    <a
        href="profile.php#listings"
        class="icon-btn"
        aria-label="Back to listings"
    >
        <i class="bi bi-arrow-left"></i>
    </a>

    <div>

        <h1>Edit Listing</h1>

        <p>
            Update your marketplace listing.
        </p>

    </div>

</div>

<?php if (!empty($_SESSION['error'])): ?>

    <div class="alert alert-danger auto-dismiss">

        <?= htmlspecialchars($_SESSION['error']) ?>

        <?php unset($_SESSION['error']); ?>

    </div>

<?php endif; ?>

<div class="card-soft form-card">

<form
    method="POST"
    enctype="multipart/form-data"
>

<input
    type="hidden"
    name="listingID"
    value="<?= $listingID ?>"
>

<div class="form-section">

    <label
        for="title"
        class="form-label"
    >
        Title
    </label>

    <input
        id="title"
        name="title"
        class="form-control"
        value="<?= htmlspecialchars($listing['title']) ?>"
        required
    >

</div>

<div class="form-section">

    <label
        for="description"
        class="form-label"
    >
        Description
    </label>

    <textarea
        id="description"
        name="description"
        class="form-control"
        rows="4"
    ><?= htmlspecialchars($listing['description'] ?? '') ?></textarea>

</div>

<div class="row g-3 form-section">

    <div class="col-md-6">

        <label
            for="price"
            class="form-label"
        >
            Price
        </label>

        <input
            id="price"
            name="price"
            type="number"
            step="0.01"
            min="0.01"
            class="form-control"
            value="<?= htmlspecialchars($listing['price']) ?>"
            required
        >

    </div>

    <div class="col-md-6">

        <label
            for="type"
            class="form-label"
        >
            Condition
        </label>

        <select
            id="type"
            name="type"
            class="form-select"
            required
        >

            <option
                value="New"
                <?= $listing['type'] === 'New'
                    ? 'selected'
                    : '' ?>
            >
                New
            </option>

            <option
                value="Used"
                <?= $listing['type'] === 'Used'
                    ? 'selected'
                    : '' ?>
            >
                Used
            </option>

        </select>

    </div>

</div>

<div class="form-section">

    <h5>
        Sale Price
    </h5>

    <p class="form-help">
        Optional. Set a temporary or recurring special price.
    </p>

    <div class="mb-3">

        <label
            for="saleType"
            class="form-label"
        >
            Sale Type
        </label>

        <select
            id="saleType"
            name="saleType"
            class="form-select"
        >

            <option
                value=""
                <?= empty($listing['saleType'])
                    ? 'selected'
                    : '' ?>
            >
                No Sale
            </option>

            <option
                value="One-time"
                <?= ($listing['saleType'] ?? '') === 'One-time'
                    ? 'selected'
                    : '' ?>
            >
                One-time Sale
            </option>

            <option
                value="Recurring"
                <?= ($listing['saleType'] ?? '') === 'Recurring'
                    ? 'selected'
                    : '' ?>
            >
                Recurring Monthly Sale
            </option>

        </select>

    </div>

    <div class="mb-3">

        <label
            for="salePrice"
            class="form-label"
        >
            Sale Price
        </label>

        <input
            id="salePrice"
            name="salePrice"
            type="number"
            step="0.01"
            min="0.01"
            class="form-control"
            value="<?= htmlspecialchars($listing['salePrice'] ?? '') ?>"
            placeholder="e.g. 40.00"
        >

    </div>

    <div
        id="oneTimeSaleFields"
        style="<?= ($listing['saleType'] ?? '') === 'One-time'
            ? ''
            : 'display:none;' ?>"
    >

        <div class="row g-3">

            <div class="col-md-6">

                <label
                    for="saleStart"
                    class="form-label"
                >
                    Sale Start
                </label>

                <input
                    id="saleStart"
                    name="saleStart"
                    type="date"
                    class="form-control"
                    value="<?= htmlspecialchars($listing['saleStart'] ?? '') ?>"
                >

            </div>


            <div class="col-md-6">

                <label
                    for="saleEnd"
                    class="form-label"
                >
                    Sale End
                </label>

                <input
                    id="saleEnd"
                    name="saleEnd"
                    type="date"
                    class="form-control"
                    value="<?= htmlspecialchars($listing['saleEnd'] ?? '') ?>"
                >

            </div>

        </div>

        <small class="form-help">
            Choose any period you want, such as one day,
            one week, one month or several months.
        </small>

    </div>

    <div
        id="recurringSaleFields"
        style="<?= ($listing['saleType'] ?? '') === 'Recurring'
            ? ''
            : 'display:none;' ?>"
    >

        <div class="row g-3">

            <div class="col-md-6">

                <label
                    for="recurringWeek"
                    class="form-label"
                >
                    Occurrence
                </label>

                <select
                    id="recurringWeek"
                    name="recurringWeek"
                    class="form-select"
                >

                    <option value="">
                        Select occurrence
                    </option>

                    <option
                        value="First"
                        <?= ($listing['recurringWeek'] ?? '') === 'First'
                            ? 'selected'
                            : '' ?>
                    >
                        First
                    </option>

                    <option
                        value="Second"
                        <?= ($listing['recurringWeek'] ?? '') === 'Second'
                            ? 'selected'
                            : '' ?>
                    >
                        Second
                    </option>

                    <option
                        value="Third"
                        <?= ($listing['recurringWeek'] ?? '') === 'Third'
                            ? 'selected'
                            : '' ?>
                    >
                        Third
                    </option>

                    <option
                        value="Fourth"
                        <?= ($listing['recurringWeek'] ?? '') === 'Fourth'
                            ? 'selected'
                            : '' ?>
                    >
                        Fourth
                    </option>

                    <option
                        value="Last"
                        <?= ($listing['recurringWeek'] ?? '') === 'Last'
                            ? 'selected'
                            : '' ?>
                    >
                        Last
                    </option>

                </select>

            </div>


            <div class="col-md-6">

                <label
                    for="recurringDay"
                    class="form-label"
                >
                    Day
                </label>

                <select
                    id="recurringDay"
                    name="recurringDay"
                    class="form-select"
                >

                    <option value="">
                        Select day
                    </option>

                    <option
                        value="Monday"
                        <?= ($listing['recurringDay'] ?? '') === 'Monday'
                            ? 'selected'
                            : '' ?>
                    >
                        Monday
                    </option>

                    <option
                        value="Tuesday"
                        <?= ($listing['recurringDay'] ?? '') === 'Tuesday'
                            ? 'selected'
                            : '' ?>
                    >
                        Tuesday
                    </option>

                    <option
                        value="Wednesday"
                        <?= ($listing['recurringDay'] ?? '') === 'Wednesday'
                            ? 'selected'
                            : '' ?>
                    >
                        Wednesday
                    </option>

                    <option
                        value="Thursday"
                        <?= ($listing['recurringDay'] ?? '') === 'Thursday'
                            ? 'selected'
                            : '' ?>
                    >
                        Thursday
                    </option>

                    <option
                        value="Friday"
                        <?= ($listing['recurringDay'] ?? '') === 'Friday'
                            ? 'selected'
                            : '' ?>
                    >
                        Friday
                    </option>

                    <option
                        value="Saturday"
                        <?= ($listing['recurringDay'] ?? '') === 'Saturday'
                            ? 'selected'
                            : '' ?>
                    >
                        Saturday
                    </option>

                    <option
                        value="Sunday"
                        <?= ($listing['recurringDay'] ?? '') === 'Sunday'
                            ? 'selected'
                            : '' ?>
                    >
                        Sunday
                    </option>

                </select>

            </div>

        </div>

        <small class="form-help">
            Example: Last + Friday = every last Friday of the month.
        </small>

    </div>

</div>

<div class="form-section">

    <label
        for="categoryID"
        class="form-label"
    >
        Category
    </label>

    <select
        id="categoryID"
        name="categoryID"
        class="form-select"
        required
    >

        <?php while ($category = $categories->fetch_assoc()): ?>

            <option
                value="<?= (int)$category['categoryID'] ?>"
                <?= (int)$listing['categoryID'] ===
                    (int)$category['categoryID']
                    ? 'selected'
                    : '' ?>
            >
                <?= htmlspecialchars($category['categoryName']) ?>
            </option>

        <?php endwhile; ?>

    </select>

</div>

<div class="form-section">

    <label
        for="location"
        class="form-label"
    >
        Location
    </label>

    <select
        id="location"
        name="location"
        class="form-select"
        required
    >

        <?php while ($locationRow = $locations->fetch_assoc()): ?>

            <option
                value="<?= htmlspecialchars($locationRow['location']) ?>"
                <?= $listing['location'] ===
                    $locationRow['location']
                    ? 'selected'
                    : '' ?>
            >
                <?= htmlspecialchars($locationRow['location']) ?>
            </option>

        <?php endwhile; ?>

    </select>

    <small class="form-help">
        Locations are loaded from existing marketplace listings.
    </small>

</div>

<div class="form-section">

    <label
        for="image"
        class="form-label"
    >
        Product Image
    </label>

    <input
        id="image"
        name="image"
        type="file"
        class="form-control"
        accept="image/jpeg,image/png,image/webp,image/gif"
    >

    <?php if (!empty($listing['imageURL'])): ?>

        <div class="current-image">

            <img
                src="<?= htmlspecialchars($listing['imageURL']) ?>"
                alt="Current listing image"
            >

            <span>
                Current image
            </span>

        </div>

    <?php endif; ?>

    <small class="form-help">
        Choose a new image to replace the current one.
    </small>

</div>

<div class="form-section">

    <label
        for="status"
        class="form-label"
    >
        Status
    </label>

    <select
        id="status"
        name="status"
        class="form-select"
    >

        <option
            value="Available"
            <?= $listing['status'] === 'Available'
                ? 'selected'
                : '' ?>
        >
            Available
        </option>

        <option
            value="Reserved"
            <?= $listing['status'] === 'Reserved'
                ? 'selected'
                : '' ?>
        >
            Reserved
        </option>

        <option
            value="Sold"
            <?= $listing['status'] === 'Sold'
                ? 'selected'
                : '' ?>
        >
            Sold
        </option>

    </select>

</div>

<button
    type="submit"
    class="btn-purple"
>
    <i class="bi bi-check2"></i>
    Save Changes
</button>

</form>

</div>

<script>

const saleType = document.getElementById('saleType');

const oneTimeSaleFields =
    document.getElementById('oneTimeSaleFields');

const recurringSaleFields =
    document.getElementById('recurringSaleFields');

function updateSaleFields() {

    if (saleType.value === 'One-time') {

        oneTimeSaleFields.style.display = '';

        recurringSaleFields.style.display = 'none';

    } else if (saleType.value === 'Recurring') {

        oneTimeSaleFields.style.display = 'none';

        recurringSaleFields.style.display = '';

    } else {

        oneTimeSaleFields.style.display = 'none';

        recurringSaleFields.style.display = 'none';
    }
}

saleType.addEventListener(
    'change',
    updateSaleFields
);

</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>