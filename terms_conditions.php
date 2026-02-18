<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<div class="legal-header">
    <div class="container text-center">
        <span class="badge bg-blue-soft text-blue mb-3">Commitment</span>
        <h1 class="display-4 font-weight-800 mb-3">Terms & Conditions</h1>
        <p class="text-muted lead">Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</div>

<div class="container">
    <div class="legal-content-wrapper">
        <aside class="legal-sidebar">
            <nav class="legal-nav">
                <a href="#acceptance" class="legal-nav-link active">1. Acceptance of Terms</a>
                <a href="#accounts" class="legal-nav-link">2. Accounts & Registration</a>
                <a href="#service-limits" class="legal-nav-link">3. Service Limits</a>
                <a href="#payments" class="legal-nav-link">4. Payments & Billing</a>
                <a href="#prohibited" class="legal-nav-link">5. Prohibited Activities</a>
                <a href="#termination" class="legal-nav-link">6. Termination</a>
                <a href="#liability" class="legal-nav-link">7. Limitation of Liability</a>
            </nav>
        </aside>

        <main class="legal-body">
            <p class="lead text-primary mb-5 font-weight-500">
                Welcome to Skill Owners. These Terms & Conditions constitute a legally binding agreement between you and Skill Owners regarding your use of our website and services.
            </p>

            <section id="acceptance">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using our Service, you agree to be bound by these Terms & Conditions. If you disagree with any part of the terms, you may not access the Service.</p>
            </section>

            <section id="accounts">
                <h2>2. Accounts & Registration</h2>
                <p>When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms.</p>
                <ul>
                    <li>You are responsible for safeguarding the password that you use to access the Service.</li>
                    <li>You agree not to disclose your password to any third party.</li>
                    <li>You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</li>
                </ul>
            </section>

            <section id="service-limits">
                <h2>3. Service Limits</h2>
                <p>Current account limits to ensure quality of service:</p>
                <ul>
                    <li><strong>Freelancers:</strong> Maximum 5 active Gigs, 3 active Projects.</li>
                    <li><strong>Agencies:</strong> Maximum 5 active Gigs, unlimited team members.</li>
                    <li><strong>Messaging:</strong> Buyers must initiate contact. Spam filters are active.</li>
                </ul>
            </section>

            <section id="payments">
                <h2>4. Payments & Billing</h2>
                <p>All payments are processed securely. Funds are held in escrow until the project is marked complete by the buyer.</p>
                <ul>
                    <li><strong>Service Fees:</strong> We charge a service fee on all transactions.</li>
                    <li><strong>Withdrawals:</strong> Processed within 3-5 business days.</li>
                    <li><strong>Refunds:</strong> Subject to our Refund Policy and dispute resolution process.</li>
                </ul>
            </section>

            <section id="prohibited">
                <h2>5. Prohibited Activities</h2>
                <p>You agree not to engage in any of the following activities:</p>
                <ul>
                    <li>Posting content that is infringing, libelous, defamatory, obscene, pornographic, abusive, or offensive.</li>
                    <li>Using the Service for any illegal purpose.</li>
                    <li>Attempting to interfere with the proper working of the Service (e.g., hacking, spamming).</li>
                    <li>Creating multiple accounts to bypass restrictions.</li>
                </ul>
            </section>

            <section id="termination">
                <h2>6. Termination</h2>
                <p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>
            </section>

            <section id="liability">
                <h2>7. Limitation of Liability</h2>
                <p>In no event shall Skill Owners, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits.</p>
            </section>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.legal-nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
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
