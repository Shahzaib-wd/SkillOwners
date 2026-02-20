<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Check for new notifications
$new_quotes_count = 0;
$new_contacts_count = 0;

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $res_quotes = $conn->query("SELECT COUNT(*) as count FROM quote_requests WHERE status = 'New'");
        if ($res_quotes) $new_quotes_count = $res_quotes->fetch_assoc()['count'];
        
        $res_contacts = $conn->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'");
        if ($res_contacts) $new_contacts_count = $res_contacts->fetch_assoc()['count'];
        
        $conn->close();
    }
} catch (Exception $e) {
    // Fail silently
}
?>
<style>
    :root {
        --dashboard-sidebar-width: 260px;
        --sidebar-bg: #111827;
        --sidebar-accent: #10b981;
    }

    /* Sidebar Layout Logic */
    .sidebar { 
        width: var(--dashboard-sidebar-width); 
        height: calc(100vh - 64px); 
        position: fixed; 
        left: 0;
        top: 64px; /* Align with global navbar height */
        background: var(--sidebar-bg); 
        color: white; 
        padding: 1.5rem 1rem; 
        z-index: 1040; /* Below navbar (1050) but above content */
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-right: 1px solid rgba(255,255,255,0.05);
    }

    .nav-link {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #9ca3af;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.35rem;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .nav-link:hover, .nav-link.active {
        background: rgba(255,255,255,0.05);
        color: white;
    }

    .nav-link.active {
        border-left: 3px solid var(--sidebar-accent);
        background: rgba(16, 185, 129, 0.1);
        color: var(--sidebar-accent);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        background-color: var(--sidebar-accent);
        border-radius: 50%;
        display: inline-block;
        margin-left: auto;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2), 0 0 8px var(--sidebar-accent);
        animation: pulse-dot-sidebar 2s infinite;
    }

    @keyframes pulse-dot-sidebar {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Mobile Sidebar Toggle Button (Sticky/Floating) */
    .sidebar-toggle-trigger {
        display: none;
        position: fixed;
        bottom: 2rem;
        right: 1.5rem;
        z-index: 1200;
        background: var(--sidebar-accent);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        transition: transform 0.3s, background 0.3s;
    }

    .sidebar-toggle-trigger:hover {
        transform: scale(1.1);
        background: #059669;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 1045;
    }

    /* Desktop View Correction */
    .content {
        margin-left: var(--dashboard-sidebar-width);
        padding: 2.5rem;
        transition: 0.3s;
        min-height: calc(100vh - 64px);
        overflow-x: hidden; /* Prevent horizontal scroll */
    }

    /* Dashboard UI Components */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        border: 1px solid rgba(255,255,255,0.2);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .stat-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .icon-box {
        transition: transform 0.2s;
    }

    .hover-bg-light:hover {
        background-color: #f9fafb !important;
        transform: translateX(5px);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
            top: 0;
            height: 100vh;
            z-index: 2000; /* Above everything */
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar-toggle-trigger {
            display: flex;
        }
        .sidebar-overlay {
            z-index: 1055;
        }
        .sidebar-overlay.active {
            display: block;
        }
        .content {
            margin-left: 0 !important;
            padding: 1.25rem;
            width: 100%;
        }
        
        .stat-card {
            padding: 1.25rem !important;
        }
    }

    @media (max-width: 575.98px) {
        .content {
            padding: 1rem;
        }
        .stat-card h2 {
            font-size: 1.5rem;
        }
        .h3 {
            font-size: 1.25rem;
        }
    }
</style>

<!-- Sidebar Trigger (Mobile Only) -->
<button class="sidebar-toggle-trigger" id="sidebarToggle">
    <i class="fas fa-th-large"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebarMenu">
    <div class="px-3 mb-4 d-flex align-items-center gap-2">
        <div style="background: var(--sidebar-accent); color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">SO</div>
        <span class="fw-bold small text-white-50" style="letter-spacing: 1px;">ADMIN PANEL</span>
    </div>
    
    <nav>
        <a href="<?php echo SITE_URL; ?>/dashboard/alpha/index.php" class="nav-link <?php echo (str_contains($current_page, 'index')) ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Overview</a>
        <a href="<?php echo SITE_URL; ?>/dashboard/alpha/quotes.php" class="nav-link <?php echo (str_contains($current_page, 'quotes')) ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice-dollar"></i> Quotes
            <?php if ($new_quotes_count > 0): ?><span class="notification-dot"></span><?php endif; ?>
        </a>
        <a href="<?php echo SITE_URL; ?>/dashboard/alpha/contacts.php" class="nav-link <?php echo (str_contains($current_page, 'contacts')) ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Contacts
            <?php if ($new_contacts_count > 0): ?><span class="notification-dot"></span><?php endif; ?>
        </a>
        <div class="px-3 mt-4 mb-2 small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Content</div>
        <a href="<?php echo SITE_URL; ?>/dashboard/alpha/services.php" class="nav-link <?php echo (str_contains($current_page, 'services')) ? 'active' : ''; ?>"><i class="fas fa-concierge-bell"></i> Services</a>
        <a href="<?php echo SITE_URL; ?>/dashboard/alpha/portfolio.php" class="nav-link <?php echo (str_contains($current_page, 'portfolio')) ? 'active' : ''; ?>"><i class="fas fa-project-diagram"></i> Portfolio</a>
        <a href="<?php echo SITE_URL; ?>/dashboard/alpha/blog.php" class="nav-link <?php echo (str_contains($current_page, 'blog')) ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i> Blog Posts</a>
        
        <div class="mt-5 pt-3 border-top border-secondary">
            <a href="<?php echo SITE_URL; ?>/logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
        </div>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleIcon = toggle.querySelector('i');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        if (sidebar.classList.contains('active')) {
            toggleIcon.classList.remove('fa-th-large');
            toggleIcon.classList.add('fa-times');
        } else {
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-th-large');
        }
    }

    if (toggle) toggle.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
});
</script>
