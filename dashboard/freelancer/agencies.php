<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole());
}

require_once '../../models/AgencyInvitation.php';
require_once '../../models/AgencyMember.php';
require_once '../../models/Gig.php';

$userId = $_SESSION['user_id'];
$invitationModel = new AgencyInvitation();
$memberModel = new AgencyMember();
$gigModel = new Gig();

$pendingInvitations = $invitationModel->getUserPendingInvitations($_SESSION['user_email'] ?? '');
$myAgencies = $memberModel->getUserAgencies($userId);

// Get user's own gigs for the contribution modal
$myGigs = $gigModel->findByUserId($userId);

// Get contribution status per agency
$agencyContributions = [];
foreach ($myAgencies as $agency) {
    $agencyContributions[$agency['agency_id']] = $gigModel->getFreelancerAgencyGig($agency['agency_id'], $userId);
}

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Agencies</h1>
            <p class="text-muted">Manage your agency memberships and contributions</p>
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
                                    <a href="<?php echo SITE_URL; ?>/dashboard/agency/accept_invitation?token=<?php echo $invitation['token']; ?>" class="btn btn-primary btn-sm px-4">View Invitation</a>
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
                    <?php $contribution = $agencyContributions[$agency['agency_id']] ?? null; ?>
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
                            
                            <!-- Gig Contribution Section -->
                            <div class="agency-contribution-section">
                                <?php if ($contribution): ?>
                                    <div class="contribution-active">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <?php if ($contribution['status'] === 'approved'): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span class="font-weight-600" style="font-size: 0.85rem;">Approved — Active in Agency</span>
                                            <?php elseif ($contribution['status'] === 'pending'): ?>
                                                <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                                <span class="font-weight-600" style="font-size: 0.85rem;">Pending Agency Approval</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="contributed-gig-info">
                                            <span class="contributed-gig-title"><?php echo htmlspecialchars($contribution['gig_title']); ?></span>
                                        </div>
                                        <form method="POST" action="<?php echo SITE_URL; ?>/dashboard/freelancer/submit_agency_gig" 
                                              onsubmit="return confirm('Withdraw this gig from the agency?');" class="mt-2">
                                            <input type="hidden" name="agency_id" value="<?php echo $agency['agency_id']; ?>">
                                            <input type="hidden" name="action" value="withdraw">
                                            <button type="submit" class="btn btn-sm btn-outline" style="font-size: 0.8rem;">
                                                <i class="fas fa-undo"></i> Withdraw Gig
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <?php if (!empty($myGigs)): ?>
                                        <div class="contribution-form">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-gift text-primary"></i>
                                                <span class="font-weight-600" style="font-size: 0.85rem;">Contribute a Gig</span>
                                            </div>
                                            <form method="POST" action="<?php echo SITE_URL; ?>/dashboard/freelancer/submit_agency_gig">
                                                <input type="hidden" name="agency_id" value="<?php echo $agency['agency_id']; ?>">
                                                <input type="hidden" name="action" value="contribute">
                                                <div class="d-flex gap-2">
                                                    <select name="gig_id" class="form-control form-control-sm" required style="font-size: 0.85rem;">
                                                        <option value="">Select a gig...</option>
                                                        <?php foreach ($myGigs as $gig): ?>
                                                            <?php if ($gig['is_active']): ?>
                                                                <option value="<?php echo $gig['id']; ?>"><?php echo htmlspecialchars($gig['title']); ?> ($<?php echo number_format($gig['price'], 2); ?>)</option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary" style="white-space: nowrap;">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted small">
                                            <i class="fas fa-info-circle"></i> Create a gig first to contribute to this agency.
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <hr style="margin: 1rem 0;">
                            
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

<style>
.agency-contribution-section {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0;
}

.contribution-active {
    position: relative;
}

.contributed-gig-info {
    background: white;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.contributed-gig-title {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--foreground);
}

.form-control-sm {
    padding: 6px 10px;
    font-size: 0.85rem;
    border-radius: 8px;
}
</style>

<script>
function openAgencyChat(agencyId) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

    ajaxRequest('<?php echo SITE_URL; ?>/chat_api', 'POST', {
        action: 'get_agency_conversation',
        agency_id: agencyId
    }).then(data => {
        if (data.success && data.conversation_id) {
            window.location.href = '<?php echo SITE_URL; ?>/chat?conversation_id=' + data.conversation_id;
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
