<?php
require_once '../../config.php';
requireLogin();

require_once '../../models/AgencyInvitation.php';
require_once '../../models/User.php';

$invitationModel = new AgencyInvitation();
$userModel = new User();

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    showError('Invalid invitation link.');
    redirect('/dashboard/freelancer.php');
}

// Get invitation details
$invitation = $invitationModel->getByToken($token);

if (!$invitation) {
    showError('Invitation not found.');
    redirect('/dashboard/freelancer.php');
}

// Check if invitation is valid
if ($invitation['status'] !== 'pending') {
    showError('This invitation is no longer valid.');
    redirect('/dashboard/freelancer.php');
}

if (strtotime($invitation['expires_at']) < time()) {
    showError('This invitation has expired.');
    redirect('/dashboard/freelancer.php');
}

// Verify user is a freelancer
if (!isLoggedIn() || getUserRole() !== 'freelancer') {
    $_SESSION['redirect_after_login'] = SITE_URL . '/dashboard/agency/accept_invitation.php?token=' . $token;
    showError('Please log in as a freelancer to accept this invitation.');
    redirect('/login.php');
}

// Verify email matches
$currentUser = $userModel->findById($_SESSION['user_id']);
if ($currentUser['email'] !== $invitation['email']) {
    showError('This invitation is not for your account.');
    redirect('/dashboard/freelancer.php');
}

// Handle acceptance/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'accept') {
        $result = $invitationModel->accept($token, $_SESSION['user_id']);
        
        if ($result['success']) {
            showSuccess($result['message']);
        } else {
            showError($result['message']);
        }
        redirect('/dashboard/freelancer.php');
        
    } elseif ($action === 'reject') {
        $result = $invitationModel->reject($token, $_SESSION['user_id']);
        
        if ($result['success']) {
            showSuccess($result['message']);
        } else {
            showError($result['message']);
        }
        redirect('/dashboard/freelancer.php');
    }
}

include '../../views/partials/header.php';
?>

<style>
.invitation-page {
    padding: 5rem 0 3rem;
    min-height: 100vh;
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
}
.invitation-card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 3rem;
    border: 1px solid var(--border);
    max-width: 600px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.invitation-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    font-size: 2rem;
}
.agency-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 1rem;
}
.invitation-details {
    background: var(--muted);
    padding: 1.5rem;
    border-radius: var(--radius);
    margin: 2rem 0;
    text-align: left;
}
.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border);
}
.detail-row:last-child {
    border-bottom: none;
}
.btn-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    justify-content: center;
}
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
}
.badge-info { background: #dbeafe; color: #1e40af; }
.badge-danger { background: #fee2e2; color: #991b1b; }
</style>

<div class="invitation-page">
    <div class="invitation-card">
        <div class="invitation-icon">
            <i class="fas fa-users"></i>
        </div>
        
        <h1 class="h3 mb-3">Agency Invitation</h1>
        
        <p class="text-muted mb-4">
            You have been invited to join an agency team
        </p>
        
        <div class="agency-name">
            <?php echo htmlspecialchars($invitation['agency_name']); ?>
        </div>
        
        <div class="invitation-details">
            <div class="detail-row">
                <span><strong>Invited by:</strong></span>
                <span><?php echo htmlspecialchars($invitation['inviter_name']); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Your role:</strong></span>
                <span class="badge badge-info"><?php echo ucfirst($invitation['agency_role']); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Invitation sent:</strong></span>
                <span><?php echo date('M d, Y', strtotime($invitation['created_at'])); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Expires on:</strong></span>
                <span><?php echo date('M d, Y', strtotime($invitation['expires_at'])); ?></span>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>What this means:</strong> As a <strong><?php echo ucfirst($invitation['agency_role']); ?></strong>, 
            you will be able to collaborate with the agency team and contribute to agency projects.
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="btn-group">
                <button type="submit" name="action" value="accept" class="btn btn-primary">
                    <i class="fas fa-check"></i> Accept Invitation
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Decline
                </button>
            </div>
        </form>
        
        <p class="text-muted mt-4">
            <small>
                Not the right account? <a href="<?php echo SITE_URL; ?>/logout.php">Log out</a> and try again.
            </small>
        </p>
    </div>
</div>

<?php include '../../views/partials/footer.php'; ?>
