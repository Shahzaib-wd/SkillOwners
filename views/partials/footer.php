    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="brand-logo">
                        <div class="logo" style="background: #10b981; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700;">SO</div>
                        <span class="navbar-brand" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 700; font-size: 1.25rem;">
                            <span>Skill<span style="color: #10b981;">Owners</span></span>
                        </span>
                    </div>
                    <p>Transforming visions into high-performance reality with senior-level technical expertise and aggressive growth strategies.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Solutions</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/services">Our Services</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/portfolio">Portfolio</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/blog">Insights</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/contact">Contact Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/request_quote">Get a Quote</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about">Our Story</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/privacy_policy">Privacy Policy</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/terms_conditions">Terms of Service</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact">Support</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                © <?php echo date('Y'); ?> Skill Owners. All rights reserved.
            </div>
        </div>
    </footer>
    
    <!-- Chatbot Component -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/chatbot.css">
    <?php include 'chatbot.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js?v=1.1"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/chatbot.js"></script>

    <?php if (!isLoggedIn()): ?>
    <!-- Google One Tap (Global) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <div id="g_id_onload"
         data-client_id="<?php echo GOOGLE_CLIENT_ID; ?>"
         data-login_uri="<?php echo SITE_URL; ?>/auth_google.php"
         data-auto_prompt="true"
         data-itp_support="true"
         data-context="signin">
    </div>
    <?php endif; ?>
</body>
</html>
