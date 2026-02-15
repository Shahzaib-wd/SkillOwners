<?php
require_once 'config.php';
require_once 'controllers/AgencyController.php';
require_once 'models/AgencyInvitation.php';
require_once 'models/AgencyMember.php';
require_once 'models/User.php';

echo "Agency Application System Verification Script\n";
echo "===========================================\n\n";

$agencyController = new AgencyController();
$invitationModel = new AgencyInvitation();
$memberModel = new AgencyMember();
$userModel = new User();

// 1. Setup - We need an agency and a freelancer
// For testing, we'll try to find existing ones or use dummy IDs if we were in a fully controlled env
// Since we are on a live system, we'll just verify the methods exist and logic is sound via dry-run or unit check if possible
// But here, I'll just check if the methods are callable and return expected errors for non-existent IDs.

echo "Testing Permission Checks...\n";
$unauthorizedResult = $agencyController->approveApplication(9999, 9999, 8888);
if (!$unauthorizedResult['success'] && strpos($unauthorizedResult['message'], 'permission') !== false) {
    echo "✅ Permission check working: Unauthorized user blocked.\n";
} else {
    echo "❌ Permission check failed or returned unexpected message: " . $unauthorizedResult['message'] . "\n";
}

echo "\nTesting Application Rejection (Non-existent ID)...\n";
// Use a real agency ID if we can find one, otherwise 1 is a common fallback
$agencyId = 1; 
$result = $agencyController->rejectApplication(9999, $agencyId, $agencyId);
if (!$result['success'] && strpos($result['message'], 'not found') !== false) {
    echo "✅ Rejection logic working: Non-existent application handled.\n";
} else {
    echo "❌ Rejection logic returned unexpected result: " . $result['message'] . "\n";
}

echo "\nVerification of File Changes:\n";
$filesToCheck = [
    'models/AgencyInvitation.php',
    'controllers/AgencyController.php',
    'dashboard/agency/agency_actions.php',
    'dashboard/agency/applications.php',
    'dashboard/agency/index.php',
    'views/partials/sidebar.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        echo "✅ File exists: $file\n";
    } else {
        echo "❌ File MISSING: $file\n";
    }
}

echo "\nDone.\n";
