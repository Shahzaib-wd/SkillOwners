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
    <?php
    $faviconUrl = SITE_URL . '/assets/images/favicon.svg?v=' . time();
    $faviconType = 'image/svg+xml';
    ?>
    <link rel="icon" type="<?php echo $faviconType; ?>" href="<?php echo $faviconUrl; ?>">
    
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
                <li><a href="<?php echo SITE_URL; ?>/services" class="<?php echo str_contains($script_name, 'services') ? 'active' : ''; ?>">Services</a></li>
                <li><a href="<?php echo SITE_URL; ?>/portfolio" class="<?php echo str_contains($script_name, 'portfolio') ? 'active' : ''; ?>">Portfolio</a></li>
                <li><a href="<?php echo SITE_URL; ?>/blog" class="<?php echo str_contains($script_name, 'blog') ? 'active' : ''; ?>">Blog</a></li>
                <li><a href="<?php echo SITE_URL; ?>/about" class="<?php echo str_contains($script_name, 'about') ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact" class="<?php echo str_contains($script_name, 'contact') ? 'active' : ''; ?>">Contact</a></li>
            </ul>
            
            <div class="navbar-actions">
                <a href="<?php echo SITE_URL; ?>/request_quote" class="btn btn-primary btn-sm">Request a Quote</a>
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
    function toggleMobileMenu() {
        document.querySelector('.navbar-menu').classList.toggle('show');
    }
    </script>
</nav>

<div class="container mt-3">
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
