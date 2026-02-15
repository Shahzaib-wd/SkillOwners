<?php
require_once 'config.php';
require_once 'models/Gig.php';
require_once 'models/User.php';

header('Content-Type: text/html');

$type = $_GET['type'] ?? 'gig';
$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;

if ($type === 'freelancer' || $type === 'agency') {
    $userModel = new User();
    $result = $userModel->searchWithPagination($type, $query, $page, $perPage);
    $items = $result['users'];
} else {
    $gigModel = new Gig();
    $result = $gigModel->searchWithPagination($query, $category, $page, $perPage);
    $items = $result['gigs'];
}

$pagination = $result['pagination'];

// Check if this is an AJAX request asking for just pagination info
$returnPagination = isset($_GET['get_pagination']) && $_GET['get_pagination'] === '1';

if (empty($items)) {
    echo '<div class="alert alert-info w-100">No results found. Try a different search.</div>';
    if ($returnPagination) {
        echo '<!--PAGINATION_DATA-->' . json_encode($pagination) . '<!--PAGINATION_DATA_END-->';
    }
    exit;
}
?>
<?php foreach ($items as $item): ?>
    <?php if ($type === 'freelancer' || $type === 'agency'): ?>
        <div class="gig-card user-discovery-card">
            <div class="gig-image-wrapper">
                <img src="<?php echo $item['profile_image'] ? SITE_URL . '/uploads/' . $item['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($item['full_name']) . '&size=280&background=10b981&color=fff'; ?>" alt="<?php echo htmlspecialchars($item['full_name']); ?>" class="user-discovery-image">
                <span class="gig-card-badge"><?php echo ucfirst($type); ?></span>
            </div>
            <div class="gig-content">
                <h3 class="gig-title"><?php echo htmlspecialchars($item['full_name']); ?></h3>
                <div class="user-skills mb-2">
                    <?php 
                    $skills = array_slice(explode(',', $item['skills']), 0, 3);
                    foreach ($skills as $skill): ?>
                        <span class="badge bg-light text-dark mb-1" style="font-size: 0.7rem;"><?php echo htmlspecialchars(trim($skill)); ?></span>
                    <?php endforeach; ?>
                </div>
                <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.5rem;">
                    <?php echo htmlspecialchars($item['bio'] ?: 'No bio available.'); ?>
                </p>
                <div class="gig-footer">
                    <a href="<?php echo SITE_URL; ?>/profile?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline">View Profile</a>
                    <?php if ($type === 'freelancer'): ?>
                        <button class="btn btn-sm btn-primary invite-btn" onclick="inviteFreelancer(<?php echo $item['id']; ?>)">Invite</button>
                    <?php elseif ($type === 'agency'): ?>
                        <button class="btn btn-sm btn-primary apply-btn" onclick="applyToAgency(<?php echo $item['id']; ?>)">Apply</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <a href="<?php echo SITE_URL; ?>/gig?id=<?php echo $item['id']; ?>" class="gig-card">
            <div class="gig-image-wrapper">
                <img src="<?php echo $item['image'] ? SITE_URL . '/uploads/' . $item['image'] : 'https://via.placeholder.com/280x200?text=' . urlencode($item['title']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="gig-image">
                <span class="gig-card-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                <button class="gig-favorite-btn" onclick="event.preventDefault(); event.stopPropagation();"><i class="far fa-heart"></i></button>
            </div>
            <div class="gig-content">
                <h3 class="gig-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                <div class="gig-seller-info">
                    <?php if ($item['seller_image']): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $item['seller_image']; ?>" 
                             alt="<?php echo htmlspecialchars($item['full_name']); ?>"
                             class="seller-avatar">
                    <?php else: ?>
                        <div class="seller-avatar-fallback">
                            <?php echo strtoupper(substr($item['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="gig-seller"><?php echo htmlspecialchars($item['full_name']); ?></div>
                </div>
                <div class="gig-rating">
                    <span class="rating-value">
                        <i class="fas fa-star"></i> 
                        <?php echo $item['avg_rating'] > 0 ? round($item['avg_rating'], 1) : 'New'; ?>
                    </span>
                    <span class="review-count">(<?php echo $item['review_count']; ?>)</span>
                </div>
                <div class="gig-footer">
                    <span class="gig-price-label">Starting at</span>
                    <span class="gig-price">$<?php echo number_format($item['price'], 2); ?></span>
                </div>
            </div>
        </a>
    <?php endif; ?>
<?php endforeach;

// Return pagination data if available
if (isset($pagination)) {
    echo '<!--PAGINATION_DATA-->' . json_encode($pagination) . '<!--PAGINATION_DATA_END-->';
}
?>
