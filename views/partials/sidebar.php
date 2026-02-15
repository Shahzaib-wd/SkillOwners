<?php
$current_page = basename($_SERVER['PHP_SELF'], ".php");
$role = getUserRole();

// Detect if we're in the admin dashboard (alpha directory)
$isAlphaDashboard = strpos($_SERVER['PHP_SELF'], '/dashboard/alpha/') !== false;
if ($isAlphaDashboard) {
    $role = 'admin';
}

$userId = $_SESSION['user_id'] ?? 0;


$pendingAppsCount = 0;
if ($role === 'agency') {
    require_once __DIR__ . '/../../models/AgencyInvitation.php';
    $sidebarInvModel = new AgencyInvitation();
    $sidebarInvs = $sidebarInvModel->getAgencyInvitations($userId);
    $pendingAppsCount = count(array_filter($sidebarInvs, function($inv) use ($userId) {
        return $inv['invited_by'] != $userId && $inv['status'] === 'pending';
    }));
}
?>

<div class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-info">
            <?php if (!empty($_SESSION['user_image'])): ?>
                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($_SESSION['user_image']); ?>" 
                     alt="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" 
                     class="avatar-image">
            <?php else: ?>
                <div class="avatar-circle">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <span class="user-role badge-<?php echo $role; ?>"><?php echo ucfirst($role); ?></span>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="section-title">Main Navigation</span>
            <?php 
            $dashboardLink = ($role === 'admin') ? SITE_URL . '/dashboard/alpha/index' : SITE_URL . '/dashboard/' . $role . '/index';
            ?>
            <a href="<?php echo $dashboardLink; ?>" class="nav-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Overview
            </a>
            
            <?php if ($role === 'freelancer'): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/gigs" class="nav-item <?php echo $current_page === 'gigs' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> My Gigs
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/projects" class="nav-item <?php echo $current_page === 'projects' ? 'active' : ''; ?>">
                    <i class="fas fa-folder-open"></i> Portfolio
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/agencies" class="nav-item <?php echo $current_page === 'agencies' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> My Agencies
                </a>
            <?php endif; ?>

            <?php if ($role === 'agency'): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency/services" class="nav-item <?php echo $current_page === 'services' ? 'active' : ''; ?>">
                    <i class="fas fa-concierge-bell"></i> Services
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency/team" class="nav-item <?php echo $current_page === 'team' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Team Management
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency/invitations" class="nav-item <?php echo $current_page === 'invitations' ? 'active' : ''; ?>">
                    <i class="fas fa-paper-plane"></i> Invitations
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency/applications" class="nav-item <?php echo $current_page === 'applications' ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature"></i> Applications
                    <?php if ($pendingAppsCount > 0): ?>
                        <span class="notification-dot"></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/alpha/users" class="nav-item <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog"></i> User Management
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/alpha/gigs" class="nav-item <?php echo $current_page === 'gigs' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Gig Moderation
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/alpha/reports" class="nav-item <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                    <i class="fas fa-flag"></i> Abuse Reports
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/alpha/messages" class="nav-item <?php echo $current_page === 'message' || $current_page === 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope-open-text"></i> Contact Messages
                </a>
<?php endif; ?>

            <?php if ($role !== 'admin'): ?>
                <?php 
                $ordersLink = SITE_URL . '/dashboard/' . $role . '/orders';
                ?>
                <a href="<?php echo $ordersLink; ?>" class="nav-item <?php echo $current_page === 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
                
                <a href="<?php echo SITE_URL; ?>/inbox" class="nav-item">
                    <i class="fas fa-envelope"></i> Messages
                </a>
            <?php endif; ?>
        </div>

        <div class="nav-section">
            <span class="section-title">Account</span>
            <?php 
            $profileLink = ($role === 'admin') ? SITE_URL . '/dashboard/alpha/profile' : SITE_URL . '/dashboard/' . $role . '/profile';
            ?>
            <a href="<?php echo $profileLink; ?>" class="nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i> Profile Settings
            </a>
            <a href="<?php echo SITE_URL; ?>/logout" class="nav-item text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>
</div>

<style>
.dashboard-layout {
    display: flex;
    min-height: calc(100vh - 64px);
    margin-top: 64px;
    background: #f8fafc;
}

.dashboard-sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 64px;
    height: calc(100vh - 64px);
    z-index: 100;
    transition: all 0.3s ease;
}

.sidebar-header {
    padding: 24px;
    border-bottom: 1px solid #f1f5f9;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar-circle {
    width: 48px;
    height: 48px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.avatar-image {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.avatar-image:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #1e293b;
}

.user-role {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 2px 8px;
    border-radius: 4px;
    width: fit-content;
    margin-top: 2px;
}

.badge-freelancer { background: #e0e7ff; color: #4338ca; }
.badge-agency { background: #fae8ff; color: #a21caf; }
.badge-buyer { background: #dcfce7; color: #15803d; }
.badge-admin { background: #fee2e2; color: #dc2626; }

.sidebar-nav {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
}

.nav-section {
    margin-bottom: 32px;
}

.section-title {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #94a3b8;
    margin-bottom: 12px;
    padding-left: 12px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: var(--radius);
    color: #64748b;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-bottom: 4px;
}

.nav-item i {
    font-size: 1.1rem;
    width: 20px;
}

.nav-item:hover {
    background: #f1f5f9;
    color: var(--primary);
    text-decoration: none;
}

.nav-item.active {
    background: #eef2ff;
    color: var(--primary);
}

.notification-dot {
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    margin-left: auto;
    box-shadow: 0 0 0 2px white;
    animation: pulse-red 2s infinite;
}

@keyframes pulse-red {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.dashboard-content {
    flex: 1;
    padding: 40px;
    overflow-x: hidden;
}

@media (max-width: 991px) {
    .dashboard-sidebar {
        position: fixed;
        left: -280px;
    }
    .dashboard-sidebar.active {
        left: 0;
    }
}
</style>
