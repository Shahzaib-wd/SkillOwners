    <!-- Footer -->
    <footer class="footer">
        <div class="container">

            <div class="footer-main">
                <div class="footer-brand">
                    <div class="brand-logo mb-4">
                        <div class="logo" style="background: #10b981; color: white; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700;">SO</div>
                        <span class="navbar-brand m-0">
                            <span>Skill<span class="text-primary">Owners</span></span>
                        </span>
                    </div>
                    <p class="brand-desc">Senior-level technical expertise and aggressive growth strategies. We build the engines that power digital market leaders.</p>
                </div>
                
                <div class="footer-nav-group">
                    <div class="footer-links">
                        <h4>Expertise</h4>
                        <ul>
                            <li><a href="<?php echo SITE_URL; ?>/services">Web Development</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/services">SEO & Ranking</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="<?php echo SITE_URL; ?>/about">Our Story</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/contact">Contact Us</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Legal</h4>
                        <ul>
                            <li><a href="<?php echo SITE_URL; ?>/privacy_policy">Privacy Policy</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/terms_conditions">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> Skill Owners Global. All equity reserved.</p>
                <div class="footer-bottom-links">
                    <span>Precision.</span>
                    <span>Performance.</span>
                    <span>Growth.</span>
                </div>
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



</body>
</html>
