<?php
require_once '../../config.php';
requireLogin();

// if (getUserRole() !== 'admin') {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit;
// }

require_once '../../models/Gig.php';

$gigId = (int)($_POST['id'] ?? 0);
$active = isset($_POST['active']) ? (int)$_POST['active'] : 1;

if ($gigId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$gigModel = new Gig();

if ($gigModel->setStatus($gigId, $active)) {
    echo json_encode(['success' => true, 'message' => 'Gig status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update gig status']);
}
