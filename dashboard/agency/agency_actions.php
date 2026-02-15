<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AgencyController.php';

// Verify login
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$action = $_POST['action'] ?? '';
$targetId = $_POST['target_id'] ?? 0;
$userId = $_SESSION['user_id'];
$userRole = getUserRole();

$agencyController = new AgencyController();

switch ($action) {
    case 'invite':
        if ($userRole !== 'agency') {
            echo json_encode(['success' => false, 'message' => 'Only agencies can invite members']);
            exit;
        }
        // Default to 'member' role for quick invitations
        $result = $agencyController->inviteMemberById($userId, $targetId, 'member', $userId);
        break;
        
    case 'apply':
        if ($userRole !== 'freelancer') {
            echo json_encode(['success' => false, 'message' => 'Only freelancers can apply to agencies']);
            exit;
        }
        $result = $agencyController->applyToAgency($targetId, $userId);
        break;
        
    case 'approve_application':
        if ($userRole !== 'agency' && !$agencyController->isAgencyMemberOrOwner($targetId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        // In this case targetId is agencyId, we need application_id as well
        $applicationId = $_POST['application_id'] ?? 0;
        $agencyId = $_POST['agency_id'] ?? $userId;
        $result = $agencyController->approveApplication($applicationId, $agencyId, $userId);
        break;

    case 'reject_application':
        if ($userRole !== 'agency' && !$agencyController->isAgencyMemberOrOwner($targetId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        $applicationId = $_POST['application_id'] ?? 0;
        $agencyId = $_POST['agency_id'] ?? $userId;
        $result = $agencyController->rejectApplication($applicationId, $agencyId, $userId);
        break;
        
    default:
        $result = ['success' => false, 'message' => 'Invalid action'];
        break;
}

header('Content-Type: application/json');
echo json_encode($result);
