<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<!-- Immersive Header -->
<section class="contact-hero">
    <div class="container text-center">
        <span class="hero-tag reveal-up">
            <i class="fas fa-handshake"></i>
            <span>Professional Agreement</span>
        </span>
        <h1 class="hero-title reveal-up" style="animation-delay: 0.2s;">Terms of <span class="text-gradient">Service</span></h1>
        <p class="hero-description mx-auto text-center reveal-up" style="animation-delay: 0.3s;">
            Last updated: <?php echo date('F d, Y'); ?>. Please review our collaboration framework.
        </p>
    </div>
</section>

<div class="container py-5 mt-n5" style="position: relative; z-index: 10;">
    <div class="row g-5">
        <!-- Sidebar Navigation -->
        <div class="col-lg-4 reveal-left">
            <div class="form-glass p-4 sticky-top" style="top: 100px;">
                <h4 class="font-weight-800 mb-4 small text-uppercase tracking-widest text-primary">Agreement Sections</h4>
                <nav class="legal-nav d-flex flex-column gap-2">
                    <a href="#acceptance" class="legal-link active">1. Acceptance</a>
                    <a href="#engagement" class="legal-link">2. Engagement Models</a>
                    <a href="#service-standards" class="legal-link">3. Service Standards</a>
                    <a href="#conduct" class="legal-link">4. Professional Conduct</a>
                    <a href="#termination" class="legal-link">5. Termination</a>
                    <a href="#liability" class="legal-link">6. Liability Framework</a>
                </nav>
            </div>
        </div>

        <!-- content -->
        <div class="col-lg-8 reveal-right">
            <div class="form-glass p-5">
                <p class="lead font-weight-600 text-dark mb-5">
                    Welcome to Skill Owners. These Terms of Service constitute a legally binding infrastructure for our professional collaboration and service delivery.
                </p>

                <div class="legal-sections text-muted" style="line-height: 1.8;">
                    <section id="acceptance" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">1. Acceptance of Terms</h2>
                        <p>By engaging with our platform or services, you enter into a binding agreement with Skill Owners. If these terms do not align with your corporate policies, please terminate engagement immediately.</p>
                    </section>

                    <section id="engagement" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">2. Engagement Models</h2>
                        <p>We provide various engagement models for our clients, ranging from project-based deliverables to long-term technical partnerships:</p>
                        <ul class="d-flex flex-column gap-2 mt-3">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Registration:</strong> All accounts must provide verified corporate data.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Account Integrity:</strong> You are responsible for maintaining the security of your access credentials.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Notification:</strong> Immediate reporting of unauthorized system access is mandatory.</li>
                        </ul>
                    </section>

                    <section id="service-standards" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">3. Service Standards</h2>
                        <p>Our commitment to excellence ensures high-quality deliverables across all vertical markets:</p>
                        <ul class="d-flex flex-column gap-2 mt-3">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Quality Assurance:</strong> All deliverables undergo rigorous internal auditing.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Project Lifecycle:</strong> Clearly defined milestones and communication protocols.</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> <strong>Technical Support:</strong> Guaranteed response times based on your engagement tier.</li>
                        </ul>
                    </section>

                    <section id="conduct" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">4. Professional Conduct</h2>
                        <p>We maintain a high-stakes professional environment. Prohibited activities include but are not limited to:</p>
                        <ul class="d-flex flex-column gap-2 mt-3">
                            <li><i class="fas fa-times-circle text-danger me-2"></i> Distribution of infringing or destructive technical content.</li>
                            <li><i class="fas fa-times-circle text-danger me-2"></i> Intentional interference with system operational integrity.</li>
                            <li><i class="fas fa-times-circle text-danger me-2"></i> Misrepresentation of corporate or individual expertise.</li>
                        </ul>
                    </section>

                    <section id="termination" class="mb-5">
                        <h2 class="h4 font-weight-800 text-dark mb-4">5. Termination</h2>
                        <p>Engagement may be terminated by either party following the stipulated notice period or immediately upon evidence of system abuse or contractual breach.</p>
                    </section>

                    <section id="liability" class="mb-0">
                        <h2 class="h4 font-weight-800 text-dark mb-4">6. Liability Framework</h2>
                        <p>Skill Owners maintains a defined liability framework. We are not responsible for indirect, incidental, or consequential damages resulting from project execution beyond the scope of defined deliverables.</p>
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