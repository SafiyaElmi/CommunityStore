<?php

$host = "HOST_NAME";
$user = "USER_NAME";
$pass = "PASSWORD";
$dbname = "DATABASE_NAME";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
