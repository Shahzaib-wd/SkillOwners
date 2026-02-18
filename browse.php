<?php
require_once 'config.php';
require_once 'models/Gig.php';
require_once 'models/User.php';

$gigModel = new Gig();
$userModel = new User();

$type = $_GET['type'] ?? 'gig'; // gig, freelancer, agency
$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;

if ($type === 'freelancer' || $type === 'agency') {
    $result = $userModel->searchWithPagination($type, $query, $page, $perPage);
    $items = $result['users'];
} else {
    $result = $gigModel->searchWithPagination($query, $category, $page, $perPage);
    $items = $result['gigs'];
}

$pagination = $result['pagination'];

// Track impressions
if ($type === 'gig' && !empty($items)) {
    $gigIds = array_column($items, 'id');
    $gigModel->incrementImpressions($gigIds);
}

include 'views/partials/header.php';
?>

<style>
.browse-container {
    padding: 6rem 0 4rem;
    min-height: 100vh;
    background: var(--background);
}
.gig-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

/* Enhanced Professional Gig Card */
.gig-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    color: inherit;
    display: block;
    position: relative;
}
.gig-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    border-color: #22c55e;
}

.gig-image-wrapper {
    position: relative;
    overflow: hidden;
}

.gig-image {
    width: 100%;
    aspect-ratio: 712 / 433;
    object-fit: cover;
    background: #f3f4f6;
    transition: transform 0.5s ease;
}
.gig-card:hover .gig-image {
    transform: scale(1.08);
}

.gig-card-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(0, 0, 0, 0.75);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    backdrop-filter: blur(4px);
}

.gig-favorite-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    opacity: 0;
}
.gig-card:hover .gig-favorite-btn {
    opacity: 1;
}
.gig-favorite-btn:hover {
    color: #ef4444;
    transform: scale(1.1);
}

.gig-content {
    padding: 1.25rem;
}

.gig-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #1f2937;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 3rem;
}

.gig-seller-info {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    margin-bottom: 0.875rem;
}

.seller-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.seller-avatar-fallback {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.7rem;
}

.gig-seller {
    font-size: 0.875rem;
    color: #4b5563;
    font-weight: 500;
}

.gig-rating {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-bottom: 1rem;
}

.gig-rating i {
    color: #f59e0b;
    font-size: 0.875rem;
}

.gig-rating .rating-value {
    font-weight: 700;
    color: #1f2937;
    font-size: 0.875rem;
}

.gig-rating .review-count {
    color: #6b7280;
    font-size: 0.8rem;
}

.gig-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.gig-price-label {
    font-size: 0.7rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.gig-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #22c55e;
}

.gig-level {
    position: absolute;
    bottom: 80px;
    right: 12px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Skeleton Loading Styles */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 4px;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.skeleton-card {
    background: var(--card);
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border);
}

.skeleton-image {
    width: 100%;
    aspect-ratio: 712 / 433;
    background: var(--muted);
}

.skeleton-content {
    padding: 1.25rem;
}

.skeleton-title {
    height: 20px;
    width: 80%;
    margin-bottom: 0.75rem;
}

.skeleton-seller {
    height: 16px;
    width: 50%;
    margin-bottom: 1rem;
}

.skeleton-rating {
    height: 16px;
    width: 30%;
    margin-bottom: 1rem;
}

.skeleton-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.skeleton-badge {
    height: 24px;
    width: 60px;
    border-radius: 12px;
}

.skeleton-price {
    height: 20px;
    width: 50px;
}

/* Pagination Styles */
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--foreground);
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.pagination-btn:hover:not(:disabled) {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination-btn.ellipsis {
    background: transparent;
    border: none;
    cursor: default;
}

.pagination-info {
    text-align: center;
    color: var(--muted-foreground);
    font-size: 0.875rem;
    margin-top: 1rem;
}

.search-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.results-count {
    color: var(--muted-foreground);
    font-size: 0.9rem;
}

.clear-search {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.9rem;
}

.filter-bar {
    background: #fff;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.filter-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}
.filter-input {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 0.875rem;
    color: #1f2937;
    min-width: 120px;
    background: #f9fafb;
}
.filter-input:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
}
.filter-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: #4b5563;
    user-select: none;
    height: 38px;
}
.filter-checkbox input {
    width: 1rem;
    height: 1rem;
    cursor: pointer;
}
</style>

<div class="browse-container">
    <div class="container">
        <div class="browse-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h1 class="mb-0">
                <?php 
                if ($type === 'freelancer') echo !empty($query) ? "Search results for Freelancers: " . htmlspecialchars($query) : "Find Freelancers";
                elseif ($type === 'agency') echo !empty($query) ? "Search results for Agencies: " . htmlspecialchars($query) : "Find Agencies";
                else echo !empty($query) ? "Search results for Services: " . htmlspecialchars($query) : "Browse Services"; 
                ?>
            </h1>
            
            <div class="browse-search" style="max-width: 500px; width: 100%;">
                <div class="premium-search-container">
                    <i class="fas fa-search"></i>
                    <div class="input-group" style="display: flex; align-items: center; flex: 1;">
                        <input type="text" id="realTimeSearch" placeholder="<?php echo ($type === 'gig' ? 'Search services...' : ($type === 'freelancer' ? 'Search freelancers...' : 'Search agencies...')); ?>" value="<?php echo htmlspecialchars($query); ?>">
                        <button id="searchButton" class="btn-search">Search</button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($type === 'freelancer'): ?>
        <div class="filter-bar">
            <div class="filter-group">
                <label>Location</label>
                <input type="text" id="filterLocation" class="filter-input" placeholder="City or Country">
            </div>
            <div class="filter-group">
                <label>Experience (Years)</label>
                <input type="number" id="filterExperience" class="filter-input" placeholder="0+">
            </div>
            <div class="filter-group">
                <label>Language</label>
                <input type="text" id="filterLanguage" class="filter-input" placeholder="English, etc.">
            </div>
            <div class="filter-group">
                <label>Min Rating</label>
                <select id="filterRating" class="filter-input">
                    <option value="">Any</option>
                    <option value="4.5">4.5+ ★</option>
                    <option value="4">4.0+ ★</option>
                    <option value="3">3.0+ ★</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-checkbox">
                    <input type="checkbox" id="filterOfficial"> Official Only
                </label>
            </div>
            
            <button id="applyFilters" class="btn btn-primary" style="height: 38px;">Apply Filters</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($query) || !empty($category) || $type === 'freelancer'): ?>
            <div class="search-info">
                <span id="resultsCount" class="results-count">
                    <?php echo $pagination['total_items']; ?> <?php echo $type === 'gig' ? 'service' : ($type === 'freelancer' ? 'freelancer' : 'agency'); ?><?php echo $pagination['total_items'] != 1 ? 's' : ''; ?> found
                    <?php if (!empty($query)): ?>
                        for "<?php echo htmlspecialchars($query); ?>"
                    <?php endif; ?>
                </span>
                <a href="<?php echo SITE_URL; ?>/browse?type=<?php echo $type; ?>" class="clear-search">Clear all filters</a>
            </div>
        <?php endif; ?>
        
        <div id="gigGrid" class="<?php echo ($type === 'freelancer') ? 'user-list-container' : 'gig-grid'; ?>">
            <?php if (empty($items)): ?>
                <div class="alert alert-info w-100">No results found. Try a different search.</div>
            <?php else: ?>
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div id="paginationContainer" class="pagination-container" <?php echo ($pagination['total_pages'] <= 1) ? 'style="display: none;"' : ''; ?>>
            <button class="pagination-btn" id="prevPage" <?php echo !$pagination['has_prev'] ? 'disabled' : ''; ?>>
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div id="pageNumbers" class="d-flex gap-2">
                <?php
                $currentPage = $pagination['current_page'];
                $totalPages = $pagination['total_pages'];
                
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                if ($startPage > 1) {
                    echo '<a href="?page=1&type=' . urlencode($type) . '&q=' . urlencode($query) . '&category=' . urlencode($category) . '" class="pagination-btn">1</a>';
                    if ($startPage > 2) {
                        echo '<span class="pagination-btn ellipsis">...</span>';
                    }
                }
                
                for ($i = $startPage; $i <= $endPage; $i++) {
                    $activeClass = ($i == $currentPage) ? 'active' : '';
                    echo '<a href="?page=' . $i . '&type=' . urlencode($type) . '&q=' . urlencode($query) . '&category=' . urlencode($category) . '" class="pagination-btn ' . $activeClass . '">' . $i . '</a>';
                }
                
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) {
                        echo '<span class="pagination-btn ellipsis">...</span>';
                    }
                    echo '<a href="?page=' . $totalPages . '&type=' . urlencode($type) . '&q=' . urlencode($query) . '&category=' . urlencode($category) . '" class="pagination-btn">' . $totalPages . '</a>';
                }
                ?>
            </div>
            
            <button class="pagination-btn" id="nextPage" <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <div class="pagination-info" <?php echo ($pagination['total_pages'] <= 1) ? 'style="display: none;"' : ''; ?>>
            Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?> (<?php echo $pagination['total_items']; ?> total <?php echo $type === 'gig' ? 'services' : ($type === 'freelancer' ? 'freelancers' : 'agencies'); ?>)
        </div>
    </div>
</div>

<script>
const skeletonTemplate = (count = 8) => {
    let html = '';
    const isList = '<?php echo $type; ?>' === 'freelancer';
    
    for (let i = 0; i < count; i++) {
        if (isList) {
            html += `
                <div class="user-list-item skeleton-list-item">
                    <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                        <div class="skeleton-avatar skeleton" style="width: 48px; height: 48px; border-radius: 50%;"></div>
                        <div style="flex: 1;">
                            <div class="skeleton-line skeleton" style="width: 150px; height: 16px; margin-bottom: 8px;"></div>
                            <div class="skeleton-line skeleton" style="width: 100px; height: 12px;"></div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="skeleton-card">
                    <div class="skeleton-image skeleton"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title skeleton"></div>
                        <div class="skeleton-seller skeleton"></div>
                        <div class="skeleton-rating skeleton"></div>
                        <div class="skeleton-footer">
                            <div class="skeleton-badge skeleton"></div>
                            <div class="skeleton-price skeleton"></div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    return html;
};
const USER_LOGGED_IN = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
const LOGIN_URL = '<?php echo SITE_URL; ?>/login';

let currentPage = <?php echo $pagination['current_page']; ?>;
let currentType = '<?php echo htmlspecialchars($type); ?>';
let currentQuery = '<?php echo htmlspecialchars($query); ?>';
let currentCategory = '<?php echo htmlspecialchars($category); ?>';
let totalPages = <?php echo $pagination['total_pages']; ?>;
let isLoading = false;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('realTimeSearch');
    const searchBtn = document.getElementById('searchButton');
    const gigGrid = document.getElementById('gigGrid');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageNumbers = document.getElementById('pageNumbers');
    const paginationContainer = document.getElementById('paginationContainer');

    const showSkeleton = () => {
        gigGrid.innerHTML = skeletonTemplate(8);
    };

    const fetchGigs = (page = 1) => {
        if (isLoading) return;
        isLoading = true;
        
        showSkeleton();
        
        const filters = {
            type: currentType,
            q: searchInput.value.trim(),
            page: page
        };

        if (document.getElementById('filterCategory')) filters.category = document.getElementById('filterCategory').value;
        if (document.getElementById('filterMinPrice')) filters.min_price = document.getElementById('filterMinPrice').value;
        if (document.getElementById('filterMaxPrice')) filters.max_price = document.getElementById('filterMaxPrice').value;
        if (document.getElementById('filterDelivery')) filters.delivery_time = document.getElementById('filterDelivery').value;
        if (document.getElementById('filterLocation')) filters.location = document.getElementById('filterLocation').value;
        if (document.getElementById('filterExperience')) filters.min_experience = document.getElementById('filterExperience').value;
        if (document.getElementById('filterLanguage')) filters.language = document.getElementById('filterLanguage').value;
        if (document.getElementById('filterOfficial')) filters.is_official = document.getElementById('filterOfficial').checked ? 1 : '';
        if (document.getElementById('filterRating')) filters.min_rating = document.getElementById('filterRating').value;

        const queryString = new URLSearchParams(filters).toString();
        const url = `gig_api.php?${queryString}`;
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const paginationMatch = html.match(/<!--PAGINATION_DATA-->([\s\S]*?)<!--PAGINATION_DATA_END-->/);
                let paginationData = null;
                
                if (paginationMatch) {
                    try {
                        paginationData = JSON.parse(paginationMatch[1]);
                        html = html.replace(paginationMatch[0], '');
                    } catch (e) {
                        console.error('Error parsing pagination data:', e);
                    }
                }
                
                gigGrid.innerHTML = html;
                
                if (paginationData) {
                    totalPages = paginationData.total_pages;
                    currentPage = paginationData.current_page;
                    updatePagination(paginationData);
                    
                    const resultsCount = document.getElementById('resultsCount');
                    if (resultsCount) {
                        const count = paginationData.total_items;
                        const itemType = currentType === 'gig' ? 'service' : (currentType === 'freelancer' ? 'freelancer' : 'agency');
                        const plural = count != 1 ? 's' : '';
                        let countText = `${count} ${itemType}${plural} found`;
                        if (searchInput.value.trim()) {
                            countText += ` for "${searchInput.value.trim()}"`;
                        }
                        resultsCount.textContent = countText;
                    }
                } else {
                    // Hide pagination if no data returned
                    totalPages = 1;
                    const container = document.getElementById('paginationContainer');
                    const info = document.querySelector('.pagination-info');
                    if (container) container.style.display = 'none';
                    if (info) info.style.display = 'none';
                }
                
                if (searchBtn) {
                    searchBtn.disabled = false;
                    searchBtn.textContent = 'Search';
                }
                
                isLoading = false;
                
                const newUrl = new URL(window.location);
                Object.keys(filters).forEach(key => {
                    if (filters[key]) {
                        newUrl.searchParams.set(key, filters[key]);
                    } else {
                        newUrl.searchParams.delete(key);
                    }
                });
                window.history.replaceState({}, '', newUrl);
                
                const title = document.querySelector('h1');
                const searchQuery = searchInput.value.trim();
                if (searchQuery) {
                    title.textContent = `Search results for ${currentType === 'gig' ? 'Services' : (currentType === 'freelancer' ? 'Freelancers' : 'Agencies')}: ${searchQuery}`;
                } else {
                    title.textContent = currentType === 'gig' ? 'Browse Services' : (currentType === 'freelancer' ? 'Find Freelancers' : 'Find Agencies');
                }
                
                gigGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                gigGrid.innerHTML = '<div class="alert alert-danger w-100">Error loading services. Please try again.</div>';
                if (searchBtn) {
                    searchBtn.disabled = false;
                    searchBtn.textContent = 'Search';
                }
                isLoading = false;
            });
    };

    const updatePagination = (data) => {
        const container = document.getElementById('paginationContainer');
        const info = document.querySelector('.pagination-info');
        if (!container) return;
        
        if (data.total_pages <= 1) {
            container.style.display = 'none';
            if (info) info.style.display = 'none';
            return;
        }
        
        container.style.display = 'flex';
        if (info) info.style.display = 'block';
        if (!pageNumbers) return;
        
        let html = '';
        const currentPage = data.current_page;
        const totalPages = data.total_pages;
        
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            html += `<a href="#" data-page="1" class="pagination-btn">1</a>`;
            if (startPage > 2) {
                html += `<span class="pagination-btn ellipsis">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = (i === currentPage) ? 'active' : '';
            html += `<a href="#" data-page="${i}" class="pagination-btn ${activeClass}">${i}</a>`;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<span class="pagination-btn ellipsis">...</span>`;
            }
            html += `<a href="#" data-page="${totalPages}" class="pagination-btn">${totalPages}</a>`;
        }
        
        pageNumbers.innerHTML = html;
        
        if (prevBtn) {
            prevBtn.disabled = !data.has_prev;
        }
        if (nextBtn) {
            nextBtn.disabled = !data.has_next;
        }
        
        const infoDiv = document.querySelector('.pagination-info');
        if (infoDiv) {
            infoDiv.textContent = `Page ${currentPage} of ${totalPages} (${data.total_items} total services)`;
        }
        
        attachPaginationHandlers();
    };

    const attachPaginationHandlers = () => {
        const pageLinks = document.querySelectorAll('#pageNumbers .pagination-btn:not(.ellipsis)');
        pageLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page && page !== currentPage) {
                    fetchGigs(page);
                }
            });
        });
    };

    const performSearch = () => {
        currentPage = 1;
        fetchGigs(currentPage);
    };

    const applyFiltersBtn = document.getElementById('applyFilters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', performSearch);
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1 && !isLoading) {
                fetchGigs(currentPage - 1);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages && !isLoading) {
                fetchGigs(currentPage + 1);
            }
        });
    }

    attachPaginationHandlers();
});

function inviteFreelancer(id) {
    if (!USER_LOGGED_IN) {
        alert('Please login to invite freelancers to your agency.');
        window.location.href = LOGIN_URL;
        return;
    }
    if (!confirm('Send an invitation to join your agency?')) return;
    performAgencyAction('invite', id);
}

function applyToAgency(id) {
    if (!USER_LOGGED_IN) {
        alert('Please login to apply to this agency.');
        window.location.href = LOGIN_URL;
        return;
    }
    if (!confirm('Apply to join this agency team?')) return;
    performAgencyAction('apply', id);
}

function performAgencyAction(action, id) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('target_id', id);
    
    fetch('<?php echo SITE_URL; ?>/dashboard/agency/agency_actions', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message || 'Action failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>

<?php include 'views/partials/footer.php'; ?>
