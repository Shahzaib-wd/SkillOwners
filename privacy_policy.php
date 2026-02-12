<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<div style="padding: 6rem 0 4rem; min-height: 100vh;">
    <div class="container">
        <h1 class="mb-4">Privacy Policy</h1>
        <p class="text-muted mb-4">Last updated: <?php echo date('F d, Y'); ?></p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">1. Information We Collect</h2>
                <p>We collect information you provide directly: name, email, profile information, and payment details for transactions.</p>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">2. How We Use Your Information</h2>
                <ul>
                    <li>To provide and maintain our services</li>
                    <li>To process transactions and send notifications</li>
                    <li>To communicate with you about our services</li>
                    <li>To improve our platform and user experience</li>
                </ul>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">3. Data Security</h2>
                <p>We implement industry-standard security measures to protect your data. Passwords are encrypted, and sensitive information is transmitted securely.</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h2 class="h4">4. Contact Us</h2>
                <p>For privacy concerns, contact us at: <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
