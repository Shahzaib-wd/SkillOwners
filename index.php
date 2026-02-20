<?php
/**
 * Skill Owners - Home Page
 * Main landing page with hero, categories, how it works, and CTA sections
 */

require_once 'config.php';
include 'views/partials/header.php';
?>

<!-- Hero Section -->
<section class="hero-section grid-bg">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-left">
                <h1 class="hero-title reveal-right" style="animation-delay: 0.3s;">
                    Transforming visions into <br>
                    <span class="text-gradient">high-performance reality.</span>
                </h1>

                <p class="hero-description reveal-right" style="animation-delay: 0.4s;">
                    We are a premier digital agency specializing in bespoke software, high-conversion marketing, and strategic brand growth. Partner with us to dominate your market.
                </p>

                <div class="cta-btn-group reveal-right" style="margin-bottom: 2rem; animation-delay: 0.5s;">
                    <a href="<?php echo SITE_URL; ?>/request_quote" class="btn btn-primary btn-lg">Start Your Project</a>
                    <a href="<?php echo SITE_URL; ?>/portfolio" class="btn btn-outline btn-lg ms-3">View Our Work</a>
                </div>

                <div class="hero-proof reveal-right" style="animation-delay: 0.7s;">
                    <div class="avatar-group">
                        <img src="https://i.pravatar.cc/100?u=a" class="avatar" alt="User">
                        <img src="https://i.pravatar.cc/100?u=b" class="avatar" alt="User">
                        <img src="https://i.pravatar.cc/100?u=c" class="avatar" alt="User">
                    </div>
                    <div class="proof-info">
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p>Trusted by Global Industry Leaders</p>
                    </div>
                </div>
            </div>

            <div class="hero-right reveal-left" style="animation-delay: 0.5s;">
                <div class="hero-cards-gallery">
                    <!-- Card 1: Software Architecture -->
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80" class="flip-card-img" alt="Infrastructure">
                                <div class="flip-card-content">
                                    <h3>Software Architecture</h3>
                                    <p>Engineering scalable, enterprise-grade systems with architectural precision.</p>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <span class="back-header">Technical Core</span>
                                <h3 class="back-title">Backend Systems</h3>
                                <div class="back-stats">
                                    <div class="stat-row"><span>Uptime SLA</span><span>99.99%</span></div>
                                    <div class="stat-row"><span>Security Level</span><span>Military</span></div>
                                    <div class="stat-row"><span>Load Capacity</span><span>1M+ RPM</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Brand Design -->
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <img src="https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=600&q=80" class="flip-card-img" alt="Design">
                                <div class="flip-card-content">
                                    <h3>Creative Direction</h3>
                                    <p>Forging high-fidelity brand identities that command market attention.</p>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <span class="back-header">Visual Impact</span>
                                <h3 class="back-title">Brand UI/UX</h3>
                                <div class="back-stats">
                                    <div class="stat-row"><span>Conversion Lift</span><span>+45%</span></div>
                                    <div class="stat-row"><span>Retention</span><span>High</span></div>
                                    <div class="stat-row"><span>Industry Leads</span><span>Top Tier</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Growth Marketing -->
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80" class="flip-card-img" alt="Marketing">
                                <div class="flip-card-content">
                                    <h3>Growth Strategies</h3>
                                    <p>Data-driven performance marketing designed for exponential ROI.</p>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <span class="back-header">Market Results</span>
                                <h3 class="back-title">Performance</h3>
                                <div class="back-stats">
                                    <div class="stat-row"><span>Avg. ROI</span><span>300%</span></div>
                                    <div class="stat-row"><span>Market Share</span><span>Growth</span></div>
                                    <div class="stat-row"><span>Lead Quality</span><span>Verified</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Stats Section -->
<section class="stats-section">
    <div class="stats-mesh"></div>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item reveal-up" style="animation-delay: 0.1s;">
                <div class="stat-number">500+</div>
                <div class="stat-label">Clients Scaled</div>
            </div>
            <div class="stat-item reveal-up" style="animation-delay: 0.2s;">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Project Reliability</div>
            </div>
            <div class="stat-item reveal-up" style="animation-delay: 0.3s;">
                <div class="stat-number">4.9/5</div>
                <div class="stat-label">Client Satisfaction</div>
            </div>
            <div class="stat-item reveal-up" style="animation-delay: 0.4s;">
                <div class="stat-number">$100M+</div>
                <div class="stat-label">Revenue Generated</div>
            </div>
        </div>
    </div>
</section>

<!-- Solutions Section -->
<section class="categories-section" id="solutions">
    <div class="category-mesh-bg"></div>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Solutions That <span class="text-gradient">Scale</span></h2>
            <p class="section-description">
                Tailored digital strategies designed to drive measurable business impact.
            </p>
        </div>
        
        <div class="categories-grid">
            <a href="<?php echo SITE_URL; ?>/services#web-dev" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-code"></i>
                </div>
                <span class="category-name">Enterprise Development</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/services#seo" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-search"></i>
                </div>
                <span class="category-name">SEO & Authority</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/services#marketing" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="category-name">Growth Marketing</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/services#paid-ads" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-ad"></i>
                </div>
                <span class="category-name">Performance Ads</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/services#content" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-pen-nib"></i>
                </div>
                <span class="category-name">Content Strategy</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/services#branding" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <span class="category-name">Identity & Branding</span>
            </a>
        </div>
    </div>
</section>

<!-- Strategy Section -->
<section class="trust-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">The <span class="text-gradient">Strategic</span> Advantage</h2>
            <p class="section-description">
                Why industry leaders choose us to lead their digital evolution.
            </p>
        </div>
        
        <div class="trust-grid">
            <div class="trust-card reveal-up" style="animation-delay: 0.1s;">
                <div class="trust-icon-box">
                    <i class="fas fa-chess-knight"></i>
                </div>
                <h3>Senior Technical Leads</h3>
                <p>Every project is spearheaded by a veteran lead with a proven track record in complex enterprise environments.</p>
            </div>
            
            <div class="trust-card reveal-up" style="animation-delay: 0.2s;">
                <div class="trust-icon-box">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3>End-to-End Delivery</h3>
                <p>From initial discovery to post-launch optimization, we manage every phase of the lifecycle with precision.</p>
            </div>
            
            <div class="trust-card reveal-up" style="animation-delay: 0.3s;">
                <div class="trust-icon-box">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Guaranteed Performance</h3>
                <p>Our solutions are built with security and scalability at the core, backed by rigorous testing and deployment standards.</p>
            </div>
        </div>
    </div>
</section>



<!-- [Bento Section - Untouched as requested] -->
<section class="f-bento-section">
    <div class="f-mesh-bg"></div>
    
    <div class="f-container">
        <div class="f-header">
            <div class="f-title-col">
                <span class="f-badge">Engineered for Domination</span>
                <h2 class="f-title">
                    Architecting <span class="f-text-gradient">Future-Proof</span><br> Digital Ecosystems.
                </h2>
                <p class="f-description">
                    We blend senior-level technical expertise with aggressive growth strategies to turn visionary ideas into market-leading platforms.
                </p>
            </div>
        </div>

        <div class="f-grid">
            <div class="f-bento-card large">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80" 
                     class="f-card-img" alt="Digital Strategy">
                <div class="f-card-overlay"></div>
                
                <div class="relative z-10">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="f-pulse"></div>
                        <span class="text-white opacity-50 small font-weight-700 text-uppercase tracking-widest">Full-Stack Mastery</span>
                    </div>
                    
                    <h3 class="h1 font-weight-800 mb-3">Custom Systems</h3>
                    <p class="text-muted mb-4 lead">From custom SaaS platforms to enterprise automation, we build for high-stakes performance.</p>
                    <a href="<?php echo SITE_URL; ?>/request_quote" class="f-btn-premium" style="text-decoration: none; display: inline-block;">Request a Quote</a>
                </div>
            </div>

            <div class="f-side-col">
                <div class="f-bento-card flex-row align-items-center justify-content-between" style="min-height: 200px;">
                    <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80" 
                         class="f-card-img" alt="AI Agents">
                    <div class="f-card-overlay" style="background: linear-gradient(to right, #050505 40%, transparent);"></div>
                    
                    <div class="relative z-10" style="max-width: 60%">
                        <h3 class="h4 font-weight-700 mb-1">AI Engineering</h3>
                        <p class="text-muted small mb-0">Integrate advanced LLMs to revolutionize your business operations.</p>
                    </div>
                    <div class="f-icon-box relative z-10">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: hsl(var(--f-primary))">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="f-stat-row">
                    <div class="f-bento-card align-items-center text-center justify-content-center" style="min-height: 180px;">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=600&q=80" 
                             class="f-card-img" alt="Satisfaction">
                        <div class="f-card-overlay" style="background: radial-gradient(circle, transparent, #050505 90%);"></div>
                        
                        <div class="relative z-10">
                            <span class="h1 font-weight-800 mb-0" style="color: white; transition: color 0.3s;" onmouseover="this.style.color='hsl(var(--f-primary))'" onmouseout="this.style.color='white'">99.8%</span>
                            <span class="text-muted smaller font-weight-700 text-uppercase tracking-widest d-block" style="font-size: 0.6rem">Satisfaction</span>
                        </div>
                    </div>
                    <div class="f-bento-card justify-content-center" style="min-height: 180px;">
                        <img src="https://images.unsplash.com/photo-152202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80" 
                             class="f-card-img" alt="Experts">
                        <div class="f-card-overlay" style="background: linear-gradient(to top, #050505, transparent);"></div>
                        
                        <div class="relative z-10">
                            <div class="d-flex mb-2" style="margin-left: 0.75rem">
                                <img src="https://i.pravatar.cc/100?u=1" class="rounded-circle border border-dark" style="width: 2rem; height: 2rem; margin-left: -0.75rem; object-fit: cover;">
                                <img src="https://i.pravatar.cc/100?u=2" class="rounded-circle border border-dark" style="width: 2rem; height: 2rem; margin-left: -0.75rem; object-fit: cover;">
                                <div class="rounded-circle border border-dark d-flex align-items-center justify-content-center text-white" style="width: 2rem; height: 2rem; margin-left: -0.75rem; background: hsl(var(--f-primary)); font-size: 0.6rem; font-weight: 800;">+</div>
                            </div>
                            <span class="font-weight-700 small line-height-1 text-white">Platform Experts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Insights Section -->
<section class="blog-preview-section py-5">
    <div class="container py-5">
        <div class="row align-items-end mb-5">
            <div class="col-lg-6">
                <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill mb-3">Industry Insights</span>
                <h2 class="display-5 font-weight-900 mb-0">Latest from our <br><span class="text-gradient">Experts</span></h2>
            </div>
            <div class="col-lg-6 text-lg-end mt-4 mt-lg-0">
                <a href="<?php echo SITE_URL; ?>/blog" class="btn btn-outline-secondary rounded-pill px-4">View All Insights <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $db = getDBConnection();
            $homeBlogStmt = $db->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
            $homePosts = $homeBlogStmt->fetchAll();

            foreach ($homePosts as $index => $post):
            ?>
            <div class="col-lg-4 col-md-6 reveal-up" style="animation-delay: <?php echo 0.1 * ($index + 1); ?>s;">
                <article class="card border-0 shadow-sm h-100 overflow-hidden rounded-4 transition-all hover-translate-y">
                    <div class="position-relative overflow-hidden" style="height: 240px;">
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $post['featured_image']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-dark px-3 py-2 rounded-pill"><?php echo htmlspecialchars($post['category']); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-muted small mb-2"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></div>
                        <h4 class="font-weight-800 mb-3">
                            <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="text-dark text-decoration-none hover-primary">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h4>
                        <p class="text-muted small mb-4"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $post['slug']; ?>" class="font-weight-700 text-primary small text-decoration-none">
                            Read Article <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; if(empty($homePosts)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted lead">New insights coming soon. Stay tuned!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.hover-translate-y:hover {
    transform: translateY(-10px);
}
.transition-all {
    transition: all 0.3s ease;
}
.bg-primary-soft {
    background-color: rgba(16, 185, 129, 0.1);
}
</style>

<!-- Final CTA Section -->
<section class="cta-section" id="partner-up">
    <div class="container">
        <div class="glass-cta">
            <div class="cta-grid-bg"></div>
            <div class="cta-content">
                <span class="cta-badge">Partner With Peak Performance</span>
                <h2 class="cta-title-v2">Ready to dominate your <br><span class="text-gradient">digital landscape?</span></h2>
                <p class="cta-desc-v2">
                    Stop settling for average results. Let's build the digital future of your business together.
                </p>
                <div class="cta-btn-group">
                    <a href="<?php echo SITE_URL; ?>/request_quote" class="cta-btn-glow">
                        Schedule Free Audit
                        <i class="fas fa-chart-line ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'views/partials/footer.php'; ?>

<script>
    document.querySelectorAll('.f-bento-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--fx', `${e.clientX - rect.left}px`);
            card.style.setProperty('--fy', `${e.clientY - rect.top}px`);
        });
    });
</script>

