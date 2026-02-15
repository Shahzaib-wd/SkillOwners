<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>Skill Owners - Find Expert Freelancers & Agencies Instantly</title>
    <meta name="description" content="Connect with top freelancers and agencies. Get projects done faster with verified professionals. Browse thousands of services in design, development, marketing, and more.">
    <meta name="keywords" content="freelancers, agencies, hire freelancers, gig marketplace, web development, design services, digital marketing">
    <meta name="author" content="Skill Owners">
    
    <!-- Open Graph -->
    <meta property="og:title" content="Skill Owners - Find Expert Talent Instantly">
    <meta property="og:description" content="Connect with top freelancers and agencies worldwide. Browse verified professionals and get your projects done.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.svg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Skill Owners - Find Expert Talent Instantly">
    <meta name="twitter:description" content="Connect with top freelancers and agencies worldwide.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo SITE_URL; ?>/assets/images/favicon.svg">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo SITE_URL; ?>" class="navbar-brand" style="text-decoration: none; display: flex; align-items: center; gap: 0.625rem; font-weight: 700; font-size: 1.25rem;">
                <div class="logo" style="background: #10b981; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700; flex-shrink: 0;">SO</div>
                <span class="d-flex align-items-center">Skill<span style="color: #10b981;">Owners</span></span>
            </a>
            
            <?php
            $current_uri = $_SERVER['REQUEST_URI'];
            $script_name = $_SERVER['SCRIPT_NAME'];
            $is_browse = str_contains($script_name, 'browse');
            $type_param = $_GET['type'] ?? '';
            ?>
            <ul class="navbar-menu">
                <li><a href="<?php echo SITE_URL; ?>/browse" class="<?php echo ($is_browse && empty($type_param)) ? 'active' : ''; ?>">Browse Services</a></li>
                <li><a href="<?php echo SITE_URL; ?>/browse?type=freelancer" class="<?php echo ($is_browse && $type_param === 'freelancer') ? 'active' : ''; ?>">Find Freelancers</a></li>
                <li><a href="<?php echo SITE_URL; ?>/browse?type=agency" class="<?php echo ($is_browse && $type_param === 'agency') ? 'active' : ''; ?>">Find Agencies</a></li>
                <li><a href="<?php echo SITE_URL; ?>/about" class="<?php echo str_contains($script_name, 'about') ? 'active' : ''; ?>">How It Works</a></li>
            </ul>
            
            <div class="navbar-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/dashboard/<?php echo getUserRole(); ?>" class="btn btn-ghost btn-sm">Dashboard</a>
                    <a href="javascript:void(0);" onclick="scrollToMessages()" class="navbar-messages-icon" id="navbarMessagesIcon" title="Messages">
                        <i class="fas fa-envelope"></i>
                        <span class="navbar-unread-dot" id="navbarUnreadDot" style="display: none;"></span>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/logout" class="btn btn-outline btn-sm">Logout</a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login" class="btn btn-ghost btn-sm">Log In</a>
                    <a href="<?php echo SITE_URL; ?>/register" class="btn btn-primary btn-sm">Get Started</a>
                <?php endif; ?>
            </div>
            
            <button class="mobile-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    
    <style>
    .navbar-messages-icon {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: var(--foreground);
        transition: all 0.2s;
        text-decoration: none;
    }
    .navbar-messages-icon:hover {
        background: var(--muted);
        color: var(--primary);
    }
    .navbar-messages-icon i {
        font-size: 1.1rem;
    }
    .navbar-unread-dot {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 10px;
        height: 10px;
        background: #ef4444;
        border: 2px solid var(--background);
        border-radius: 50%;
        animation: pulse-navbar-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-navbar-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.2); }
    }
    /* Navbar buttons refinement (Dashboard, Logout, Log In) */
    .navbar .btn-ghost,
    .navbar .btn-outline {
        transition: all 0.2s ease;
    }
    .navbar .btn-ghost:hover,
    .navbar .btn-outline:hover {
        transform: none !important;
        background: var(--muted);
        border-color: var(--border);
        color: var(--primary);
    }
    </style>
    
    <script>
    function scrollToMessages() {
        // Always navigate to dedicated inbox page
        window.location.href = '<?php echo SITE_URL; ?>/inbox';
    }
    
    function toggleMobileMenu() {
        document.querySelector('.navbar-menu').classList.toggle('show');
        // Removed navbar-actions toggle since we use mobile-only links now
    }
    
    <?php if (isLoggedIn()): ?>
    // Poll for unread messages count (navbar indicator)
    let navbarPreviousUnreadCount = 0;
    
    function updateNavbarUnreadIndicator() {
        if (typeof ajaxRequest === 'function') {
            ajaxRequest('<?php echo SITE_URL; ?>/chat_api?action=get_unread_count&_=' + Date.now())
                .then(data => {
                    if (data.success) {
                        const count = parseInt(data.unread_count) || 0;
                        const dot = document.getElementById('navbarUnreadDot');
                        
                        if (dot) {
                            if (count > 0) {
                                dot.style.display = 'block';
                                
                                // Play sound if count increased (new message)
                                if (count > navbarPreviousUnreadCount && navbarPreviousUnreadCount >= 0) {
                                    playMessageNotificationSound();
                                }
                            } else {
                                dot.style.display = 'none';
                            }
                        }
                        
                        navbarPreviousUnreadCount = count;
                    }
                })
                .catch(error => {
                    console.error('Error updating navbar unread indicator:', error);
                });
        }
    }
    
    // Sound notification
    function playMessageNotificationSound() {
        try {
            // Create a pleasant notification sound using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            // Pleasant notification tone (two quick beeps)
            oscillator.frequency.value = 800; // Higher pitch
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
            
            // Second beep
            setTimeout(() => {
                const oscillator2 = audioContext.createOscillator();
                const gainNode2 = audioContext.createGain();
                
                oscillator2.connect(gainNode2);
                gainNode2.connect(audioContext.destination);
                
                oscillator2.frequency.value = 1000;
                gainNode2.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                
                oscillator2.start(audioContext.currentTime);
                oscillator2.stop(audioContext.currentTime + 0.1);
            }, 150);
            
        } catch (error) {
            console.log('Audio notification not available:', error);
        }
    }
    
    // Initial check and polling
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            updateNavbarUnreadIndicator();
            setInterval(updateNavbarUnreadIndicator, 10000); // Check every 10 seconds
            
            // Scroll to messages if anchor present
            if (window.location.hash === '#messages') {
                const messagesSection = document.getElementById('messages');
                if (messagesSection) {
                    setTimeout(() => {
                        messagesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                }
            }
        });
    } else {
        updateNavbarUnreadIndicator();
        setInterval(updateNavbarUnreadIndicator, 10000);
        
        // Scroll to messages if anchor present
        if (window.location.hash === '#messages') {
            const messagesSection = document.getElementById('messages');
            if (messagesSection) {
                setTimeout(() => {
                    messagesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        }
    }
    <?php endif; ?>
    </script>
</nav>

<div class="container" style="margin-top: 2rem;">
    <?php if ($error = getError()): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-lg py-3 px-4 mb-0" role="alert" style="background: #fef2f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-circle text-danger"></i>
                <div class="font-weight-600"><?php echo $error; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($success = getSuccess()): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-lg py-3 px-4 mb-0" role="alert" style="background: #f0fdf4; border-left: 4px solid #10b981 !important; color: #065f46;">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-check-circle text-primary"></i>
                <div class="font-weight-600"><?php echo $success; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>
