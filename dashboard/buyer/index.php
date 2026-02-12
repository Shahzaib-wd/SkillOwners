<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'buyer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Order.php';
$userId = $_SESSION['user_id'];
$orderModel = new Order();
$orders = $orderModel->findByBuyerId($userId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Buyer Dashboard</h1>
            <p class="text-muted">Manage your service purchases and interactions</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value"><?php echo count($orders); ?></span>
                    <span class="stat-label">My Orders</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value">Active</span>
                    <span class="stat-label">Messenger</span>
                </div>
            </div>
        </div>

        <div class="dashboard-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 mb-0">My Recent Orders</h3>
                <a href="<?php echo SITE_URL; ?>/browse.php" class="btn btn-primary btn-sm">Browse Services</a>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted opacity-25 mb-3"></i>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                </div>
            <?php else: ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Gig</th>
                                <th>Seller</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><span class="font-weight-600 text-primary">#<?php echo $order['id']; ?></span></td>
                                    <td><?php echo htmlspecialchars($order['gig_title']); ?></td>
                                    <td><?php echo htmlspecialchars($order['seller_name']); ?></td>
                                    <td><span class="font-weight-700">$<?php echo number_format($order['amount'], 2); ?></span></td>
                                    <td>
                                        <span class="badge-primary user-role">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo SITE_URL; ?>/chat.php?receiver_id=<?php echo $order['seller_id']; ?>" class="btn btn-sm btn-outline">
                                            <i class="fas fa-comment"></i> Contact Seller
                                        </a>
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

<?php include '../../views/partials/footer.php'; ?>
