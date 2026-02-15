<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'buyer') {
    redirect('/dashboard/' . getUserRole());
}

require_once '../../models/Order.php';
require_once '../../models/Review.php';
$userId = $_SESSION['user_id'];
$orderModel = new Order();
$reviewModel = new Review();
$orders = $orderModel->findByBuyerId($userId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">My Orders</h1>
            <p class="text-muted">Track and manage your service purchases</p>
        </div>

        <div class="dashboard-card">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon info mb-3 mx-auto">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h3>No Orders Yet</h3>
                    <p class="text-muted">You haven't placed any orders yet. Browse services to get started.</p>
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
                                        <span class="badge-<?php echo $order['status'] === 'completed' ? 'success' : 'primary'; ?> user-role">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <?php if (!$reviewModel->hasReviewed($order['id'])): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary"
                                                            onclick="openReviewModal(<?php echo $order['id']; ?>, '<?php echo addslashes(htmlspecialchars($order['gig_title'])); ?>')">
                                                        <i class="fas fa-star"></i> Review
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-success-soft text-success small" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; border-radius: 2rem;">
                                                        <i class="fas fa-check-double me-1"></i> Reviewed
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
                                                <?php if (!$order['buyer_confirmed']): ?>
                                                    <form action="<?php echo SITE_URL; ?>/dashboard/update_order_status" method="POST" class="d-inline">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-check"></i> Mark as Done
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark small" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; border-radius: 2rem;">
                                                        <i class="fas fa-hourglass-half me-1"></i> Waiting for Seller
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <a href="<?php echo SITE_URL; ?>/chat?receiver_id=<?php echo $order['seller_id']; ?>" class="btn btn-sm btn-outline">
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

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title font-weight-700">Leave a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo SITE_URL; ?>/submit_review" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="order_id" id="modalOrderId">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-700 text-uppercase mb-2">Gig Title</label>
                        <p id="modalGigTitle" class="font-weight-600 mb-0"></p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-700 text-uppercase mb-3">Your Rating</label>
                        <div class="star-rating d-flex gap-2">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" class="d-none" required>
                                <label for="star<?php echo $i; ?>" class="cursor-pointer">
                                    <i class="fas fa-star fa-2x text-muted star-icon" data-value="<?php echo $i; ?>"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted small font-weight-700 text-uppercase mb-2">Detailed Feedback</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience working with this seller..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-icon.active { color: #f59e0b !important; }
.bg-success-soft { background: #ecfdf5; color: #10b981; }
.cursor-pointer { cursor: pointer; }
</style>

<script>
function openReviewModal(orderId, gigTitle) {
    document.getElementById('modalOrderId').value = orderId;
    document.getElementById('modalGigTitle').innerText = gigTitle;
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
}

document.querySelectorAll('.star-icon').forEach(star => {
    star.addEventListener('click', function() {
        const val = this.getAttribute('data-value');
        document.getElementById('star' + val).checked = true;
        
        document.querySelectorAll('.star-icon').forEach(s => {
            if (parseInt(s.getAttribute('data-value')) <= parseInt(val)) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    });
});
</script>
