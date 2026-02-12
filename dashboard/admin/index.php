<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'admin') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/User.php';
require_once '../../models/Gig.php';
require_once '../../models/Order.php';

$userModel = new User();
$gigModel = new Gig();
$orderModel = new Order();

$userStats = $userModel->getStats();
$gigStats = $gigModel->getStats();
$orderStats = $orderModel->getStats();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">System Administration</h1>
            <p class="text-muted">Global overview and system-wide management</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $userStats['total_users'] ?? 0; ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $gigStats['total_gigs'] ?? 0; ?></span>
                    <span class="stat-label">Total Gigs</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo number_format($orderStats['total_revenue'] ?? 0, 0); ?></span>
                    <span class="stat-label">Revenue ($)</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo $orderStats['total_orders'] ?? 0; ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="dashboard-card mb-4">
                    <h3 class="h5 mb-4">User Distribution</h3>
                    <div class="row g-4">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h4 class="h2 font-weight-700 mb-1 text-primary"><?php echo $userStats['freelancers'] ?? 0; ?></h4>
                                <span class="text-muted small">Freelancers</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h4 class="h2 font-weight-700 mb-1 text-secondary"><?php echo $userStats['agencies'] ?? 0; ?></h4>
                                <span class="text-muted small">Agencies</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h4 class="h2 font-weight-700 mb-1 text-danger"><?php echo $userStats['admins'] ?? 0; ?></h4>
                                <span class="text-muted small">Administrators</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="dashboard-card mb-4">
                    <h3 class="h5 mb-4">Admin Quick Links</h3>
                    <div class="d-grid gap-2">
                        <a href="users.php" class="btn btn-outline text-left">
                            <i class="fas fa-user-cog"></i> User Management
                        </a>
                        <a href="gigs.php" class="btn btn-outline text-left">
                            <i class="fas fa-briefcase"></i> Gig Moderation
                        </a>
                        <a href="orders.php" class="btn btn-outline text-left">
                            <i class="fas fa-file-invoice-dollar"></i> Transaction Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
