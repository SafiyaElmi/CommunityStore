<?php

session_start();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$first = trim($_POST['first_name'] ?? '');
$last = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$type = $_POST['user_type'] ?? '';
$password = $_POST['password'] ?? '';

$allowedTypes = ['Student', 'Vendor', 'Resident'];

if ($first === '' || $last === '' || !in_array($type, $allowedTypes, true)) {
    $_SESSION['error'] = 'Please complete all account details.';
    header('Location: register.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    header('Location: register.php');
    exit();
}

if ($type === 'Student' && !preg_match('/@cput\.ac\.za$/i', $email)) {
    $_SESSION['error'] = 'Students must register with an institutional @cput.ac.za email address.';
    header('Location: register.php');
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password must be at least 6 characters.';
    header('Location: register.php');
    exit();
}

$check = $conn->prepare(
    "SELECT userID FROM `user` WHERE email = ? LIMIT 1"
);

$check->bind_param('s', $email);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if ($exists) {
    $_SESSION['error'] = 'Email already exists.';
    header('Location: register.php');
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO `user` (firstName, lastName, email, password, userType)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param('sssss', $first, $last, $email, $hash, $type);

if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['success'] = 'Registration successful. You can now login.';
    header('Location: login.php');
    exit();
}

$stmt->close();
$_SESSION['error'] = 'Registration failed. Please try again.';
header('Location: register.php');
exit();
