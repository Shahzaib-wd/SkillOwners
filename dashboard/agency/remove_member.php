<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/agency.php');
}

require_once '../../models/AgencyMember.php';
require_once '../../controllers/AgencyController.php';

$agencyId = $_SESSION['user_id'];
$memberModel = new AgencyMember();
$agencyController = new AgencyController();

// Check permission
if (!hasAgencyPermission($agencyId, 'remove_members')) {
    showError('You do not have permission to remove members.');
    redirect('/dashboard/agency.php');
}

// Get member ID
$memberId = $_GET['id'] ?? 0;

if (empty($memberId)) {
    showError('Invalid member ID.');
    redirect('/dashboard/agency.php');
}

// Get member details
$member = $memberModel->getMemberById($memberId);

if (!$member || $member['agency_id'] != $agencyId) {
    showError('Member not found.');
    redirect('/dashboard/agency.php');
}

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();
    
    $confirm = $_POST['confirm'] ?? '';
    
    if ($confirm === 'yes') {
        $result = $agencyController->removeMember($memberId, $agencyId, $_SESSION['user_id']);
        
        if ($result['success']) {
            showSuccess($result['message']);
        } else {
            showError($result['message']);
        }
    }
    
    redirect('/dashboard/agency.php#team');
}

include '../../views/partials/header.php';
?>

<style>
.confirm-page {
    padding: 5rem 0 3rem;
    min-height: 100vh;
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
}
.confirm-card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 2rem;
    border: 1px solid var(--border);
    max-width: 500px;
    text-align: center;
}
.warning-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #fee2e2;
    color: #991b1b;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.5rem;
}
.member-info {
    background: var(--muted);
    padding: 1rem;
    border-radius: var(--radius);
    margin: 1.5rem 0;
}
.btn-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    justify-content: center;
}
</style>

<div class="confirm-page">
    <div class="confirm-card">
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h2 class="h4 mb-3">Remove Team Member</h2>
        
        <p class="text-muted mb-3">
            Are you sure you want to remove this member from your agency?
        </p>
        
        <div class="member-info">
            <div class="mb-2">
                <strong><?php echo htmlspecialchars($member['full_name']); ?></strong>
            </div>
            <div class="text-muted" style="font-size: 0.875rem;">
                <?php echo htmlspecialchars($member['email']); ?>
            </div>
            <div class="mt-2">
                <span class="badge badge-info">
                    <?php echo ucfirst($member['agency_role']); ?>
                </span>
            </div>
        </div>
        
        <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i>
            This action cannot be undone. The member will lose access to agency resources.
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="btn-group">
                <button type="submit" name="confirm" value="yes" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Remove Member
                </button>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency.php#team" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../../views/partials/footer.php'; ?>
