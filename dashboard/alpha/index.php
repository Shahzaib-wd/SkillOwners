<?php
require_once '../../config.php';
requireLogin();

// if (getUserRole() !== 'admin') {
//     redirect('/dashboard/' . getUserRole() . '.php');
// }

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

    /* Using global style.css for dashboard elements */
</style>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="text-muted">Welcome back, Owner. Here is your system overview.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid mb-5">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo number_format($userStats['total_users'] ?? 0); ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo number_format($gigStats['total_gigs'] ?? 0); ?></span>
                    <span class="stat-label">Active Gigs</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo number_format($orderStats['completed_orders'] ?? 0); ?></span>
                    <span class="stat-label">Completed Orders</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo number_format($orderStats['total_orders'] ?? 0); ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Managed Agencies -->
            <div class="col-lg-8 mb-4">
                <div class="card-pro h-100">
                    <div class="card-header-pro">
                        <h3 class="card-title-pro">Managed Agencies</h3>
                        <span class="badge bg-primary">Admin Access</span>
                    </div>
                    <div class="card-body p-0">
                        <?php
                        $db = getDBConnection();
                        $managedSql = "SELECT u.id, u.full_name, u.email, u.profile_image, u.is_official 
                                     FROM users u 
                                     JOIN agency_members am ON u.id = am.agency_id 
                                     WHERE am.freelancer_id = :admin_id AND u.role = 'agency'";
                        $managedStmt = $db->prepare($managedSql);
                        $managedStmt->execute(['admin_id' => $_SESSION['user_id']]);
                        $managedAgencies = $managedStmt->fetchAll();

                        if (empty($managedAgencies)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <p>No managed agencies found.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($managedAgencies as $agency): ?>
                                    <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm" style="width: 40px; height: 40px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <?php if ($agency['profile_image']): ?>
                                                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $agency['profile_image']; ?>" class="w-100 h-100" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="fw-bold text-primary"><?php echo strtoupper(substr($agency['full_name'], 0, 1)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold d-flex align-items-center gap-1">
                                                    <?php echo htmlspecialchars($agency['full_name']); ?>
                                                    <?php if ($agency['is_official']): ?>
                                                        <i class="fas fa-check-circle official-badge-icon" title="Official Agency"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($agency['email']); ?></div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo SITE_URL; ?>/profile?id=<?php echo $agency['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View Profile</a>
                                            <a href="<?php echo SITE_URL; ?>/dashboard/agency" class="btn btn-sm btn-primary">Switch to Agency</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="card-pro h-100">
                    <div class="card-header-pro">
                        <h3 class="card-title-pro">Quick Actions</h3>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="users" class="quick-link-item">
                            <div class="quick-link-icon"><i class="fas fa-users-cog"></i></div>
                            <div>
                                <div class="fw-bold">User Management</div>
                                <small class="text-muted">Edit, Ban, or Delete users</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="gigs" class="quick-link-item">
                            <div class="quick-link-icon"><i class="fas fa-briefcase"></i></div>
                            <div>
                                <div class="fw-bold">Gig Moderation</div>
                                <small class="text-muted">Review and toggle services</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="orders" class="quick-link-item">
                            <div class="quick-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div>
                                <div class="fw-bold">Order Oversight</div>
                                <small class="text-muted">View all platform transactions</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                        <a href="reports" class="quick-link-item">
                            <div class="quick-link-icon"><i class="fas fa-flag"></i></div>
                            <div>
                                <div class="fw-bold">Abuse Reports</div>
                                <small class="text-muted">Handle user complaints</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
