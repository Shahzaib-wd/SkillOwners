<?php
require_once 'config.php';
require_once 'models/Gig.php';

$gigModel = new Gig();
$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

$gigs = $gigModel->search($query, $category);

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
.gig-card {
    background: var(--card);
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border);
    transition: all 0.3s ease;
}
.gig-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-elevated);
}
.gig-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: var(--muted);
}
.gig-content {
    padding: 1.25rem;
}
.gig-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.gig-seller {
    font-size: 0.875rem;
    color: var(--muted-foreground);
    margin-bottom: 1rem;
}
.gig-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.gig-price {
    font-weight: 600;
    color: var(--primary);
}
/* Rating display in grid */
.gig-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
}
.gig-rating i {
    color: #f59e0b;
}
.gig-rating .rating-value {
    font-weight: 600;
    color: var(--foreground);
    margin-right: 0.125rem;
}
.gig-rating .review-count {
    color: var(--muted-foreground);
    font-size: 0.75rem;
}
.gig-seller-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.seller-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
    background: var(--muted);
}
.seller-avatar-fallback {
    width: 24px;
    height: 24px;
    background: #e2e8f0;
    color: #475569;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.7rem;
}
</style>

<div class="browse-container">
    <div class="container">
        <div class="browse-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h1 class="mb-0">
                <?php echo !empty($query) ? "Search results for: " . htmlspecialchars($query) : "Browse Services"; ?>
            </h1>
            
            <div class="browse-search" style="max-width: 500px; width: 100%;">
                <div class="premium-search-container">
                    <i class="fas fa-search"></i>
                    <div class="input-group" style="display: flex; align-items: center; flex: 1;">
                        <input type="text" id="realTimeSearch" placeholder="Search services..." value="<?php echo htmlspecialchars($query); ?>">
                        <button id="searchButton" class="btn-search">Search</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="gigGrid" class="gig-grid">
            <?php if (empty($gigs)): ?>
                <div class="alert alert-info w-100">No services found. Try a different search.</div>
            <?php else: ?>
                <?php foreach ($gigs as $gig): ?>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('realTimeSearch');
    const searchBtn = document.getElementById('searchButton');
    const gigGrid = document.getElementById('gigGrid');

    const performSearch = () => {
        const query = searchInput.value.trim();
        const category = new URLSearchParams(window.location.search).get('category') || '';
        
        // Show loading state
        gigGrid.style.opacity = '0.5';
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch(`gig_api.php?q=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}`)
            .then(response => response.text())
            .then(html => {
                gigGrid.innerHTML = html;
                gigGrid.style.opacity = '1';
                searchBtn.disabled = false;
                searchBtn.textContent = 'Search';
                
                // Update URL without reloading
                const newUrl = new URL(window.location);
                if (query) {
                    newUrl.searchParams.set('q', query);
                } else {
                    newUrl.searchParams.delete('q');
                }
                window.history.replaceState({}, '', newUrl);
                
                // Update title
                const title = document.querySelector('h1');
                if (query) {
                    title.textContent = `Search results for: ${query}`;
                } else {
                    title.textContent = 'Browse Services';
                }
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                gigGrid.style.opacity = '1';
                searchBtn.disabled = false;
                searchBtn.textContent = 'Search';
            });
    };

    // Trigger search on click
    searchBtn.addEventListener('click', performSearch);

    // Trigger search on Enter key
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
