<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    public function create($firstName, $lastName, $email, $password, $userType = 'Student')
    {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO `user`
            (firstName,lastName,email,password,userType)
            VALUES (?,?,?,?,?)");
        if (!$stmt) return false;
        $stmt->bind_param("sssss", $firstName,$lastName,$email,$hashedPassword,$userType);
        $ok=$stmt->execute(); $stmt->close(); return $ok;
    }

    public function getAll() {
        global $conn;
        return $conn->query("SELECT userID,firstName,lastName,email,role,userType FROM `user` ORDER BY userID DESC");
    }

    public function getById($userID) {
        global $conn;
        $stmt=$conn->prepare("SELECT userID,firstName,lastName,email,role,userType FROM `user` WHERE userID=?");
        $stmt->bind_param("i",$userID); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByEmail($email) {
        global $conn;
        $stmt=$conn->prepare("SELECT * FROM `user` WHERE email=?");
        $stmt->bind_param("s",$email); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($userID,$firstName,$lastName,$email) {
        global $conn;
        $stmt=$conn->prepare("UPDATE `user` SET firstName=?,lastName=?,email=? WHERE userID=?");
        $stmt->bind_param("sssi",$firstName,$lastName,$email,$userID);
        return $stmt->execute();
    }

    public function updatePassword($userID,$password) {
        global $conn;
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $stmt=$conn->prepare("UPDATE `user` SET password=? WHERE userID=?");
        $stmt->bind_param("si",$hash,$userID); return $stmt->execute();
    }

    public function delete($userID) {
        global $conn;
        $stmt=$conn->prepare("DELETE FROM `user` WHERE userID=?");
        $stmt->bind_param("i",$userID); return $stmt->execute();
    }
}
?>