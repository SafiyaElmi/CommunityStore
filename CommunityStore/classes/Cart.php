<?php
require_once __DIR__ . '/../config/database.php';

class Cart
{
    public function create($userID)
    {
        global $conn;
        $stmt = $conn->prepare('INSERT INTO cart (userID) VALUES (?) ON DUPLICATE KEY UPDATE userID = VALUES(userID)');
        $stmt->bind_param('i', $userID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAll()
    {
        global $conn;
        return $conn->query('SELECT c.cartID, c.userID, u.firstName, u.lastName
                             FROM cart c INNER JOIN `user` u ON c.userID = u.userID');
    }

    public function getById($cartID)
    {
        global $conn;
        $stmt = $conn->prepare('SELECT * FROM cart WHERE cartID = ?');
        $stmt->bind_param('i', $cartID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function getByUser($userID)
    {
        global $conn;
        $stmt = $conn->prepare('SELECT * FROM cart WHERE userID = ? LIMIT 1');
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function update($cartID, $userID)
    {
        global $conn;
        $stmt = $conn->prepare('UPDATE cart SET userID = ? WHERE cartID = ?');
        $stmt->bind_param('ii', $userID, $cartID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete($cartID)
    {
        global $conn;
        $stmt = $conn->prepare('DELETE FROM cart WHERE cartID = ?');
        $stmt->bind_param('i', $cartID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>