<?php
require_once 'config.php';
require_once 'models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login');
}

$credential = $_POST['credential'] ?? '';
if (empty($credential)) {
    showError("Google authentication failed.");
    redirect('/login');
}

// Decode JWT without library (header.payload.signature)
$parts = explode('.', $credential);
if (count($parts) !== 3) {
    showError("Invalid Google token.");
    redirect('/login');
}

$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

if (!$payload || $payload['aud'] !== GOOGLE_CLIENT_ID) {
    showError("Google authentication verification failed.");
    redirect('/login');
}

$googleId = $payload['sub'];
$email = $payload['email'];
$fullName = $payload['name'];
$profileImage = $payload['picture'] ?? null;

error_log("GOOGLE AUTH: googleId=$googleId, email=$email, name=$fullName, picture=$profileImage");


$userModel = new User();
$user = $userModel->findByGoogleId($googleId);
$isNewUser = false;

if (!$user) {
    // Check if user exists with this email but no google_id
    $user = $userModel->findByEmail($email);
    
    if ($user) {
        // Link existing account to Google
        $userModel->update($user['id'], ['google_id' => $googleId]);
        
        // If existing user has no profile image, fetch from Google
        if (empty($user['profile_image']) && $profileImage) {
            $imgData = @file_get_contents($profileImage);
            if ($imgData) {
                $fileName = uniqid() . '.jpg';
                if (file_put_contents(UPLOAD_DIR . $fileName, $imgData)) {
                    $userModel->update($user['id'], ['profile_image' => $fileName]);
                }
            }
        }
    } else {
        // Register new user (Default as buyer, will be changed on onboarding)
        $isNewUser = true;
        $password = bin2hex(random_bytes(16));
        if ($userModel->create($fullName, $email, $password, 'buyer')) {
            $user = $userModel->findByEmail($email);
            $userModel->update($user['id'], ['google_id' => $googleId]);
            
            // Save profile image from Google
            if ($profileImage) {
                $imgData = @file_get_contents($profileImage);
                if ($imgData) {
                    $fileName = uniqid() . '.jpg';
                    if (file_put_contents(UPLOAD_DIR . $fileName, $imgData)) {
                        $userModel->update($user['id'], ['profile_image' => $fileName]);
                    }
                }
            }
            
            showSuccess("Welcome to Skill Owners!");
        } else {
            showError("Registration failed.");
            redirect('/register');
        }
    }
}

// Re-fetch user to get the absolute latest data (including any profile_image we just saved)
$user = $userModel->findById($user['id']);

error_log("GOOGLE AUTH SESSION SET: id={$user['id']}, full_name={$user['full_name']}, role={$user['role']}, profile_image={$user['profile_image']}");

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_image'] = $user['profile_image'];
$_SESSION['login_time'] = time();

error_log("GOOGLE AUTH SESSION VERIFY: user_name={$_SESSION['user_name']}, user_image={$_SESSION['user_image']}, user_role={$_SESSION['user_role']}");


// New users go to onboarding to pick their role
if ($isNewUser) {
    redirect('/onboarding');
} else {
    redirect('/dashboard/' . $user['role']);
}

