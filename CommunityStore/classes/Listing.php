<?php

require_once __DIR__ . '/../config/database.php';

class Listing
{
    public function create($title, $description, $price, $type, $imageURL, $location, $status, $userID, $categoryID)
    {
        global $conn;

        $datePosted = date('Y-m-d H:i:s');

        $stmt = $conn->prepare(
            "INSERT INTO listing
            (title, description, price, type, imageURL, location, datePosted, status, userID, categoryID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssdsssssii",
            $title,
            $description,
            $price,
            $type,
            $imageURL,
            $location,
            $datePosted,
            $status,
            $userID,
            $categoryID
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getAll($search = '', $categoryID = 0, $minPrice = null, $maxPrice = null, $location = '')
    {
        global $conn;

        $sql = "SELECT l.*, u.firstName, u.lastName, c.categoryName
                FROM listing l
                INNER JOIN `user` u ON l.userID = u.userID
                INNER JOIN category c ON l.categoryID = c.categoryID
                WHERE l.status <> 'Sold'";

        $params = [];
        $types = '';

        if ($search !== '') {
            $sql .= " AND (l.title LIKE ? OR l.description LIKE ?)";
            $searchValue = "%{$search}%";
            $params[] = $searchValue;
            $params[] = $searchValue;
            $types .= 'ss';
        }

        if ($categoryID > 0) {
            $sql .= " AND l.categoryID = ?";
            $params[] = $categoryID;
            $types .= 'i';
        }

        if ($minPrice !== '' && is_numeric($minPrice)) {
            $sql .= " AND l.price >= ?";
            $params[] = (float)$minPrice;
            $types .= 'd';
        }

        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $sql .= " AND l.price <= ?";
            $params[] = (float)$maxPrice;
            $types .= 'd';
        }

        if ($location !== '') {
            $sql .= " AND l.location = ?";
            $params[] = $location;
            $types .= 's';
        }

        $sql .= " ORDER BY l.datePosted DESC";

        $stmt = $conn->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getLocations()
    {
        global $conn;

        return $conn->query(
            "SELECT DISTINCT location
             FROM listing
             WHERE location IS NOT NULL
             AND TRIM(location) <> ''
             ORDER BY location ASC"
        );
    }

    public function locationExists($location)
    {
        global $conn;

        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS total
             FROM listing
             WHERE location = ?"
        );

        $stmt->bind_param('s', $location);
        $stmt->execute();

        $total = (int)$stmt->get_result()->fetch_assoc()['total'];

        $stmt->close();

        return $total > 0;
    }

    public function getById($listingID)
    {
        global $conn;

        $stmt = $conn->prepare(
            "SELECT l.*, u.firstName, u.lastName, c.categoryName
             FROM listing l
             INNER JOIN `user` u ON l.userID = u.userID
             INNER JOIN category c ON l.categoryID = c.categoryID
             WHERE l.listingID = ?"
        );

        $stmt->bind_param('i', $listingID);

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $result;
    }

    public function getByUser($userID)
    {
        global $conn;

        $stmt = $conn->prepare(
            "SELECT l.*, c.categoryName
             FROM listing l
             INNER JOIN category c ON l.categoryID = c.categoryID
             WHERE l.userID = ?
             ORDER BY l.datePosted DESC"
        );

        $stmt->bind_param('i', $userID);

        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }

    public function update(
        $listingID,
        $title,
        $description,
        $price,
        $salePrice,
        $saleStart,
        $saleEnd,
        $saleType,
        $recurringDay,
        $recurringWeek,
        $type,
        $imageURL,
        $location,
        $status,
        $categoryID
    ) {
        global $conn;

        $stmt = $conn->prepare(
            "UPDATE listing
             SET title = ?,
                 description = ?,
                 price = ?,
                 salePrice = ?,
                 saleStart = ?,
                 saleEnd = ?,
                 saleType = ?,
                 recurringDay = ?,
                 recurringWeek = ?,
                 type = ?,
                 imageURL = ?,
                 location = ?,
                 status = ?,
                 categoryID = ?
             WHERE listingID = ?"
        );

        $stmt->bind_param(
            "ssddsssssssssii",
            $title,
            $description,
            $price,
            $salePrice,
            $saleStart,
            $saleEnd,
            $saleType,
            $recurringDay,
            $recurringWeek,
            $type,
            $imageURL,
            $location,
            $status,
            $categoryID,
            $listingID
        );

        $result = $stmt->execute();

        $stmt->close();

        return $result;
    }

    public function getCurrentPrice($listing)
    {
        $normalPrice = (float)$listing['price'];

        if (
            empty($listing['salePrice']) ||
            empty($listing['saleType'])
        ) {
            return $normalPrice;
        }

        $salePrice = (float)$listing['salePrice'];

        if ($listing['saleType'] === 'One-time') {

            if (
                empty($listing['saleStart']) ||
                empty($listing['saleEnd'])
            ) {
                return $normalPrice;
            }

            $today = date('Y-m-d');

            if (
                $today >= $listing['saleStart'] &&
                $today <= $listing['saleEnd']
            ) {
                return $salePrice;
            }

            return $normalPrice;
        }

        if ($listing['saleType'] === 'Recurring') {

            if (
                empty($listing['recurringDay']) ||
                empty($listing['recurringWeek'])
            ) {
                return $normalPrice;
            }

            $today = new DateTime();

            if ($today->format('l') !== $listing['recurringDay']) {
                return $normalPrice;
            }

            $occurrence = (int)ceil(
                (int)$today->format('j') / 7
            );

            if ($listing['recurringWeek'] === 'Last') {

                $nextWeek = clone $today;
                $nextWeek->modify('+7 days');

                if (
                    $nextWeek->format('m') !==
                    $today->format('m')
                ) {
                    return $salePrice;
                }

                return $normalPrice;
            }

            $occurrenceNames = [
                1 => 'First',
                2 => 'Second',
                3 => 'Third',
                4 => 'Fourth'
            ];

            if (
                isset($occurrenceNames[$occurrence]) &&
                $occurrenceNames[$occurrence] ===
                $listing['recurringWeek']
            ) {
                return $salePrice;
            }
        }

        return $normalPrice;
    }

    public function updateStatus($listingID, $status)
    {
        global $conn;

        $stmt = $conn->prepare(
            "UPDATE listing
             SET status = ?
             WHERE listingID = ?"
        );

        $stmt->bind_param('si', $status, $listingID);

        $result = $stmt->execute();

        $stmt->close();

        return $result;
    }

    public function delete($listingID)
    {
        global $conn;

        $stmt = $conn->prepare(
            "DELETE FROM listing
             WHERE listingID = ?"
        );

        $stmt->bind_param('i', $listingID);

        $result = $stmt->execute();

        $stmt->close();

        return $result;
    }
}