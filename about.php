<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container text-center">
        <h1 class="display-3 font-weight-900 mb-4 reveal-up">We Are <span class="text-gradient">Skill Owners</span></h1>
        <p class="lead mb-5 opacity-75 mx-auto reveal-up" style="max-width: 700px; animation-delay: 0.1s;">
            The marketplace built for the future of work. We connect visionary businesses with world-class talent to build extraordinary things.
        </p>
    </div>
</section>

<!-- Stats Grid -->
<div class="container" style="margin-top: -4rem; position: relative; z-index: 2;">
    <div class="row g-4 justify-content-center">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center py-4 h-100 reveal-up" style="animation-delay: 0.2s;">
                <h3 class="display-5 font-weight-800 text-primary mb-1">10k+</h3>
                <p class="text-muted font-weight-600 mb-0 small text-uppercase tracking-wide">Active Experts</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center py-4 h-100 reveal-up" style="animation-delay: 0.3s;">
                <h3 class="display-5 font-weight-800 text-primary mb-1">50k+</h3>
                <p class="text-muted font-weight-600 mb-0 small text-uppercase tracking-wide">Projects Done</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center py-4 h-100 reveal-up" style="animation-delay: 0.4s;">
                <h3 class="display-5 font-weight-800 text-primary mb-1">98%</h3>
                <p class="text-muted font-weight-600 mb-0 small text-uppercase tracking-wide">Success Rate</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center py-4 h-100 reveal-up" style="animation-delay: 0.5s;">
                <h3 class="display-5 font-weight-800 text-primary mb-1">24/7</h3>
                <p class="text-muted font-weight-600 mb-0 small text-uppercase tracking-wide">Support</p>
            </div>
        </div>
    </div>
</div>

<!-- Mission Section -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 reveal-left">
                <span class="badge bg-primary-soft text-primary mb-3">Our Mission</span>
                <h2 class="display-5 font-weight-800 mb-4">Empowering the <br>World's Talent</h2>
                <p class="lead text-muted mb-4">
                    We believe that talent refers to what you can do, not where you are located. Our mission is to bridge the gap between skilled professionals and the businesses that need them.
                </p>
                <div class="d-flex align-items-start mb-4">
                    <div class="about-icon-box flex-shrink-0 me-4">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div>
                        <h4 class="font-weight-700">Global Reach</h4>
                        <p class="text-muted">Access top-tier talent from over 100 countries, vetted and ready to work.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="about-icon-box flex-shrink-0 me-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-weight-700">Secure Payments</h4>
                        <p class="text-muted">Your funds are protected with our robust escrow system until you are satisfied.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 reveal-right">
                <div class="position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-10 rounded-pill" style="transform: rotate(-6deg) scale(0.9);"></div>
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Team Collaboration" class="img-fluid rounded-3 shadow-lg position-relative">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Grid -->
<section class="bg-light py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="font-weight-800 display-6">Why Choose Skill Owners?</h2>
            <p class="text-muted lead">Built for reliability, speed, and quality.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 text-center reveal-up">
                    <div class="about-icon-box mx-auto bg-blue-soft text-blue">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Smart Matching</h4>
                    <p class="text-muted">Our algorithm connects you with the perfect expert for your specific needs instantly.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 text-center reveal-up" style="animation-delay: 0.1s;">
                    <div class="about-icon-box mx-auto bg-purple-soft text-purple">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4>Quality Assured</h4>
                    <p class="text-muted">We verify skills and identities so you can hire with absolute confidence.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 text-center reveal-up" style="animation-delay: 0.2s;">
                    <div class="about-icon-box mx-auto bg-orange-soft text-orange">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p class="text-muted">Our dedicated support team is always here to help resolve any issues quickly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'views/partials/footer.php'; ?>
