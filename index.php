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
                    We Build Websites &<br>
                    <span class="text-gradient">Rank Them on Google.</span>
                </h1>

                <p class="hero-description reveal-right" style="animation-delay: 0.4s;">
                    If your business isn't online, you're invisible to 90% of your customers. We design high-performance websites and use proven SEO strategies to put you on page one — where the money is.
                </p>

                <div class="cta-btn-group reveal-right" style="margin-bottom: 2rem; animation-delay: 0.5s;">
                    <a href="<?php echo SITE_URL; ?>/request_quote" class="btn btn-primary btn-lg">Start Your Project</a>
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
                    <!-- Card 1: Website Development -->
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80" class="flip-card-img" alt="Website Development">
                                <div class="flip-card-content">
                                    <h3>Website Development</h3>
                                    <p>Custom-built, high-converting websites that turn visitors into paying customers.</p>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <span class="back-header">We Build It</span>
                                <h3 class="back-title">Your Website</h3>
                                <div class="back-stats">
                                    <div class="stat-row"><span>Mobile-First</span><span>100%</span></div>
                                    <div class="stat-row"><span>Load Speed</span><span><1s</span></div>
                                    <div class="stat-row"><span>Conversion Rate</span><span>3x Higher</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: SEO & Rankings -->
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <img src="https://images.unsplash.com/photo-1562577309-4932fdd64cd1?auto=format&fit=crop&w=600&q=80" class="flip-card-img" alt="SEO Services">
                                <div class="flip-card-content">
                                    <h3>SEO & Rankings</h3>
                                    <p>We get your website to page one of Google so customers find you first.</p>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <span class="back-header">We Rank It</span>
                                <h3 class="back-title">Page One</h3>
                                <div class="back-stats">
                                    <div class="stat-row"><span>Organic Traffic</span><span>+300%</span></div>
                                    <div class="stat-row"><span>Keyword Ranks</span><span>Top 10</span></div>
                                    <div class="stat-row"><span>ROI</span><span>Proven</span></div>
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
                <div class="stat-number">150+</div>
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
                <div class="stat-number">$100K+</div>
                <div class="stat-label">Revenue Generated</div>
            </div>
        </div>
    </div>
</section>

<!-- Why Your Business Needs a Website -->
<section class="categories-section" id="solutions">
    <div class="category-mesh-bg"></div>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">No Website = <span class="text-gradient">No Business</span></h2>
            <p class="section-description">
                97% of consumers search online before buying. If you don't have a professional website, your competitors are stealing your customers right now.
            </p>
        </div>
        
        <div class="solutions-grid-2">
            <a href="<?php echo SITE_URL; ?>/services" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-code"></i>
                </div>
                <span class="category-name">Website Development</span>
                <p class="category-desc text-muted small mt-2 mb-0 text-center">We build fast, modern, mobile-first websites that convert visitors into customers.</p>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/services" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-search"></i>
                </div>
                <span class="category-name">SEO & Google Rankings</span>
                <p class="category-desc text-muted small mt-2 mb-0 text-center">We rank your website on page one of Google so customers find you — not your competition.</p>
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
                    <img src="https://images.unsplash.com/photo-1562577309-4932fdd64cd1?auto=format&fit=crop&w=800&q=80" 
                         class="f-card-img" alt="SEO Rankings">
                    <div class="f-card-overlay" style="background: linear-gradient(to right, #050505 40%, transparent);"></div>
                    
                    <div class="relative z-10" style="max-width: 60%">
                        <h3 class="h4 font-weight-700 mb-1">SEO & Rankings</h3>
                        <p class="text-muted small mb-0">Dominate Google search results and drive organic traffic that converts.</p>
                    </div>
                    <div class="f-icon-box relative z-10">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: hsl(var(--f-primary))">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
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

