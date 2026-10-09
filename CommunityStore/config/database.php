<?php

$host = "HOST_NAME";
$user = "USER_NAME";
$pass = "PASSWORD";
$dbname = "communitystore";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
