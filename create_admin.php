<?php
require_once 'config.php';
require_once 'models/User.php';

echo "Creating Admin User...\n";

$userModel = new User();

// Admin credentials
$fullName = "Super Admin";
$email = "admin@skillowners.com";
$password = "admin123";
$role = "admin";

// Check if already exists
if ($userModel->emailExists($email)) {
    echo "Admin user already exists!\n";
    
    // Optional: Reset password if it exists? 
    // For now just exit to avoid overwriting if they changed it
    exit;
}

if ($userModel->create($fullName, $email, $password, $role)) {
    echo "Admin user created successfully!\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
} else {
    echo "Failed to create admin user.\n";
}
