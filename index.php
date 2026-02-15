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
                    Find the perfect <br>
                    <span class="text-gradient">expert for your business.</span>
                </h1>

                <p class="hero-description reveal-right" style="animation-delay: 0.4s;">
                    Work with the top 1% of creative and technical talent across the globe. No hassle, just results.
                </p>

                <div class="premium-search-container reveal-right" style="max-width: 36rem; margin-bottom: 1.5rem; animation-delay: 0.5s;">
                    <i class="fas fa-search"></i>
                    <form action="<?php echo SITE_URL; ?>/browse" method="GET">
                        <input type="text" name="q" placeholder="What service are you looking for today?" required>
                        <button type="submit" class="btn-search">Search</button>
                    </form>
                </div>
                
                <div class="popular-tags reveal-right" style="justify-content: flex-start; margin-bottom: 2rem; animation-delay: 0.6s;">
                    <span style="font-[11px] font-bold text-gray-400 uppercase">Popular:</span>
                    <a href="<?php echo SITE_URL; ?>/browse?q=Web+Design" class="tag">Web Design</a>
                    <a href="<?php echo SITE_URL; ?>/browse?q=WordPress" class="tag">WordPress</a>
                    <a href="<?php echo SITE_URL; ?>/browse?q=Logo+Design" class="tag">Logo Design</a>
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
                        <p>Trusted by 10k+ Founders</p>
                    </div>
                </div>
            </div>

            <div class="hero-right reveal-left" style="animation-delay: 0.5s;">
                <div class="float-card">
                    <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&w=800&q=80" class="card-img" alt="Pro Talent">
                    <div class="card-badge">
                        <p>Verified Pro</p>
                    </div>
                    <div class="card-body">
                        <div class="profile-info" style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h3>David Chen</h3>
                                <p class="role" style="margin-bottom: 0;">Senior Full-Stack Developer</p>
                            </div>
                            <div class="rating" style="font-size: 1rem; font-weight: 800; color: #10b981;">
                                ★ 5.0
                            </div>
                        </div>
                        <div class="profile-experience" style="margin-top: 10px; padding-top: 0; border-top: 1px solid #f9fafb;">
                            <p style="font-size: 0.875rem; color: #6b7280; line-height: 1.6; font-weight: 500; padding-top: 10px;">
                                David has completed over <span style="color: #10b981; font-weight: 700;">150+ high-scale</span> enterprise projects on SkillOwners with a 100% success rate. Specializing in high-performance system architectures.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Global Stats Section -->
<section class="stats-section">
    <div class="stats-mesh"></div>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item reveal-up" style="animation-delay: 0.1s;">
                <div class="stat-number">150k+</div>
                <div class="stat-label">Verified Experts</div>
            </div>
            <div class="stat-item reveal-up" style="animation-delay: 0.2s;">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Global Support</div>
            </div>
            <div class="stat-item reveal-up" style="animation-delay: 0.3s;">
                <div class="stat-number">4.9/5</div>
                <div class="stat-label">Avg. Rating</div>
            </div>
            <div class="stat-item reveal-up" style="animation-delay: 0.4s;">
                <div class="stat-number">1M+</div>
                <div class="stat-label">Projects Completed</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section" id="categories">
    <div class="category-mesh-bg"></div>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-description">
                Explore services across every creative and technical discipline
            </p>
        </div>
        
        <div class="categories-grid">
            <a href="<?php echo SITE_URL; ?>/browse?category=Development" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-code"></i>
                </div>
                <span class="category-name">Development</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Design" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <span class="category-name">Design</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Writing" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-pen-nib"></i>
                </div>
                <span class="category-name">Writing</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Video+Animation" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-video"></i>
                </div>
                <span class="category-name">Video & Animation</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Marketing" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="category-name">Marketing</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Translation" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <span class="category-name">Translation</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Music+Audio" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-music"></i>
                </div>
                <span class="category-name">Music & Audio</span>
            </a>
            
            <a href="<?php echo SITE_URL; ?>/browse?category=Photography" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <span class="category-name">Photography</span>
            </a>
        </div>
    </div>
</section>

<!-- Buyer Confidence Section -->
<section class="trust-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Why Businesses Trust <span class="text-gradient">SkillOwners</span></h2>
            <p class="section-description">
                We provide the tools and talent you need to grow with absolute peace of mind.
            </p>
        </div>
        
        <div class="trust-grid">
            <div class="trust-card reveal-up" style="animation-delay: 0.1s;">
                <div class="trust-icon-box">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Verified Experts Only</h3>
                <p>Every freelancer and agency undergoes a rigorous identity and skill verification process before joining our elite network.</p>
            </div>
            
            <div class="trust-card reveal-up" style="animation-delay: 0.2s;">
                <div class="trust-icon-box">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Direct Collaboration</h3>
                <p>Hire and pay talent directly on your terms. We provide the connections, you manage the relationship with zero platform fees.</p>
            </div>
            
            <div class="trust-card reveal-up" style="animation-delay: 0.3s;">
                <div class="trust-icon-box">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Priority Support</h3>
                <p>Our dedicated success managers are available around the clock to assist you with any questions or project needs.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Agencies Showcase -->
<section class="agency-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Partner with <span class="text-gradient">Elite Agencies</span></h2>
            <p class="section-description">
                Scaling your operations? Collaborate with verified full-service agency teams.
            </p>
        </div>
        
        <div class="agency-grid">
            <div class="agency-card reveal-up" style="animation-delay: 0.1s;">
                <div class="agency-badge">
                    <i class="fas fa-check-shield"></i> Top Rated
                </div>
                <div class="agency-card-logo">
                    <img src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?auto=format&fit=crop&w=200&q=80" alt="Pixel Perfect">
                </div>
                <h3 class="agency-name">Pixel Perfect Std</h3>
                <div class="agency-rating">
                    <i class="fas fa-star"></i> 4.9 (240+ Reviews)
                </div>
                <div class="agency-services">
                    <span class="agency-service-tag">UI/UX Design</span>
                    <span class="agency-service-tag">Branding</span>
                    <span class="agency-service-tag">Webflow</span>
                </div>
                <a href="<?php echo SITE_URL; ?>/browse?type=agency" class="btn agency-btn">Explore Services</a>
            </div>

            <div class="agency-card reveal-up" style="animation-delay: 0.2s;">
                <div class="agency-badge">
                    <i class="fas fa-check-shield"></i> Verified
                </div>
                <div class="agency-card-logo">
                    <img src="https://images.unsplash.com/photo-1549923746-c502d488b3ea?auto=format&fit=crop&w=200&q=80" alt="DevFlow">
                </div>
                <h3 class="agency-name">DevFlow Systems</h3>
                <div class="agency-rating">
                    <i class="fas fa-star"></i> 5.0 (180+ Reviews)
                </div>
                <div class="agency-services">
                    <span class="agency-service-tag">Laravel</span>
                    <span class="agency-service-tag">React</span>
                    <span class="agency-service-tag">App Dev</span>
                </div>
                <a href="<?php echo SITE_URL; ?>/browse?type=agency" class="btn agency-btn">Explore Services</a>
            </div>

            <div class="agency-card reveal-up" style="animation-delay: 0.3s;">
                <div class="agency-badge">
                    <i class="fas fa-check-shield"></i> Enterprise
                </div>
                <div class="agency-card-logo">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=200&q=80" alt="GrowthMasters">
                </div>
                <h3 class="agency-name">GrowthMasters HQ</h3>
                <div class="agency-rating">
                    <i class="fas fa-star"></i> 4.8 (310+ Reviews)
                </div>
                <div class="agency-services">
                    <span class="agency-service-tag">SEO</span>
                    <span class="agency-service-tag">PPC</span>
                    <span class="agency-service-tag">Copywriting</span>
                </div>
                <a href="<?php echo SITE_URL; ?>/browse?type=agency" class="btn agency-btn">Explore Services</a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section" id="how-it-works">
    <div class="steps-flow-line"></div>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">How It Works</h2>
            <p class="section-description">
                Three simple steps to get your project done
            </p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon-wrapper">
                    <i class="fas fa-search step-icon"></i>
                    <span class="step-number">1</span>
                </div>
                <h3 class="step-title">Find the Right Talent</h3>
                <p class="step-description">
                    Browse thousands of verified freelancers and agencies. Filter by skill, budget, and delivery time.
                </p>
            </div>
            
            <div class="step-card">
                <div class="step-icon-wrapper">
                    <i class="fas fa-shield-alt step-icon"></i>
                    <span class="step-number">2</span>
                </div>
                <h3 class="step-title">Connect Directly</h3>
                <p class="step-description">
                    Review portfolios and past work. Message experts directly to discuss terms, budget, and payment methods.
                </p>
            </div>
            
            <div class="step-card">
                <div class="step-icon-wrapper">
                    <i class="fas fa-rocket step-icon"></i>
                    <span class="step-number">3</span>
                </div>
                <h3 class="step-title">Get Results Fast</h3>
                <p class="step-description">
                    Receive deliveries on time. Request revisions, leave reviews, and scale your projects effortlessly.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Premium Bento Section -->
<section class="f-bento-section">
    <div class="f-mesh-bg"></div>
    
    <div class="f-container">
        <div class="f-header">
            <div class="f-title-col">
                <span class="f-badge">Engineered for Excellence</span>
                <h2 class="f-title">
                    Build it <span class="f-text-gradient">Faster.</span><br> Hire the Best.
                </h2>
                <p class="f-description">
                    The next generation of freelance commerce. No noise, just vetted talent working at the speed of thought.
                </p>
            </div>
        </div>

        <div class="f-grid">
            <div class="f-bento-card large">
                <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1200&q=80" 
                     class="f-card-img" alt="Advanced Infrastructure">
                <div class="f-card-overlay"></div>
                
                <div class="relative z-10">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="f-pulse"></div>
                        <span class="text-white opacity-50 small font-weight-700 text-uppercase tracking-widest">Advanced Infrastructure</span>
                    </div>
                    
                    <h3 class="h1 font-weight-800 mb-3">System Architecture</h3>
                    <p class="text-muted mb-4 lead">Deploy scalable, enterprise-grade backends with our senior engineers.</p>
                    <a href="<?php echo SITE_URL; ?>/browse" class="f-btn-premium" style="text-decoration: none; display: inline-block;">Explore Solutions</a>
                </div>
            </div>

            <div class="f-side-col">
                <div class="f-bento-card flex-row align-items-center justify-content-between" style="min-height: 200px;">
                    <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80" 
                         class="f-card-img" alt="AI Agents">
                    <div class="f-card-overlay" style="background: linear-gradient(to right, #050505 40%, transparent);"></div>
                    
                    <div class="relative z-10" style="max-width: 60%">
                        <h3 class="h4 font-weight-700 mb-1">AI Agents</h3>
                        <p class="text-muted small mb-0">Automate workflows with custom LLM integrations.</p>
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


<!-- CTA Section -->
<section class="cta-section" id="get-started">
    <div class="container">
        <div class="glass-cta">
            <div class="cta-grid-bg"></div>
            <div class="cta-content">
                <span class="cta-badge">Join the Future</span>
                <h2 class="cta-title-v2">Ready to scale your <br><span class="text-gradient">vision to reality?</span></h2>
                <p class="cta-desc-v2">
                    Whether you're a founder looking for top-tier talent or an expert ready to work with the best, SkillOwners is your home.
                </p>
                <div class="cta-btn-group">
                    <a href="<?php echo SITE_URL; ?>/browse" class="cta-btn-glow">
                        Hire Top Talent
                        <i class="fas fa-rocket ms-2"></i>
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

