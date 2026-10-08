<?php
require_once __DIR__ . '/../config/database.php';
class BulletinPost {
    public function create($title,$content,$postType,$userID) {
        global $conn; $postDate=date('Y-m-d H:i:s');
        $stmt=$conn->prepare("INSERT INTO bulletin_post (title,content,postType,postDate,userID) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssssi",$title,$content,$postType,$postDate,$userID); return $stmt->execute();
    }
    public function getAll() {
        global $conn;
        return $conn->query("SELECT b.*,u.firstName,u.lastName FROM bulletin_post b INNER JOIN `user` u ON b.userID=u.userID ORDER BY b.postDate DESC");
    }
    public function delete($bulletinID,$userID) {
        global $conn; $stmt=$conn->prepare("DELETE FROM bulletin_post WHERE bulletinID=? AND userID=?");
        $stmt->bind_param("ii",$bulletinID,$userID); return $stmt->execute();
    }
}
?>