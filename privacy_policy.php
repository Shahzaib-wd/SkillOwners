<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<div class="legal-header">
    <div class="container text-center">
        <span class="badge bg-green-soft text-green mb-3">Legal</span>
        <h1 class="display-4 font-weight-800 mb-3">Privacy Policy</h1>
        <p class="text-muted lead">Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</div>

<div class="container">
    <div class="legal-content-wrapper">
        <aside class="legal-sidebar">
            <nav class="legal-nav">
                <a href="#info-collection" class="legal-nav-link active">1. Information We Collect</a>
                <a href="#info-usage" class="legal-nav-link">2. How We Use Information</a>
                <a href="#data-security" class="legal-nav-link">3. Data Security</a>
                <a href="#user-rights" class="legal-nav-link">4. Your Rights</a>
                <a href="#cookies" class="legal-nav-link">5. Cookies & Tracking</a>
                <a href="#contact" class="legal-nav-link">6. Contact Us</a>
            </nav>
        </aside>

        <main class="legal-body">
            <p class="lead text-primary mb-5 font-weight-500">
                At Skill Owners, we take your privacy seriously. This policy describes how we collect, use, and protect your personal data when you use our marketplace.
            </p>

            <section id="info-collection">
                <h2>1. Information We Collect</h2>
                <p>We collect information you provide directly to us when you create an account, update your profile, or communicate with us. This may include:</p>
                <ul>
                    <li><strong>Account Information:</strong> Name, email address, password, and profile details.</li>
                    <li><strong>Payment Information:</strong> Billing address and payment method details (processed securely by our payment providers).</li>
                    <li><strong>Usage Data:</strong> Information about how you interact with our services, including IP address, browser type, and device information.</li>
                </ul>
            </section>

            <section id="info-usage">
                <h2>2. How We Use Your Information</h2>
                <p>We use the information we collect to operate, maintain, and improve our services. Specifically, we use it to:</p>
                <ul>
                    <li>Facilitate connections between freelancers and clients.</li>
                    <li>Process payments and payouts securely.</li>
                    <li>Send you technical notices, updates, security alerts, and support messages.</li>
                    <li>Detect, investigate, and prevent fraudulent transactions and other illegal activities.</li>
                </ul>
            </section>

            <section id="data-security">
                <h2>3. Data Security</h2>
                <p>We implement industry-standard security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. We use SSL encryption for data transmission and secure servers for data storage.</p>
                <div class="alert alert-info">
                    <i class="fas fa-shield-alt me-2"></i> All payment data is handled in compliance with PCI-DSS standards.
                </div>
            </section>

            <section id="user-rights">
                <h2>4. Your Rights</h2>
                <p>You have the right to access, correct, or delete your personal information. You can manage most of your data directly through your account settings. If you need assistance, please contact our support team.</p>
            </section>

            <section id="cookies">
                <h2>5. Cookies & Tracking</h2>
                <p>We use cookies and similar tracking technologies to track the activity on our service and hold certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
            </section>

            <section id="contact">
                <h2>6. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                <p><strong>Email:</strong> <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="text-primary"><?php echo ADMIN_EMAIL; ?></a><br>
                <strong>Address:</strong> Skill Owners HQ, Tech District, London, UK</p>
            </section>
        </main>
    </div>
</div>

<script>
    // Simple script to handle active state on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.legal-nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>

<?php include 'views/partials/footer.php'; ?>
