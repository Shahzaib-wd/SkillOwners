<?php
require_once 'config.php';
require_once 'models/Gig.php';
require_once 'models/Review.php';

$gigId = $_GET['id'] ?? 0;
$gigModel = new Gig();
$reviewModel = new Review();
$gig = $gigModel->findById($gigId);

if (!$gig) {
    die('Gig not found');
}

$reviews = $reviewModel->findByGigId($gigId);
$ratingData = $reviewModel->getAverageRating($gigId);
$avgRating = round($ratingData['avg_rating'] ?? 0, 1);
$reviewCount = $ratingData['count'] ?? 0;

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
.review-item {
    border-bottom: 1px solid var(--border);
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}
.review-item:last-child {
    border-bottom: none;
}
.star-rating i {
    color: #f59e0b;
    font-size: 0.85rem;
}
.rating-summary {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
</style>

<div class="gig-detail">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="gig-header">
                    <span class="badge badge-primary mb-2"><?php echo htmlspecialchars($gig['category']); ?></span>
                    <h1><?php echo htmlspecialchars($gig['title']); ?></h1>
                    <div class="rating-summary">
                        <div class="star-rating">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="<?php echo $i <= $avgRating ? 'fas' : 'far'; ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="font-weight-700"><?php echo $avgRating ?: 'New'; ?></span>
                        <span class="text-muted">(<?php echo $reviewCount; ?> reviews)</span>
                        <span class="mx-2 text-muted">|</span>
                        <span class="text-muted">by <?php echo htmlspecialchars($gig['full_name']); ?></span>
                    </div>
                </div>
                
                <img src="<?php echo $gig['image'] ? SITE_URL . '/uploads/' . $gig['image'] : 'https://via.placeholder.com/800x500'; ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>" class="gig-main-image">
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-3">About This Gig</h3>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($gig['description'])); ?></p>
                    </div>
                </div>

                <div class="reviews-section">
                    <h3 class="h4 mb-4">Customer Reviews (<?php echo $reviewCount; ?>)</h3>
                    
                    <?php if (empty($reviews)): ?>
                        <div class="card">
                            <div class="card-body text-center py-4 text-muted">
                                <i class="far fa-comment-dots fa-2x mb-2 opacity-25"></i>
                                <p class="mb-0">No reviews yet for this gig.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-sm me-3">
                                        <?php if ($review['profile_image']): ?>
                                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $review['profile_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['buyer_name']); ?>"
                                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="avatar-circle-sm" style="width: 40px; height: 40px; background: #e2e8f0; color: #475569; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                                                <?php echo strtoupper(substr($review['buyer_name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h5 class="h6 mb-1"><?php echo htmlspecialchars($review['buyer_name']); ?></h5>
                                        <div class="star-rating">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                            <?php endfor; ?>
                                            <span class="text-muted small ms-2"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
