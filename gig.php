<?php
require_once 'config.php';
require_once 'models/Gig.php';

$gigId = $_GET['id'] ?? 0;
$gigModel = new Gig();
$gig = $gigModel->findById($gigId);

if (!$gig) {
    die('Gig not found');
}

include 'views/partials/header.php';
?>

<style>
.gig-detail {
    padding: 6rem 0 4rem;
    min-height: 100vh;
}
.gig-header {
    margin-bottom: 2rem;
}
.gig-main-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: var(--radius);
    margin-bottom: 2rem;
}
.seller-card {
    background: var(--card);
    padding: 1.5rem;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    position: sticky;
    top: 80px;
}
.order-btn {
    width: 100%;
    margin-bottom: 1rem;
}
</style>

<div class="gig-detail">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="gig-header">
                    <span class="badge badge-primary mb-2"><?php echo htmlspecialchars($gig['category']); ?></span>
                    <h1><?php echo htmlspecialchars($gig['title']); ?></h1>
                    <p class="text-muted">by <?php echo htmlspecialchars($gig['full_name']); ?></p>
                </div>
                
                <img src="<?php echo $gig['image'] ? SITE_URL . '/uploads/' . $gig['image'] : 'https://via.placeholder.com/800x500'; ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>" class="gig-main-image">
                
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title">About This Gig</h3>
                        <p><?php echo nl2br(htmlspecialchars($gig['description'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="seller-card">
                    <h3 class="h5 mb-3">$<?php echo number_format($gig['price'], 2); ?></h3>
                    <p class="text-muted mb-3"><i class="fas fa-clock"></i> <?php echo $gig['delivery_time']; ?> days delivery</p>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if ($gig['user_id'] == $_SESSION['user_id']): ?>
                            <a href="<?php echo SITE_URL; ?>/dashboard/<?php echo getUserRole(); ?>/gigs.php" class="btn btn-primary order-btn">
                                <i class="fas fa-edit"></i> Manage This Gig
                            </a>
                        <?php elseif (getUserRole() === 'buyer'): ?>
                            <a href="<?php echo SITE_URL; ?>/checkout.php?id=<?php echo $gig['id']; ?>" class="btn btn-primary order-btn mb-2">
                                <i class="fas fa-shopping-cart"></i> Order Now
                            </a>
                            <a href="<?php echo SITE_URL; ?>/chat.php?seller_id=<?php echo $gig['user_id']; ?>" class="btn btn-outline btn-block mb-3">
                                <i class="fas fa-comments"></i> Contact Seller
                            </a>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/chat.php?seller_id=<?php echo $gig['user_id']; ?>" class="btn btn-primary order-btn">
                                <i class="fas fa-comments"></i> Contact Seller
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary order-btn">
                            Login to Order
                        </a>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo $gig['seller_image'] ? SITE_URL . '/uploads/' . $gig['seller_image'] : 'https://via.placeholder.com/50'; ?>" 
                             alt="<?php echo htmlspecialchars($gig['full_name']); ?>" 
                             style="width: 50px; height: 50px; border-radius: 50%; margin-right: 1rem;">
                        <div>
                            <h4 class="h6 mb-0"><?php echo htmlspecialchars($gig['full_name']); ?></h4>
                            <small class="text-muted">Freelancer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
