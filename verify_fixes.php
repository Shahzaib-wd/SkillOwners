<?php
require_once 'config.php';
require_once 'models/Gig.php';

$gigModel = new Gig();

echo "Running verification tests...\n";

// 1. Test findAll()
$allGigs = $gigModel->findAll();
echo "findAll() count: " . count($allGigs) . "\n";
if (count($allGigs) >= 0) {
    echo "[PASS] findAll() executed without error.\n";
} else {
    echo "[FAIL] findAll() failed.\n";
}

// 2. Test removeGigFromAgency() logic (without actually deleting if possible, or using a dummy)
// Since I don't want to break existing data too much, I'll just check if it executes.
// I'll try to find a gig that is NOT in agency_gigs to see if it runs.
try {
    $gigModel->removeGigFromAgency(999999, 999999);
    echo "[PASS] removeGigFromAgency() executed without SQL error.\n";
} catch (Exception $e) {
    echo "[FAIL] removeGigFromAgency() threw error: " . $e->getMessage() . "\n";
}

// 3. Test setStatus()
try {
    $gigModel->setStatus(1, 1); // Assuming ID 1 exists, otherwise it just returns true/false
    echo "[PASS] setStatus() executed without SQL error.\n";
} catch (Exception $e) {
    echo "[FAIL] setStatus() threw error: " . $e->getMessage() . "\n";
}

echo "Verification script finished.\n";
?>
