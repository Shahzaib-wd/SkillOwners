<?php
require_once '../../config.php';
requireLogin();

// if (getUserRole() !== 'admin') {
//     redirect('/dashboard/' . getUserRole() . '.php');
// }

require_once '../../models/Order.php';
$orderModel = new Order();
$orders = $orderModel->findAll();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Order Management</h1>
            <p class="text-muted">Overview of all platform transactions and order statuses</p>
        </div>

        <div class="card-pro">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Order ID</th>
                            <th class="px-4 py-3">Gig</th>
                            <th class="px-4 py-3">Buyer</th>
                            <th class="px-4 py-3">Seller</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-4 text-muted">#<?php echo $order['id']; ?></td>
                                <td class="px-4 fw-bold"><?php echo htmlspecialchars($order['gig_title']); ?></td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle">
                                            <?php echo strtoupper(substr($order['buyer_name'], 0, 1)); ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($order['buyer_name']); ?></span>
                                    </div>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle">
                                            <?php echo strtoupper(substr($order['seller_name'], 0, 1)); ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($order['seller_name']); ?></span>
                                    </div>
                                </td>
                                <td class="px-4 fw-bold">$<?php echo number_format($order['amount'], 2); ?></td>
                                <td class="px-4">
                                    <?php 
                                    $statusClass = [
                                        'pending' => 'bg-warning bg-opacity-10 text-warning',
                                        'in_progress' => 'bg-info bg-opacity-10 text-info',
                                        'completed' => 'bg-success bg-opacity-10 text-success',
                                        'cancelled' => 'bg-danger bg-opacity-10 text-danger'
                                    ];
                                    $sClass = $statusClass[$order['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $sClass; ?>"><?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?></span>
                                </td>
                                <td class="px-4 text-muted small">
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="px-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="View Details (Coming Soon)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
