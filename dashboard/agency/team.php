<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/AgencyMember.php';
$agencyId = $_SESSION['user_id'];
$memberModel = new AgencyMember();
$members = $memberModel->getAgencyMembers($agencyId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Team Management</h1>
                <p class="text-muted">Manage your agency members and their permissions</p>
            </div>
                    <a href="<?php echo SITE_URL; ?>/dashboard/agency/invite_member" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Invite New Member
            </a>
        </div>

        <div class="dashboard-card">
            <?php if (empty($members)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon info mb-3 mx-auto">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3>Your Team is Empty</h3>
                    <p class="text-muted mb-4">You haven't added any members to your agency yet. Build your expert team now!</p>
                    <a href="<?php echo SITE_URL; ?>/dashboard/agency/invite_member.php" class="btn btn-primary">
                        Invite Your First Member
                    </a>
                </div>
            <?php else: ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                                <?php echo strtoupper(substr($member['full_name'], 0, 1)); ?>
                                            </div>
                                            <span class="font-weight-600"><?php echo htmlspecialchars($member['full_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'badge-freelancer';
                                        if ($member['agency_role'] === 'admin') $badgeClass = 'badge-agency';
                                        elseif ($member['agency_role'] === 'manager') $badgeClass = 'badge-info';
                                        ?>
                                        <span class="<?php echo $badgeClass; ?> user-role">
                                            <?php echo ucfirst($member['agency_role']); ?>
                                        </span>
                                    </td>
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($member['joined_at'])); ?></span></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <?php if ($member['user_id'] != $_SESSION['user_id']): ?>
                                                <a href="#" class="btn btn-sm btn-outline" title="Change Role">
                                                    <i class="fas fa-user-shield"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" title="Remove Member" onclick="confirmRemove(<?php echo $member['id']; ?>)">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small italic">You</span>
                                            <?php endif; ?>
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
function confirmRemove(id) {
    if (confirm('Are you sure you want to remove this member from your agency?')) {
        window.location.href = '<?php echo SITE_URL; ?>/dashboard/agency/remove_member.php?id=' + id;
    }
}
</script>

<?php include '../../views/partials/footer.php'; ?>
