<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/agency');
}

require_once '../../models/AgencyMember.php';
require_once '../../controllers/AgencyController.php';

$agencyId = $_SESSION['user_id'];
$agencyController = new AgencyController();

// Check permission
if (!hasAgencyPermission($agencyId, 'invite_members')) {
    showError('You do not have permission to invite members.');
    redirect('/dashboard/agency.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();
    
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'member');
    
    if (empty($email)) {
        showError('Email is required.');
    } else {
        $result = $agencyController->inviteMember($agencyId, $email, $role, $_SESSION['user_id']);
        
        if ($result['success']) {
            showSuccess($result['message']);
            redirect('/dashboard/agency#invitations');
        } else {
            showError($result['message']);
        }
    }
}

include '../../views/partials/header.php';
?>

<style>
.page-container {
    padding: 5rem 0 3rem;
    min-height: 100vh;
    background: var(--background);
}
.form-card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 2rem;
    border: 1px solid var(--border);
    max-width: 600px;
    margin: 0 auto;
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--foreground);
}
.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: var(--background);
    color: var(--foreground);
}
.form-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: var(--background);
    color: var(--foreground);
}
.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--muted-foreground);
}
.btn-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
.info-box {
    background: var(--muted);
    border-left: 4px solid var(--primary);
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: var(--radius);
}
</style>

<div class="page-container">
    <div class="container">
        <div class="mb-4">
            <a href="<?php echo SITE_URL; ?>/dashboard/agency" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="form-card">
            <h2 class="h3 mb-4">Invite Team Member</h2>
            
            <?php if ($error = getError()): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Only registered freelancers can be invited to your agency. 
                The invited user will receive a notification and must accept the invitation.
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="freelancer@example.com"
                        required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                    <small class="form-text">
                        Enter the email address of the freelancer you want to invite.
                    </small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="role">
                        <i class="fas fa-user-tag"></i> Assign Role *
                    </label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="member">Member - Basic access to team features</option>
                        <option value="manager">Manager - Can invite members and manage projects</option>
                        <?php if (isAgencyAdmin($agencyId)): ?>
                            <option value="admin">Admin - Full control over agency</option>
                        <?php endif; ?>
                    </select>
                    <small class="form-text">
                        <strong>Member:</strong> Can view team and create gigs<br>
                        <strong>Manager:</strong> Can invite members, create gigs, and manage orders<br>
                        <strong>Admin:</strong> Full control including removing members and changing roles
                    </small>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Invitation
                    </button>
                    <a href="<?php echo SITE_URL; ?>/dashboard/agency" class="btn btn-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../views/partials/footer.php'; ?>
