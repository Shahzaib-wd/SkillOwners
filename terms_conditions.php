<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<div style="padding: 6rem 0 4rem; min-height: 100vh;">
    <div class="container">
        <h1 class="mb-4">Terms & Conditions</h1>
        <p class="text-muted mb-4">Last updated: <?php echo date('F d, Y'); ?></p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">1. Acceptance of Terms</h2>
                <p>By accessing Skill Owners, you agree to these Terms & Conditions and our Privacy Policy.</p>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">2. User Accounts</h2>
                <ul>
                    <li>You must create an account to use services</li>
                    <li>You're responsible for account security</li>
                    <li>Provide accurate information</li>
                    <li>One account per user</li>
                </ul>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">3. Service Limits</h2>
                <ul>
                    <li>Freelancers: Maximum 5 gigs, 3 projects, 1 portfolio link</li>
                    <li>Agencies: Maximum 5 gigs</li>
                    <li>Buyer-initiated chat only</li>
                </ul>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h4">4. Prohibited Activities</h2>
                <ul>
                    <li>Spam or fraudulent content</li>
                    <li>Violation of intellectual property</li>
                    <li>Harassment or abusive behavior</li>
                    <li>Circumventing platform fees</li>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h2 class="h4">5. Contact</h2>
                <p>Questions? Email: <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
