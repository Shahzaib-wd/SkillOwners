<?php
require_once '../../config.php';
require_once '../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/alpha');
}

$credential = $_POST['credential'] ?? '';
if (empty($credential)) {
    showError("Google authentication failed.");
    redirect('/alpha');
}

// Decode JWT
$parts = explode('.', $credential);
if (count($parts) !== 3) {
    showError("Invalid Google token.");
    redirect('/alpha');
}

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

if (!$payload || $payload['aud'] !== GOOGLE_CLIENT_ID) {
    showError("Google authentication verification failed.");
    redirect('/alpha');
}

$email = $payload['email'];
$admin_email = ADMIN_LOGIN_EMAIL;

if ($email !== $admin_email) {
    showError("Unauthorized access. Admin only.");
    redirect('/alpha');
}

$userModel = new User();
$user = $userModel->findByEmail($email);

if (!$user) {
    // If user doesn't exist, create it as admin
    $fullName = $payload['name'];
    $password = bin2hex(random_bytes(16));
    $userModel->create($fullName, $email, $password, 'admin');
    $user = $userModel->findByEmail($email);
}

// Update google_id if not set
if (empty($user['google_id'])) {
    $userModel->update($user['id'], ['google_id' => $payload['sub']]);
}

// Set admin session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_role'] = 'admin';
$_SESSION['login_time'] = time();

redirect('/alpha');
