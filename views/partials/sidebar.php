<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = getUserRole();
$userId = $_SESSION['user_id'];
?>

<div class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-info">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <span class="user-role badge-<?php echo $role; ?>"><?php echo ucfirst($role); ?></span>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="section-title">Main Navigation</span>
            <a href="<?php echo SITE_URL; ?>/dashboard/<?php echo $role; ?>/index.php" class="nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Overview
            </a>
            
            <?php if ($role === 'freelancer'): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/gigs.php" class="nav-item <?php echo $current_page === 'gigs.php' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> My Gigs
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/projects.php" class="nav-item <?php echo $current_page === 'projects.php' ? 'active' : ''; ?>">
                    <i class="fas fa-folder-open"></i> Portfolio
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/agencies.php" class="nav-item <?php echo $current_page === 'agencies.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> My Agencies
                </a>
            <?php endif; ?>

            <?php if ($role === 'agency'): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency/team.php" class="nav-item <?php echo $current_page === 'team.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Team Management
                </a>
                <a href="<?php echo SITE_URL; ?>/dashboard/agency/invitations.php" class="nav-item <?php echo $current_page === 'invitations.php' ? 'active' : ''; ?>">
                    <i class="fas fa-paper-plane"></i> Invitations
                </a>
            <?php endif; ?>

            <a href="<?php echo SITE_URL; ?>/dashboard/<?php echo $role; ?>/orders.php" class="nav-item <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            
            <a href="<?php echo SITE_URL; ?>/inbox.php" class="nav-item">
                <i class="fas fa-envelope"></i> Messages
            </a>
        </div>

        <div class="nav-section">
            <span class="section-title">Account</span>
            <a href="<?php echo SITE_URL; ?>/dashboard/<?php echo $role; ?>/profile.php" class="nav-item <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i> Profile Settings
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="nav-item text-danger">
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
