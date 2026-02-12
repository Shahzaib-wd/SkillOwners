<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Order.php';
require_once '../../models/AgencyMember.php';
require_once '../../models/AgencyInvitation.php';
require_once '../../models/Message.php';

$agencyId = $_SESSION['user_id'];
$orderModel = new Order();
$memberModel = new AgencyMember();
$invitationModel = new AgencyInvitation();
$messageModel = new Message();

$agencyConversationId = $messageModel->getOrCreateAgencyConversation($agencyId);
$orders = $orderModel->findByBuyerId($agencyId);
$teamStats = $memberModel->getTeamStats($agencyId);
$invitations = $invitationModel->getAgencyInvitations($agencyId);
$pendingInvitationsCount = count(array_filter($invitations, function($inv) {
    return $inv['status'] === 'pending' && strtotime($inv['expires_at']) > time();
}));

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Agency Overview</h1>
            <p class="text-muted">Manage your agency team and collaborative projects</p>
        </div>

        <div class="dashboard-card mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none;">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div style="color: white;">
                        <h2 class="h4 font-weight-700 mb-1"><i class="fas fa-comments"></i> Team Chat</h2>
                        <p class="mb-0 opacity-75">Connect with your agency members in real-time</p>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/chat.php?conversation_id=<?php echo $agencyConversationId; ?>" class="btn btn-white px-4" style="background: white; color: #6366f1; font-weight: 700;">
                        Open Team Chat
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $teamStats['total_members'] ?? 0; ?></span>
                    <span class="stat-label">Team Members</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $teamStats['admins'] ?? 0; ?></span>
                    <span class="stat-label">Admins</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $pendingInvitationsCount; ?></span>
                    <span class="stat-label">Pending Invites</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo count($orders); ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="dashboard-card mb-4">
                    <h3 class="h5 mb-4">Team Composition</h3>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-user-shield text-danger"></i>
                                <span class="font-weight-600">Admins</span>
                            </div>
                            <span class="font-weight-700"><?php echo $teamStats['admins'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-user-tie text-primary"></i>
                                <span class="font-weight-600">Managers</span>
                            </div>
                            <span class="font-weight-700"><?php echo $teamStats['managers'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-user text-secondary"></i>
                                <span class="font-weight-600">Members</span>
                            </div>
                            <span class="font-weight-700"><?php echo $teamStats['members'] ?? 0; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="dashboard-card mb-4">
                    <h3 class="h5 mb-4">Agency Actions</h3>
                    <div class="d-grid gap-2">
                        <a href="team.php?action=invite" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Invite Team Member
                        </a>
                        <a href="team.php" class="btn btn-outline">
                            <i class="fas fa-users-cog"></i> Manage All Members
                        </a>
                        <hr>
                        <a href="invitations.php" class="btn btn-outline">
                            <i class="fas fa-history"></i> Invitation History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
