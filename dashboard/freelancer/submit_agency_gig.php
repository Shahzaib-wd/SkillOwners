<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole());
}

require_once '../../models/Gig.php';
require_once '../../models/AgencyMember.php';

$userId = $_SESSION['user_id'];
$gigModel = new Gig();
$memberModel = new AgencyMember();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agencyId = (int)($_POST['agency_id'] ?? 0);
    $gigId = (int)($_POST['gig_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    // Verify freelancer is member of this agency
    if (!$memberModel->isMember($agencyId, $userId)) {
        showError('You are not a member of this agency.');
        redirect('/dashboard/freelancer/agencies');
    }
    
    if ($action === 'contribute') {
        if ($gigId <= 0) {
            showError('Please select a gig to contribute.');
            redirect('/dashboard/freelancer/agencies');
        }
        
        $result = $gigModel->addGigToAgency($agencyId, $userId, $gigId);
        
        if ($result['success']) {
            showSuccess($result['message']);
        } else {
            showError($result['message']);
        }
    } elseif ($action === 'withdraw') {
        $existing = $gigModel->getFreelancerAgencyGig($agencyId, $userId);
        if ($existing) {
            $gigModel->removeGigFromAgency($existing['gig_id'], $agencyId);
            showSuccess('Gig withdrawn from agency.');
        } else {
            showError('No contributed gig found.');
        }
    }
    
    redirect('/dashboard/freelancer/agencies');
}

// If GET, redirect back
redirect('/dashboard/freelancer/agencies.php');
