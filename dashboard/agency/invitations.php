<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/AgencyInvitation.php';
$agencyId = $_SESSION['user_id'];
$invitationModel = new AgencyInvitation();
$invitations = $invitationModel->getAgencyInvitations($agencyId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Invitations History</h1>
                <p class="text-muted">Track outbound invitations sent to freelancers</p>
            </div>
            <a href="<?php echo SITE_URL; ?>/dashboard/agency/invite_member.php" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send New Invitation
            </a>
        </div>

        <div class="dashboard-card">
            <?php if (empty($invitations)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon warning mb-3 mx-auto">
                        <i class="fas fa-envelope-open fa-2x"></i>
                    </div>
                    <h3>No Invitations Sent</h3>
                    <p class="text-muted">When you invite freelancers to join your agency, they will appear here.</p>
                </div>
            <?php else: ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Sent On</th>
                                <th>Expires On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invitations as $invitation): ?>
                                <tr>
                                    <td><span class="font-weight-600"><?php echo htmlspecialchars($invitation['email']); ?></span></td>
                                    <td><span class="badge-freelancer user-role"><?php echo ucfirst($invitation['agency_role']); ?></span></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-warning';
                                        if ($invitation['status'] === 'accepted') $statusClass = 'badge-success';
                                        elseif ($invitation['status'] === 'rejected') $statusClass = 'badge-danger';
                                        elseif ($invitation['status'] === 'expired') $statusClass = 'badge-secondary';
                                        ?>
                                        <span class="<?php echo $statusClass; ?> user-role">
                                            <?php echo ucfirst($invitation['status']); ?>
                                        </span>
                                    </td>
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($invitation['created_at'])); ?></span></td>
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($invitation['expires_at'])); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
