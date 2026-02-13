<?php
require_once 'config.php';
require_once 'models/Gig.php';
require_once 'models/User.php';

requireLogin();

if (getUserRole() !== 'buyer') {
    showError("Only buyers can place orders.");
    redirect('/index.php');
}

$gigId = $_GET['id'] ?? 0;
$gigModel = new Gig();
$gig = $gigModel->findById($gigId);

if (!$gig) {
    showError("Gig not found.");
    redirect('/browse.php');
}

// Don't allow ordering own gig
if ($gig['user_id'] == $_SESSION['user_id']) {
    showError("You cannot order your own gig.");
    redirect('/gig.php?id=' . $gigId);
}

include 'views/partials/header.php';
?>

<div class="checkout-page py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="dashboard-card reveal-up">
                    <div class="checkout-header border-bottom pb-4 mb-4">
                        <h1 class="h3 font-weight-800 mb-2">Review Your Order</h1>
                        <p class="text-muted mb-0">Please confirm the details of your service purchase below.</p>
                    </div>

                    <div class="order-summary-card mb-4 p-4 rounded-xl" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.1);">
                        <div class="row align-items-center">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <img src="<?php echo $gig['image'] ? SITE_URL . '/uploads/' . $gig['image'] : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=400&q=80'; ?>" 
                                     alt="<?php echo htmlspecialchars($gig['title']); ?>" 
                                     class="img-fluid rounded-lg shadow-sm">
                            </div>
                            <div class="col-md-6">
                                <span class="badge badge-primary mb-2"><?php echo htmlspecialchars($gig['category']); ?></span>
                                <h2 class="h5 font-weight-700 mb-2"><?php echo htmlspecialchars($gig['title']); ?></h2>
                                <p class="text-muted small mb-0">by <span class="font-weight-600"><?php echo htmlspecialchars($gig['full_name']); ?></span></p>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <p class="h4 font-weight-800 text-primary mb-0">$<?php echo number_format($gig['price'], 2); ?></p>
                                <p class="text-muted small"><i class="fas fa-clock me-1"></i><?php echo $gig['delivery_time']; ?> Days Delivery</p>
                            </div>
                        </div>
                    </div>

                    <div class="order-details mb-5">
                        <h3 class="h6 font-weight-700 text-uppercase tracking-widest mb-4">Payment Summary</h3>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Service Subtotal</span>
                            <span class="font-weight-600">$<?php echo number_format($gig['price'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Service Fee</span>
                            <span class="font-weight-600">$0.00</span>
                        </div>
                        <hr class="my-3 opacity-10">
                        <div class="d-flex justify-content-between">
                            <span class="h5 font-weight-800">Total Amount</span>
                            <span class="h5 font-weight-800 text-primary">$<?php echo number_format($gig['price'], 2); ?></span>
                        </div>
                    </div>

                    <form action="<?php echo SITE_URL; ?>/order_process.php" method="POST">
                        <input type="hidden" name="gig_id" value="<?php echo $gig['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="alert alert-info d-flex align-items-center gap-3 mb-5 border-0 rounded-lg shadow-sm" style="background: rgba(30, 64, 175, 0.05);">
                            <i class="fas fa-info-circle text-primary h4 mb-0"></i>
                            <p class="small mb-0 text-muted">By clicking "Confirm Order", you agree to our Terms of Service and Privacy Policy. Funds will be held securely until work is delivered.</p>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <a href="<?php echo SITE_URL; ?>/gig.php?id=<?php echo $gig['id']; ?>" class="btn btn-ghost w-100">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Gig
                                </a>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary w-100 py-3 font-weight-700 rounded-pill shadow-lg hover-lift">
                                    Confirm and Place Order <i class="fas fa-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-page .order-summary-card {
    transition: transform 0.3s ease;
}
.checkout-page .rounded-xl {
    border-radius: 1.25rem;
}
</style>

<?php include 'views/partials/footer.php'; ?>
