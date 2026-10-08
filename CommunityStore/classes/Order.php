<?php
require_once __DIR__ . '/../config/database.php';
class Order {
    public function create($status,$userID) {
        global $conn; $orderDate=date('Y-m-d H:i:s');
        $stmt=$conn->prepare("INSERT INTO `order` (orderDate,status,userID) VALUES (?,?,?)");
        $stmt->bind_param("ssi",$orderDate,$status,$userID); return $stmt->execute();
    }
    public function getAll() {
        global $conn; return $conn->query("SELECT o.*,u.firstName,u.lastName,u.email FROM `order` o INNER JOIN `user` u ON o.userID=u.userID ORDER BY o.orderDate DESC");
    }
    public function getById($orderID) {
        global $conn; $stmt=$conn->prepare("SELECT o.*,u.firstName,u.lastName,u.email FROM `order` o INNER JOIN `user` u ON o.userID=u.userID WHERE o.orderID=?");
        $stmt->bind_param("i",$orderID);$stmt->execute();return $stmt->get_result()->fetch_assoc();
    }
    public function getByUser($userID) {
        global $conn; $stmt=$conn->prepare("SELECT * FROM `order` WHERE userID=? ORDER BY orderDate DESC");
        $stmt->bind_param("i",$userID);$stmt->execute();return $stmt->get_result();
    }
    public function updateStatus($orderID,$status) {
        global $conn; $stmt=$conn->prepare("UPDATE `order` SET status=? WHERE orderID=?");
        $stmt->bind_param("si",$status,$orderID);return $stmt->execute();
    }
    public function delete($orderID) {
        global $conn; $stmt=$conn->prepare("DELETE FROM `order` WHERE orderID=?");
        $stmt->bind_param("i",$orderID);return $stmt->execute();
    }
}
?>