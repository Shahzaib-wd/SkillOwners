<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/AgencyInvitation.php';
require_once '../../models/AgencyMember.php';

$userId = $_SESSION['user_id'];
$invitationModel = new AgencyInvitation();
$memberModel = new AgencyMember();

$pendingInvitations = $invitationModel->getUserPendingInvitations($_SESSION['user_email'] ?? '');
$myAgencies = $memberModel->getUserAgencies($userId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Agencies</h1>
            <p class="text-muted">Manage your agency memberships and invitations</p>
        </div>

        <?php if (!empty($pendingInvitations)): ?>
            <div class="mb-5">
                <h2 class="h5 mb-3"><i class="fas fa-envelope-open-text text-primary"></i> Pending Invitations</h2>
                <div class="row">
                    <?php foreach ($pendingInvitations as $invitation): ?>
                        <div class="col-md-6 mb-3">
                            <div class="dashboard-card" style="border-left: 4px solid var(--primary);">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="h6 font-weight-700 mb-1"><?php echo htmlspecialchars($invitation['agency_name']); ?></h3>
                                        <p class="text-muted small mb-0">Role Offered: <strong><?php echo ucfirst($invitation['agency_role']); ?></strong></p>
                                    </div>
                                    <span class="badge-warning user-role">Pending</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo SITE_URL; ?>/dashboard/agency/accept_invitation.php?token=<?php echo $invitation['token']; ?>" class="btn btn-primary btn-sm px-4">View Invitation</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="h5 mb-3"><i class="fas fa-building text-primary"></i> Joined Agencies</h2>
        <?php if (empty($myAgencies)): ?>
            <div class="dashboard-card text-center py-5">
                <div class="stat-icon info mb-3 mx-auto">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h3>No Agency Memberships</h3>
                <p class="text-muted">You haven't joined any agencies yet. Agencies can invite you to handle their client projects!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($myAgencies as $agency): ?>
                    <div class="col-md-6 mb-3">
                        <div class="dashboard-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon info" style="width: 40px; height: 40px; font-size: 1rem;">
                                        <?php echo strtoupper(substr($agency['agency_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h3 class="h6 font-weight-700 mb-0"><?php echo htmlspecialchars($agency['agency_name']); ?></h3>
                                        <span class="text-muted small">Joined <?php echo date('M Y', strtotime($agency['joined_at'])); ?></span>
                                    </div>
                                </div>
                                <span class="badge-freelancer user-role"><?php echo ucfirst($agency['agency_role']); ?></span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline" onclick="openAgencyChat(<?php echo $agency['agency_id']; ?>)">
                                    <i class="fas fa-comments"></i> Open Team Chat
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
function openAgencyChat(agencyId) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

    ajaxRequest('<?php echo SITE_URL; ?>/chat_api.php', 'POST', {
        action: 'get_agency_conversation',
        agency_id: agencyId
    }).then(data => {
        if (data.success && data.conversation_id) {
            window.location.href = '<?php echo SITE_URL; ?>/chat.php?conversation_id=' + data.conversation_id;
        } else {
            alert('Failed to open chat: ' + (data.message || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }).catch(error => {
        console.error('Error opening agency chat:', error);
        alert('Error opening chat. Check console for details.');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>

<?php include '../../views/partials/footer.php'; ?>
