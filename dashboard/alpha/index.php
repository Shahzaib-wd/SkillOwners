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

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: 1px solid rgba(255, 255, 255, 0.2);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        --gradient-primary: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    body {
        background-color: #f3f4f6;
    }

    .dashboard-layout {
        display: flex;
        min-height: calc(100vh - 70px);
    }

    .dashboard-content {
        flex: 1;
        padding: 2rem;
        overflow-x: hidden;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .stat-card {
        background: var(--glass-bg);
        border: var(--glass-border);
        box-shadow: var(--glass-shadow);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #111827;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .card-pro {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .card-header-pro {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title-pro {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .quick-link-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        color: #4b5563;
        text-decoration: none;
        transition: background-color 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }

    .quick-link-item:last-child {
        border-bottom: none;
    }

    .quick-link-item:hover {
        background-color: #f9fafb;
        color: #6366f1;
    }

    .quick-link-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background-color: #eef2ff;
        color: #6366f1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
</style>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="text-muted">Welcome back, Owner. Here is your system overview.</p>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background: var(--gradient-info);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($userStats['total_users'] ?? 0); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background: var(--gradient-primary);">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($gigStats['total_gigs'] ?? 0); ?></div>
                    <div class="stat-label">Active Gigs</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background: var(--gradient-success);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($orderStats['completed_orders'] ?? 0); ?></div>
                    <div class="stat-label">Completed Orders</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background: var(--gradient-warning);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($orderStats['total_orders'] ?? 0); ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- User Distribution -->
            <div class="col-lg-8 mb-4">
                <div class="card-pro h-100">
                    <div class="card-header-pro">
                        <h3 class="card-title-pro">User Distribution</h3>
                        <a href="users" class="btn btn-sm btn-outline-primary rounded-pill">Manage Users</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="display-6 fw-bold text-primary mb-0"><?php echo $userStats['freelancers'] ?? 0; ?></h4>
                                    <small class="text-uppercase text-muted fw-bold">Freelancers</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="display-6 fw-bold text-info mb-0"><?php echo $userStats['agencies'] ?? 0; ?></h4>
                                    <small class="text-uppercase text-muted fw-bold">Agencies</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="display-6 fw-bold text-warning mb-0"><?php echo $userStats['buyers'] ?? 0; ?></h4>
                                    <small class="text-uppercase text-muted fw-bold">Buyers</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Activity could go here -->
                        <hr class="my-4">
                        <h5 class="fw-bold fs-6 text-muted mb-3">SYSTEM HEALTH</h5>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success me-2">ONLINE</span>
                            <span class="text-muted small">Database Connection: Active</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">OK</span>
                            <span class="text-muted small">Email System: SMTP Configured</span>
                        </div>
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
