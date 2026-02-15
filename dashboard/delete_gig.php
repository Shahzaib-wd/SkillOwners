<?php
require_once '../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole());
}

require_once '../models/Gig.php';

$gigId = (int)($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];

if ($gigId <= 0) {
    showError('Invalid gig selected.');
    redirect('/dashboard/freelancer/gigs');
}

$gigModel = new Gig();

// Verification and Deletion
if ($gigModel->delete($gigId, $userId)) {
    showSuccess('Gig deleted successfully.');
} else {
    showError('Failed to delete gig or unauthorized access.');
}

redirect('/dashboard/freelancer/gigs');
