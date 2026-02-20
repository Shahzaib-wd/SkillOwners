<?php
require_once 'config.php';
include 'views/partials/header.php';

$id = $_GET['id'] ?? null;
$slug = $_GET['slug'] ?? null;
$db = getDBConnection();

if ($id || $slug) {
    try {
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 'published'");
            $stmt->execute([$id]);
        } else {
            $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
            $stmt->execute([$slug]);
        }
        $post = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Blog post fetch failed: " . $e->getMessage());
        $post = null;
    }
} else {
    $post = null;
}

// Fallback to demo if not found
if (!$post):
    $post = [
        'title' => 'Insights into Digital Strategy',
        'content' => '<p>Coming soon. Our team is working on sharing the latest industry insights with you.</p>',
        'category' => 'Strategy',
        'created_at' => date('Y-m-d H:i:s'),
        'featured_image' => null
    ];
endif;
?>

<!-- Immersive Article Header -->
<header class="article-header-full">
    <img src="<?php echo $post['featured_image'] ? SITE_URL . '/uploads/' . $post['featured_image'] : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920'; ?>" class="article-header-image" alt="Header">
    <div class="article-header-overlay">
        <div class="container reveal-up">
            <div class="row">
                <div class="col-lg-10">
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-4"><?php echo htmlspecialchars($post['category']); ?></span>
                    <h1 class="display-2 font-weight-900 text-white mb-0" style="text-shadow: 0 10px 30px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($post['title']); ?></h1>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Article Content -->
<article class="article-content-immersive reveal-up">
    <div class="d-flex align-items-center gap-4 text-muted small mb-5 pb-5 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">SO</div>
            <span class="font-weight-800 text-dark">Team SkillOwners</span>
        </div>
        <div class="vr"></div>
        <span class="d-flex align-items-center gap-2"><i class="far fa-calendar-alt text-primary"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
        <div class="vr"></div>
        <span class="d-flex align-items-center gap-2"><i class="far fa-clock text-primary"></i> 5 min read</span>
    </div>

    <div class="blog-post-body text-muted" style="font-size: 1.25rem; line-height: 2; letter-spacing: -0.01em;">
        <?php echo $post['content']; ?>
    </div>

    <div class="mt-5 pt-5 border-top d-flex flex-wrap justify-content-between align-items-center gap-4">
        <a href="<?php echo SITE_URL; ?>/blog" class="btn btn-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to Insights
        </a>
    </div>
</article>

<style>
.blog-post-body blockquote {
    font-family: var(--font-display);
    font-size: 2rem;
    color: var(--primary);
    font-weight: 700;
    font-style: italic;
    padding: 3rem 0;
    margin: 2rem 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}
.blog-post-body h2, .blog-post-body h3 {
    color: #0c0c0c;
    font-weight: 800;
    margin-top: 3.5rem;
    margin-bottom: 1.5rem;
}
.blog-post-body p {
    margin-bottom: 2rem;
}
</style>

<?php
// Fetch Related Posts
$relatedStmt = $db->prepare("SELECT * FROM blog_posts WHERE category = ? AND id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3");
$relatedStmt->execute([$post['category'] ?? '', $post['id'] ?? 0]);
$relatedPosts = $relatedStmt->fetchAll();

if (!empty($relatedPosts)):
?>
<section class="section-padding-fluid bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill mb-3">Keep Reading</span>
            <h2 class="font-weight-900 display-4">Related <span class="text-gradient">Insights</span></h2>
        </div>
        
        <div class="row g-4">
            <?php foreach ($relatedPosts as $index => $relPost): ?>
            <div class="col-lg-4 col-md-6 reveal-up" style="animation-delay: <?php echo 0.1 * ($index + 1); ?>s;">
                <article class="card border-0 shadow-sm h-100 overflow-hidden rounded-5 transition-all hover-translate-y bg-white">
                    <div class="position-relative overflow-hidden" style="height: 240px;">
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $relPost['featured_image']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($relPost['title']); ?>">
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-dark px-3 py-2 rounded-pill"><?php echo htmlspecialchars($relPost['category']); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-muted small mb-3"><?php echo date('M d, Y', strtotime($relPost['created_at'])); ?></div>
                        <h4 class="font-weight-800 mb-4">
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $relPost['slug']; ?>" class="text-dark text-decoration-none hover-primary">
                                <?php echo htmlspecialchars($relPost['title']); ?>
                            </a>
                        </h4>
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $relPost['slug']; ?>" class="font-weight-800 text-primary small text-decoration-none tracking-widest text-uppercase">
                            Full Experience <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'views/partials/footer.php'; ?>
