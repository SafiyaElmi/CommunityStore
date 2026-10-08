<?php

session_start();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$userType = $_POST['user_type'] ?? '';

$allowedTypes = ['Student', 'Vendor', 'Resident'];

if (!in_array($userType, $allowedTypes, true)) {
    $_SESSION['error'] = 'Please select your account type.';
    header('Location: login.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['error'] = 'Please enter a valid email and password.';
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare(
    "SELECT userID, firstName, lastName, email, password, role, userType
     FROM `user`
     WHERE email = ?
     LIMIT 1"
);

$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Invalid email or password.';
    header('Location: login.php');
    exit();
}

if ($user['userType'] !== $userType) {
    $_SESSION['error'] = 'The selected account type does not match this account.';
    header('Location: login.php');
    exit();
}

session_regenerate_id(true);

$_SESSION['userID'] = (int)$user['userID'];
$_SESSION['firstName'] = $user['firstName'];
$_SESSION['lastName'] = $user['lastName'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['userType'] = $user['userType'];

header('Location: ../frontend/home.php');
exit();
