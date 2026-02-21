<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Check for new notifications
$new_quotes_count = 0;
$new_contacts_count = 0;

try {
    $db = getDBConnection();
    $new_quotes_count = $db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'New'")->fetchColumn();
    $new_contacts_count = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'new'")->fetchColumn();
} catch (Exception $e) {
    // Fail silently
}
?>
<style>
    :root {
        --dashboard-sidebar-width: 260px;
        --sidebar-bg: #050505;
        --sidebar-accent: #10b981;
    }

    /* Sidebar Layout Logic */
    .sidebar { 
        width: var(--dashboard-sidebar-width); 
        height: calc(100vh - 64px); 
        position: fixed; 
        left: 0;
        top: 64px;
        background: var(--sidebar-bg); 
        color: white; 
        padding: 1.5rem 1rem; 
        z-index: 1040;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-right: 1px solid hsla(0, 0%, 100%, 0.05);
    }

    .nav-link {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #94a3b8;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.35rem;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .nav-link:hover, .nav-link.active {
        background: hsla(0, 0%, 100%, 0.05);
        color: white;
    }

    .nav-link.active {
        border-left: 3px solid var(--sidebar-accent);
        background: hsla(150, 100%, 35%, 0.1);
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
        overflow-x: hidden;
        color: white;
    }

    /* Dashboard UI Components Override for dark dashboard */
    .glass-card {
        background: hsla(0, 0%, 100%, 0.03);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 1px solid hsla(0, 0%, 100%, 0.05);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        border-color: hsla(150, 100%, 35%, 0.2);
    }

    /* Dark Mode Utility Overrides */
    .content .text-muted {
        color: #94a3b8 !important;
    }

    .content .bg-light {
        background-color: hsla(0, 0%, 100%, 0.05) !important;
        color: white !important;
    }

    .content .border {
        border-color: hsla(0, 0%, 100%, 0.05) !important;
    }

    .content a.text-dark {
        color: white !important;
    }

    .content .hover-bg-light:hover {
        background-color: hsla(0, 0%, 100%, 0.08) !important;
        transform: translateX(5px);
    }

    /* Table Fixes */
    .content .table {
        color: white;
    }
    
    .content .table thead th {
        background: hsla(0, 0%, 100%, 0.02);
        border-bottom-color: hsla(0, 0%, 100%, 0.05);
    }

    .content .table tbody td {
        border-bottom-color: hsla(0, 0%, 100%, 0.03);
    }

    .content .table-hover tbody tr:hover {
        background-color: hsla(0,0%,100%,0.02);
        color: white;
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

    /* Modal Styling for Admin */
    .modal-content {
        background: #0a0a0b;
        color: white;
        border: 1px solid hsla(0, 0%, 100%, 0.1);
    }

    .modal-header.bg-light {
        background: hsla(0, 0%, 100%, 0.05) !important;
        border-bottom: 1px solid hsla(0, 0%, 100%, 0.05);
    }

    .modal .text-muted {
        color: #94a3b8 !important;
    }

    .modal .border-end {
        border-color: hsla(0, 0%, 100%, 0.05) !important;
    }

    /* Button and Badge fixes - Making them pop with a solid primary background */
    .btn-light {
        background: var(--sidebar-accent) !important;
        color: #000000 !important;
        border: none !important;
        padding: 0.5rem 1.25rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .btn-light i {
        color: #000000 !important;
    }

    .btn-light:hover {
        background: #059669 !important; /* Slightly darker emerald */
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
    }

    .btn-light:hover i {
        color: #ffffff !important;
    }

    .badge.bg-success.bg-opacity-10 {
        background-color: hsla(150, 100%, 35%, 0.15) !important;
        color: #10b981 !important;
    }

    .badge.bg-primary.bg-opacity-10 {
        background-color: hsla(210, 100%, 50%, 0.1) !important;
        color: #3b82f6 !important;
    }

    .badge.bg-danger.bg-opacity-10 {
        background-color: hsla(0, 100%, 50%, 0.1) !important;
        color: #ef4444 !important;
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
