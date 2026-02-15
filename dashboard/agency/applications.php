<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/AgencyInvitation.php';
require_once '../../models/User.php';

$agencyId = $_SESSION['user_id'];
$invitationModel = new AgencyInvitation();
$userModel = new User();

// Fetch all invitations/applications
$allInvitations = $invitationModel->getAgencyInvitations($agencyId);

// Filter for applications (where invited_by IS NOT the agency owner)
// In our current system, when a freelancer applies, invited_by is set to their own ID
$applications = array_filter($allInvitations, function($inv) use ($agencyId) {
    return $inv['invited_by'] != $agencyId && $inv['status'] === 'pending';
});

// For each application, get the freelancer details
foreach ($applications as &$app) {
    $freelancer = $userModel->findByEmail($app['email']);
    $app['freelancer_name'] = $freelancer['full_name'] ?? 'Unknown';
    $app['freelancer_id'] = $freelancer['id'] ?? 0;
}

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Hiring Applications</h1>
                <p class="text-muted">Review freelancers who want to join your agency</p>
            </div>
            <a href="invitations" class="btn btn-outline">
                <i class="fas fa-history"></i> Sent Invitations
            </a>
        </div>

        <div class="dashboard-card">
            <?php if (empty($applications)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon info mb-3 mx-auto">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                    <h3>No Pending Applications</h3>
                    <p class="text-muted">When freelancers apply to join your agency, they will appear here.</p>
                </div>
            <?php else: ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Freelancer</th>
                                <th>Requested Role</th>
                                <th>Applied On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm">
                                                <i class="fas fa-user-circle fa-2x opacity-25"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-600"><?php echo htmlspecialchars($app['freelancer_name']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($app['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge-freelancer user-role"><?php echo ucfirst($app['agency_role']); ?></span></td>
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($app['created_at'])); ?></span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-success action-btn" 
                                                    data-action="approve_application" 
                                                    data-id="<?php echo $app['id']; ?>">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger action-btn" 
                                                    data-action="reject_application" 
                                                    data-id="<?php echo $app['id']; ?>">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                            <a href="../../profile?id=<?php echo $app['freelancer_id']; ?>" class="btn btn-sm btn-outline" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> View Profile
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionBtns = document.querySelectorAll('.action-btn');
    
    actionBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            const action = this.dataset.action;
            const applicationId = this.dataset.id;
            const agencyId = '<?php echo $agencyId; ?>';
            
            if (!confirm(`Are you sure you want to ${action === 'approve_application' ? 'approve' : 'reject'} this application?`)) {
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            try {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('application_id', applicationId);
                formData.append('agency_id', agencyId);
                formData.append('target_id', agencyId); // for permission check in agency_actions.php
                
                const response = await fetch('agency_actions', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message || 'An error occurred');
                    this.disabled = false;
                    this.innerHTML = action === 'approve_application' ? '<i class="fas fa-check"></i> Approve' : '<i class="fas fa-times"></i> Reject';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                this.disabled = false;
                this.innerHTML = action === 'approve_application' ? '<i class="fas fa-check"></i> Approve' : '<i class="fas fa-times"></i> Reject';
            }
        });
    });
});
</script>

<?php include '../../views/partials/footer.php'; ?>
