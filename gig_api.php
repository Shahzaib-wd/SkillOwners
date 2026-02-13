<?php
require_once 'config.php';
require_once 'models/Gig.php';

header('Content-Type: text/html');

$gigModel = new Gig();
$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

$gigs = $gigModel->search($query, $category);

if (empty($gigs)) {
    echo '<div class="alert alert-info w-100">No services found. Try a different search.</div>';
    exit;
}

foreach ($gigs as $gig): ?>
    <a href="<?php echo SITE_URL; ?>/gig.php?id=<?php echo $gig['id']; ?>" class="gig-card">
        <img src="<?php echo $gig['image'] ? SITE_URL . '/uploads/' . $gig['image'] : 'https://via.placeholder.com/280x200?text=' . urlencode($gig['title']); ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>" class="gig-image">
        <div class="gig-content">
            <h3 class="gig-title"><?php echo htmlspecialchars($gig['title']); ?></h3>
            <div class="gig-seller-info">
                <?php if ($gig['seller_image']): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $gig['seller_image']; ?>" 
                         alt="<?php echo htmlspecialchars($gig['full_name']); ?>"
                         class="seller-avatar">
                <?php else: ?>
                    <div class="seller-avatar-fallback">
                        <?php echo strtoupper(substr($gig['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="gig-seller"><?php echo htmlspecialchars($gig['full_name']); ?></div>
            </div>
            <div class="gig-rating">
                <span class="rating-value">
                    <i class="fas fa-star"></i> 
                    <?php echo $gig['avg_rating'] > 0 ? round($gig['avg_rating'], 1) : 'New'; ?>
                </span>
                <span class="review-count">(<?php echo $gig['review_count']; ?>)</span>
            </div>
            <div class="gig-footer">
                <span class="badge badge-primary"><?php echo htmlspecialchars($gig['category']); ?></span>
                <span class="gig-price">$<?php echo number_format($gig['price'], 2); ?></span>
            </div>
        </div>
    </a>
<?php endforeach; ?>
