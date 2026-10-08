<?php
require_once __DIR__ . '/../config/database.php';

class CartItem
{
    public function create($quantity, $cartID, $listingID)
    {
        global $conn;
        $quantity = max(1, (int)$quantity);
        $stmt = $conn->prepare('INSERT INTO cartitem (quantity, cartID, listingID)
                                VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)');
        $stmt->bind_param('iii', $quantity, $cartID, $listingID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAll()
    {
        global $conn;
        return $conn->query('SELECT ci.*, l.title, l.price FROM cartitem ci INNER JOIN listing l ON ci.listingID = l.listingID');
    }

    public function getById($cartItemID)
    {
        global $conn;
        $stmt = $conn->prepare('SELECT ci.*, l.title, l.price, l.imageURL, l.status
                                FROM cartitem ci INNER JOIN listing l ON ci.listingID = l.listingID
                                WHERE ci.cartItemID = ?');
        $stmt->bind_param('i', $cartItemID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function getByCart($cartID)
    {
        global $conn;
        $stmt = $conn->prepare('SELECT ci.*, l.title, l.price, l.imageURL, l.status, l.userID AS sellerID
                                FROM cartitem ci INNER JOIN listing l ON ci.listingID = l.listingID
                                WHERE ci.cartID = ? ORDER BY ci.cartItemID ASC');
        $stmt->bind_param('i', $cartID);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getByCartAndListing($cartID, $listingID)
    {
        global $conn;
        $stmt = $conn->prepare('SELECT * FROM cartitem WHERE cartID = ? AND listingID = ?');
        $stmt->bind_param('ii', $cartID, $listingID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function update($cartItemID, $quantity)
    {
        global $conn;
        $quantity = (int)$quantity;
        if ($quantity < 1) return $this->delete($cartItemID);
        $stmt = $conn->prepare('UPDATE cartitem SET quantity = ? WHERE cartItemID = ?');
        $stmt->bind_param('ii', $quantity, $cartItemID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateForUser($cartItemID, $userID, $quantity)
    {
        global $conn;
        $quantity = (int)$quantity;
        if ($quantity < 1) return $this->deleteForUser($cartItemID, $userID);
        $stmt = $conn->prepare('UPDATE cartitem ci
                                INNER JOIN cart c ON ci.cartID = c.cartID
                                SET ci.quantity = ?
                                WHERE ci.cartItemID = ? AND c.userID = ?');
        $stmt->bind_param('iii', $quantity, $cartItemID, $userID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete($cartItemID)
    {
        global $conn;
        $stmt = $conn->prepare('DELETE FROM cartitem WHERE cartItemID = ?');
        $stmt->bind_param('i', $cartItemID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteForUser($cartItemID, $userID)
    {
        global $conn;
        $stmt = $conn->prepare('DELETE ci FROM cartitem ci INNER JOIN cart c ON ci.cartID = c.cartID
                                WHERE ci.cartItemID = ? AND c.userID = ?');
        $stmt->bind_param('ii', $cartItemID, $userID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>