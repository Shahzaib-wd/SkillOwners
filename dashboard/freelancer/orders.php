<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Order.php';
$userId = $_SESSION['user_id'];
$orderModel = new Order();
$orders = $orderModel->findBySellerId($userId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Orders</h1>
            <p class="text-muted">Track and manage your service sales</p>
        </div>

        <div class="dashboard-card">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon info mb-3 mx-auto">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h3>No Orders Yet</h3>
                    <p class="text-muted">When buyers purchase your gigs, they will appear here.</p>
                </div>
            <?php else: ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Gig</th>
                                <th>Buyer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><span class="font-weight-600 text-primary">#<?php echo $order['id']; ?></span></td>
                                        <td><?php echo htmlspecialchars($order['gig_title']); ?></td>
                                        <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                        <td><span class="font-weight-700">$<?php echo number_format($order['amount'], 2); ?></span></td>
                                        <td>
                                            <span class="badge-<?php echo $order['status'] === 'completed' ? 'success' : 'primary'; ?> user-role">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span></td>
                                        <td class="text-right">
                                            <div class="d-flex justify-content-end gap-2">
                                                <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
                                                    <?php if (!$order['seller_confirmed']): ?>
                                                        <form action="<?php echo SITE_URL; ?>/dashboard/update_order_status.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-check"></i> Mark as Done
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark small" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; border-radius: 2rem;">
                                                            <i class="fas fa-hourglass-half me-1"></i> Waiting for Buyer
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <a href="<?php echo SITE_URL; ?>/chat.php?receiver_id=<?php echo $order['buyer_id']; ?>" class="btn btn-sm btn-outline">
                                                    <i class="fas fa-comment"></i> Contact
                                                </a>
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

<?php include '../../views/partials/footer.php'; ?>
