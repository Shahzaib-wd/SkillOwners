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

$filters = [
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'delivery_time' => $_GET['delivery_time'] ?? '',
    'min_rating' => $_GET['min_rating'] ?? '',
    'location' => $_GET['location'] ?? '',
    'is_official' => $_GET['is_official'] ?? '',
    'min_experience' => $_GET['min_experience'] ?? '',
    'language' => $_GET['language'] ?? ''
];

if ($type === 'freelancer' || $type === 'agency') {
    $userModel = new User();
    $result = $userModel->searchWithPagination($type, $query, $page, $perPage, $filters);
    $items = $result['users'];
} else {
    $gigModel = new Gig();
    $result = $gigModel->searchWithPagination($query, $category, $page, $perPage, $filters);
    $items = $result['gigs'];
}

$pagination = $result['pagination'];

// Track impressions for AJAX results
if ($type === 'gig' && !empty($items)) {
    $gigIds = array_column($items, 'id');
    $gigModel->incrementImpressions($gigIds);
}

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
    <?php if ($type === 'freelancer'): ?>
        <div class="user-list-item">
            <div class="user-list-info">
                <?php if ($item['profile_image']): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $item['profile_image']; ?>" class="user-list-avatar" alt="<?php echo htmlspecialchars($item['full_name']); ?>">
                <?php else: ?>
                    <div class="user-list-avatar-fallback">
                        <?php echo strtoupper(substr($item['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="user-list-details">
                    <h4 class="user-list-name">
                        <?php echo htmlspecialchars($item['full_name']); ?>
                        <?php if ($item['is_official']): ?>
                            <i class="fas fa-check-circle official-badge-icon" title="Official Agency"></i>
                        <?php endif; ?>
                    </h4>
                    <p class="user-list-title"><?php echo htmlspecialchars($item['professional_title'] ?: 'Freelancer'); ?></p>
                    <div class="user-list-rating" style="font-size: 0.85rem; color: #f59e0b; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                        <i class="fas fa-star"></i>
                        <span style="font-weight: 700; color: #1f2937;"><?php echo $item['avg_rating'] > 0 ? round($item['avg_rating'], 1) : 'New'; ?></span>
                    </div>
                </div>
            </div>
            <div class="user-list-actions">
                <a href="<?php echo SITE_URL; ?>/profile?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline">View Profile</a>
                <button class="btn btn-sm btn-primary invite-btn" onclick="inviteFreelancer(<?php echo $item['id']; ?>)">Invite</button>
            </div>
        </div>
    <?php elseif ($type === 'agency'): ?>
        <a href="<?php echo SITE_URL; ?>/profile?id=<?php echo $item['id']; ?>" class="gig-card">
            <div class="gig-image-wrapper">
                <img src="<?php echo $item['profile_image'] ? SITE_URL . '/uploads/' . $item['profile_image'] : 'https://via.placeholder.com/280x200?text=' . urlencode($item['full_name']); ?>" alt="<?php echo htmlspecialchars($item['full_name']); ?>" class="gig-image">
                <?php if ($item['is_official']): ?>
                    <span class="gig-card-badge">Official Agency</span>
                <?php endif; ?>
            </div>
            <div class="gig-content">
                <h3 class="gig-title">
                    <?php echo htmlspecialchars($item['full_name']); ?>
                </h3>
                <div class="gig-seller-info">
                    <?php if ($item['profile_image']): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $item['profile_image']; ?>" 
                             alt="<?php echo htmlspecialchars($item['full_name']); ?>"
                             class="seller-avatar">
                    <?php else: ?>
                        <div class="seller-avatar-fallback">
                            <?php echo strtoupper(substr($item['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="gig-seller"><?php echo htmlspecialchars($item['professional_title'] ?: 'Verified Agency'); ?></div>
                </div>
                <div class="gig-rating">
                    <span class="rating-value">
                        <i class="fas fa-star"></i> 
                        <?php echo $item['avg_rating'] > 0 ? round($item['avg_rating'], 1) : 'New'; ?>
                    </span>
                    <span class="review-count">(<?php echo $item['review_count']; ?> reviews)</span>
                </div>
                <div class="gig-footer">
                    <span class="gig-price-label">Status</span>
                    <span class="gig-price" style="font-size: 0.95rem; color: <?php echo $item['is_official'] ? '#10b981' : 'inherit'; ?>;">
                        <?php echo $item['is_official'] ? 'Official Partner' : 'Verified Agency'; ?>
                    </span>
                </div>
            </div>
        </a>
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
