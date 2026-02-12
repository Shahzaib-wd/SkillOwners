<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Gig.php';
require_once '../../models/Project.php';
require_once '../../models/Order.php';
require_once '../../models/AgencyInvitation.php';

$userId = $_SESSION['user_id'];
$gigModel = new Gig();
$projectModel = new Project();
$orderModel = new Order();
$invitationModel = new AgencyInvitation();

$gigs = $gigModel->findByUserId($userId);
$projects = $projectModel->findByUserId($userId);
$orders = $orderModel->findBySellerId($userId);
$pendingInvitations = $invitationModel->getUserPendingInvitations($_SESSION['user_email'] ?? '');

$gigCount = count($gigs);
$projectCount = count($projects);
$orderCount = count($orders);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard Overview</h1>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        </div>

        <?php if (!empty($pendingInvitations)): ?>
            <div class="dashboard-card mb-4" style="border-left: 4px solid var(--primary); background: #f5f3ff;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon primary">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Agency Invitations</h5>
                        <p class="mb-0 text-muted">You have <?php echo count($pendingInvitations); ?> pending invitations to join agencies.</p>
                    </div>
                    <a href="agencies.php" class="btn btn-primary btn-sm ml-auto">View Invitations</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $gigCount; ?></span>
                    <span class="stat-label">Active Gigs</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $projectCount; ?></span>
                    <span class="stat-label">Portfolio Projects</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $orderCount; ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="dashboard-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 mb-0">Recent Orders</h3>
                        <a href="orders.php" class="btn btn-link btn-sm">View All</a>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No orders yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Buyer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                            <td>$<?php echo number_format($order['amount'], 2); ?></td>
                                            <td>
                                                <span class="badge-<?php echo $order['status']; ?> user-role">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="dashboard-card mb-4">
                    <h3 class="h5 mb-4">Quick Actions</h3>
                    <div class="d-grid gap-2">
                        <a href="gigs.php?action=create" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> Create New Gig
                        </a>
                        <a href="projects.php?action=add" class="btn btn-outline btn-block">
                            <i class="fas fa-folder-plus"></i> Add Portfolio Project
                        </a>
                        <hr>
                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $userId; ?>" class="btn btn-outline btn-block" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Public Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
