<?php
require_once 'config.php';
include 'views/partials/header.php';

$db = getDBConnection();
$blogStmt = $db->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC");
$posts = $blogStmt->fetchAll();

// Featured post (latest one)
$featuredPost = $posts[0] ?? null;
$otherPosts = array_slice($posts, 1);
?>

<!-- Immersive Hero -->
<section class="blog-hero-immersive">
    <div class="blog-hero-bg">
        <img src="<?php echo $featuredPost ? SITE_URL . '/uploads/' . $featuredPost['featured_image'] : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920'; ?>" alt="Hero BG">
    </div>
    <div class="blog-hero-overlay"></div>
    <div class="container">
        <div class="blog-hero-content reveal-up">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-4">Latest Insight</span>
            <?php if ($featuredPost): ?>
                <h1 class="display-2 font-weight-900 mb-4"><?php echo htmlspecialchars($featuredPost['title']); ?></h1>
                <p class="lead mb-5 opacity-75" style="max-width: 600px;"><?php echo htmlspecialchars($featuredPost['excerpt']); ?></p>
                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $featuredPost['slug']; ?>" class="btn btn-primary btn-lg rounded-pill px-5">Read Experience <i class="fas fa-arrow-right ms-2"></i></a>
            <?php else: ?>
                <h1 class="display-2 font-weight-900 mb-4">Exploring the <span class="text-gradient">Digital Frontier</span></h1>
                <p class="lead mb-5 opacity-75" style="max-width: 600px;">Join our experts as we dive deep into the technologies and strategies defining the next generation of business.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Magazine Grid Section -->
<section class="section-padding-fluid bg-white">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h2 class="display-5 font-weight-900 mb-0">More <span class="text-gradient">Insights</span></h2>
            </div>
            <div class="col-md-6 text-md-end mt-4 mt-md-0">
                <div class="d-flex justify-content-md-end gap-3 font-weight-700 small text-uppercase tracking-widest text-muted">
                    <span class="text-primary border-bottom border-2 border-primary pb-1">All Topics</span>
                    <span class="hover-primary transition-all pointer pb-1">Strategy</span>
                    <span class="hover-primary transition-all pointer pb-1">Development</span>
                </div>
            </div>
        </div>

        <div class="blog-grid-bento">
            <?php foreach ($otherPosts as $index => $post): 
                $isLarge = ($index % 5 === 0);
            ?>
            <div class="bento-card <?php echo $isLarge ? 'large' : ''; ?> reveal-up" style="animation-delay: <?php echo 0.1 * ($index % 3); ?>s;">
                <article class="card border-0 shadow-sm h-100 overflow-hidden rounded-5 transition-all hover-translate-y">
                    <div class="position-relative overflow-hidden" style="height: <?php echo $isLarge ? '450px' : '280px'; ?>;">
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $post['featured_image']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <div class="position-absolute top-0 start-0 m-4">
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill shadow-sm"><?php echo htmlspecialchars($post['category']); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-<?php echo $isLarge ? '5' : '4'; ?> bg-white">
                        <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                            <span><i class="far fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                            <span><i class="far fa-clock text-primary me-1"></i> 5m read</span>
                        </div>
                        <h3 class="<?php echo $isLarge ? 'display-6' : 'h4'; ?> font-weight-800 mb-4">
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="text-dark text-decoration-none hover-primary">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        <p class="text-muted <?php echo $isLarge ? 'lead' : ''; ?> mb-5"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="font-weight-800 text-primary text-uppercase tracking-widest small text-decoration-none">
                            Full Experience <i class="fas fa-chevron-right ms-2 mt-n1"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($posts)): ?>
            <div class="col-12 text-center py-5">
                <div class="glass-card p-5 rounded-5 border-0 bg-light">
                    <i class="fas fa-feather-alt fa-3x text-muted mb-4 opacity-50"></i>
                    <h3 class="font-weight-800">No insights yet.</h3>
                    <p class="text-muted">Stay tuned for expert-led content coming your way.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'views/partials/footer.php'; ?>
