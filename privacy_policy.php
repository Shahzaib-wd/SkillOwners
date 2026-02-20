<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<!-- Immersive Header -->
<section class="contact-hero">
    <div class="container text-center">
        <span class="hero-tag reveal-up">
            <i class="fas fa-shield-alt"></i>
            <span>Legal Compliance</span>
        </span>
        <h1 class="hero-title reveal-up" style="animation-delay: 0.2s;">Privacy <span class="text-gradient">Policy</span></h1>
        <p class="hero-description mx-auto text-center reveal-up" style="animation-delay: 0.3s;">
            Last updated: <?php echo date('F d, Y'); ?>. Your data security is our absolute priority.
        </p>
    </div>
</section>

<div class="container py-5 mt-n5" style="position: relative; z-index: 10;">
    <div class="row g-5">
        <!-- Sidebar Navigation -->
        <div class="col-lg-4 reveal-left">
            <div class="form-glass p-4 sticky-top" style="top: 100px;">
                <h4 class="font-weight-800 mb-4 small text-uppercase tracking-widest text-primary">Table of Contents</h4>
                <nav class="legal-nav d-flex flex-column gap-2">
                    <a href="#info-collection" class="legal-link active">1. Information Collection</a>
                    <a href="#info-usage" class="legal-link">2. Usage Protocols</a>
                    <a href="#data-security" class="legal-link">3. Security Framework</a>
                    <a href="#user-rights" class="legal-link">4. Your Data Rights</a>
                    <a href="#cookies" class="legal-link">5. Tracking Systems</a>
                    <a href="#contact" class="legal-link">6. Global Support</a>
                </nav>
            </div>
        </div>

        <!-- content -->
        <div class="col-lg-8 reveal-right">
            <div class="form-glass p-5">
                <p class="lead font-weight-600 text-dark mb-5">
                    At Skill Owners, we architect solutions with privacy by design. This policy outlines our commitment to protecting the digital identity of our clients and partners.
                </p>

                <div class="legal-sections text-muted" style="line-height: 1.8;">
                    <section id="info-collection" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">1. Information Collection</h2>
                        <p>We only acquire essential data required to provide our technical services and facilitate platform interactions:</p>
                        <ul class="d-flex flex-column gap-2 mt-3">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Professional Identity:</strong> Corporate name, contact credentials, and verified emails.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Project Parameters:</strong> Discovery documents, technical requirements, and communication logs.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>System Analytics:</strong> Anonymized interaction data to optimize platform performance.</li>
                        </ul>
                    </section>

                    <section id="info-usage" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">2. Usage Protocols</h2>
                        <p>Your information is utilized exclusively for service delivery and operational optimization:</p>
                        <ul class="d-flex flex-column gap-2 mt-3">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Executing project milestones and technical deliverables.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Facilitating secure agency-client collaborations.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Compliance with jurisdictional legal requirements.</li>
                        </ul>
                    </section>

                    <section id="data-security" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">3. Security Framework</h2>
                        <p>We deploy advanced cybersecurity protocols to ensure end-to-end data integrity. Our framework includes enterprise-grade encryption and secure localized storage.</p>
                    </section>

                    <section id="user-rights" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">4. Your Data Rights</h2>
                        <p>Under global data protection regulations, you maintain full sovereignty over your data. You may request access, rectification, or complete erasure of your business data at any time.</p>
                    </section>

                    <section id="cookies" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">5. Tracking Systems</h2>
                        <p>We utilize functional tracking to maintain session integrity and enhance user experience. These systems are non-intrusive and strictly operational.</p>
                    </section>

                    <section id="contact" class="mb-0">
                        <h2 class="h4 font-weight-800 text-dark mb-4">6. Global Support</h2>
                        <p>For inquiries regarding our privacy infrastructure, please contact our Legal Compliance team:</p>
                        <div class="mt-4 p-4 rounded-4 bg-light">
                            <p class="mb-2"><strong>Primary Contact:</strong> <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="text-primary text-decoration-none"><?php echo ADMIN_EMAIL; ?></a></p>
                            <p class="mb-0"><strong>HQ Operations:</strong> Tech District, London, United Kingdom</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.legal-link {
    color: #64748b;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}
.legal-link:hover, .legal-link.active {
    background: hsla(150, 100%, 35%, 0.1);
    color: var(--primary);
}
.mt-n5 {
    margin-top: -5rem !important;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.legal-link');

        function updateActive() {
            let fromTop = window.scrollY + 200;
            
            sections.forEach(section => {
                if (section.offsetTop <= fromTop && section.offsetTop + section.offsetHeight > fromTop) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + section.id) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }

        window.addEventListener('scroll', updateActive);
        updateActive();
    });
</script>

<?php include 'views/partials/footer.php'; ?>