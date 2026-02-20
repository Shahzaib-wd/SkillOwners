<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<section class="about-hero">
    <div class="container text-center">
        <h1 class="display-3 font-weight-900 mb-4 reveal-up">Our <span class="text-gradient">Case Studies</span></h1>
        <p class="lead mb-5 opacity-75 mx-auto reveal-up" style="max-width: 700px; animation-delay: 0.1s;">
            We don't just deliver projects; we deliver results. Explore some of our latest success stories.
        </p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">
        <?php
        $db = getDBConnection();
        $portfolioStmt = $db->query("SELECT * FROM portfolio_projects ORDER BY created_at DESC");
        $projects = $portfolioStmt->fetchAll();

        if (empty($projects)):
            // Static Demo Content if DB is empty
            for ($i = 1; $i <= 3; $i++):
        ?>
            <div class="col-lg-4 col-md-6 reveal-up" style="animation-delay: <?php echo 0.1 * $i; ?>s;">
                <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-4">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Project">
                        <div class="badge bg-primary position-absolute top-0 end-0 m-3">Web Development</div>
                    </div>
                    <div class="card-body p-4">
                        <h4 class="font-weight-800 mb-2 text-dark">Enterprise Dashboard</h4>
                        <p class="text-muted small mb-4 line-clamp-2">A comprehensive management system for a global logistics company, focused on efficiency and real-time tracking.</p>
                        <div class="d-flex justify-content-between align-items-center">
                        </div>
                    </div>
                </div>
            </div>
        <?php endfor; else: foreach ($projects as $index => $project): ?>
            <div class="col-lg-4 col-md-6 reveal-up" style="animation-delay: <?php echo 0.1 * ($index + 1); ?>s;">
                <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-4">
                    <div class="position-relative">
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $project['main_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <div class="badge bg-primary position-absolute top-0 end-0 m-3"><?php echo htmlspecialchars($project['category']); ?></div>
                    </div>
                    <div class="card-body p-4">
                        <h4 class="font-weight-800 mb-2 text-dark"><?php echo htmlspecialchars($project['title']); ?></h4>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars(substr($project['problem'], 0, 100)); ?>...</p>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<section class="cta-section py-5">
    <div class="container text-center py-5">
        <h2 class="display-6 font-weight-800 mb-3">Want Your Project Featured Here?</h2>
        <p class="lead text-muted mb-4 opacity-75">Let's build something extraordinary together.</p>
        <a href="<?php echo SITE_URL; ?>/request_quote" class="btn btn-primary btn-lg px-5">Start Your Project</a>
    </div>
</section>

<?php include 'views/partials/footer.php'; ?>
