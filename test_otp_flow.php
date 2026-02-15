<?php
require_once 'config.php';
require_once 'controllers/AuthController.php';

// Mock session
$_SESSION = [];

$auth = new AuthController();
$email = 'test@example.com'; // Use a dummy email

// Ensure user exists for testing (mocking user check if needed, but AuthController checks DB)
// For this test to really work, we need a user in DB. 
// We'll just test the method logic if we can, or rely on manual test if DB dependency is strict.

echo "Testing OTP Flow...\n";

// 1. Request OTP
echo "1. Requesting OTP for $email...\n";
// Note: This will fail if user doesn't exist in DB. 
// We'll create a dummy user first to be sure.
$db = getDBConnection();
$stmt = $db->prepare("INSERT IGNORE INTO users (full_name, email, password, role) VALUES ('Test User', :email, 'hash', 'buyer')");
$stmt->execute(['email' => $email]);

if ($auth->requestPasswordReset($email)) {
    echo "OTP Request Successful. Check DB for token.\n";
    
    // Retrieve OTP from DB to simulate user checking email
    $stmt = $db->prepare("SELECT token FROM password_resets WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $token = $stmt->fetchColumn();
    echo "Retrieved OTP from DB: $token\n";
    
    // 2. Verify OTP
    echo "2. Verifying OTP...\n";
    if ($auth->verifyOTP($email, $token)) {
        echo "OTP Verification Successful!\n";
        
        // 3. Reset Password
        echo "3. Resetting Password...\n";
        if ($auth->resetPassword($email, $token, "newpassword123")) {
            echo "Password Reset Successful!\n";
        } else {
            echo "Password Reset Failed.\n";
        }
    } else {
        echo "OTP Verification Failed.\n";
    }

} else {
    echo "OTP Request Failed (maybe email not found or DB error).\n";
}

// Cleanup
$db->prepare("DELETE FROM users WHERE email = :email")->execute(['email' => $email]);
$db->prepare("DELETE FROM password_resets WHERE email = :email")->execute(['email' => $email]);
echo "Cleanup done.\n";
