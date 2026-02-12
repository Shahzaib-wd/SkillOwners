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
                    <p>The marketplace connecting skilled professionals with businesses worldwide.</p>
                </div>
                
                <div class="footer-links">
                    <h4>For Buyers</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/browse.php">Browse Services</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php">How It Works</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>For Sellers</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/register.php?role=freelancer">Start Selling</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/register.php?role=agency">Create Agency</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/about.php">About</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/privacy_policy.php">Privacy</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/terms_conditions.php">Terms</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                © <?php echo date('Y'); ?> Skill Owners. All rights reserved.
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js?v=1.1"></script>
</body>
</html>
