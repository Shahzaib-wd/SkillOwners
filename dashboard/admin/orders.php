<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'admin') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Order.php';
$orderModel = new Order();
$orders = $orderModel->findAll();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Global Transactions</h1>
            <p class="text-muted">Monitor all platform orders and revenue</p>
        </div>

        <div class="dashboard-card">
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Gig</th>
                            <th>Buyer</th>
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
                                <td><span class="small"><?php echo htmlspecialchars($order['gig_title']); ?></span></td>
                                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['seller_name']); ?></td>
                                <td><span class="font-weight-700">$<?php echo number_format($order['amount'], 2); ?></span></td>
                                <td>
                                    <span class="badge-success user-role">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
