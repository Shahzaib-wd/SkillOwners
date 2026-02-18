<?php
require_once 'config.php';
require_once 'models/User.php';

requireLogin();

$userModel = new User();
$currentUserId = $_SESSION['user_id'];
$currentRole = $_SESSION['user_role'] ?? 'buyer';

// Only allow switching between 'buyer' and 'freelancer'
// Agency roles are handled differently (usually fixed or requires specific setup)
if ($currentRole === 'agency' || $currentRole === 'admin') {
    showError("Your current role does not support quick switching.");
    redirect('/dashboard/' . $currentRole);
}

$newRole = ($currentRole === 'buyer') ? 'freelancer' : 'buyer';

if ($userModel->update($currentUserId, ['role' => $newRole])) {
    $_SESSION['user_role'] = $newRole;
    showSuccess("Successfully switched to " . ucfirst($newRole) . " mode.");
    
    // Redirect to the new dashboard
    redirect('/dashboard/' . $newRole);
} else {
    showError("Failed to switch profile. Please try again.");
    redirect('/dashboard/' . $currentRole);
}
