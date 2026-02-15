<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Order.php';
$agencyId = $_SESSION['user_id'];
$orderModel = new Order();
$orders = $orderModel->findByBuyerId($agencyId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Agency Orders</h1>
            <p class="text-muted">Track and manage your agency service purchases</p>
        </div>

        <div class="dashboard-card">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon info mb-3 mx-auto">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h3>No Orders Yet</h3>
                    <p class="text-muted">When you hire freelancers or purchase services, they will appear here.</p>
                    <a href="<?php echo SITE_URL; ?>/browse" class="btn btn-primary mt-3">Browse Services</a>
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
                                <th>Date</th>
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
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span></td>
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
