<?php
require_once 'config.php';
require_once 'models/User.php';
require_once 'models/Gig.php';

$userId = $_GET['id'] ?? null;

if (!$userId) {
    redirect('/index');
}

$userModel = new User();
$gigModel = new Gig();

$user = $userModel->findById($userId);

if (!$user || ($user['role'] !== 'freelancer' && $user['role'] !== 'agency')) {
    include 'views/partials/header.php';
    echo '<div class="container py-5 text-center">
            <div class="dashboard-card py-5">
                <i class="fas fa-user-slash fa-4x text-muted mb-4"></i>
                <h1 class="h3 font-weight-700">User Not Found</h1>
                <p class="text-muted">The profile you are looking for does not exist or is not a professional profile.</p>
                <a href="' . SITE_URL . '/browse" class="btn btn-primary mt-3">Browse Services</a>
            </div>
          </div>';
    include 'views/partials/footer.php';
    exit;
}

$gigs = $gigModel->findByUserId($userId);

// For agency profiles, also get contributed gigs from team members
$agencyGigs = [];
if ($user['role'] === 'agency') {
    $agencyGigs = $gigModel->getAgencyGigs($userId, 'approved');
}

$allGigs = $user['role'] === 'agency' ? $agencyGigs : $gigs;
$gigCount = count($allGigs);

include 'views/partials/header.php';
?>

<div class="profile-hero">
    <div class="profile-banner"></div>
    <div class="container">
        <div class="profile-header-content">
            <div class="row align-items-end">
                <div class="col-md-auto">
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar-main">
                            <?php if ($user['profile_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $user['profile_image']; ?>" alt="<?php echo htmlspecialchars($user['full_name']); ?>">
                            <?php else: ?>
                                <div class="profile-avatar-fallback">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="verified-badge" title="Verified Professional">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                <?php 
                $headline = !empty($user['professional_title']) ? $user['professional_title'] : ucfirst($user['role']);
                ?>
                <div class="col-md profile-info-main">
                    <h1 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="text-primary fw-700 h5 mb-0"><?php echo htmlspecialchars($headline); ?></span>
                        <span class="badge <?php echo ($user['role'] ?? '') === 'agency' ? 'bg-indigo' : 'bg-success'; ?> text-white text-uppercase" style="font-size: 0.6rem; padding: 0.3rem 0.6rem;">
                            <?php echo ucfirst($user['role'] ?? 'user'); ?>
                        </span>
                    </div>
                    <div class="profile-meta">
                        <span><i class="fas fa-star me-1"></i> 5.0 (New)</span>
                        <span><i class="fas fa-map-marker-alt me-1"></i> Verified Location</span>
                        <span><i class="fas fa-calendar-alt me-1"></i> Joining <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
                <div class="col-md-auto pb-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary px-4" onclick="alert('Messaging system coming soon!')">
                            <i class="fas fa-paper-plane me-2"></i> Contact Me
                        </button>
                        <button class="btn btn-outline" onclick="navigator.clipboard.writeText(window.location.href); alert('Profile link copied!')">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="pro-card mb-4">
                <h3 class="pro-card-title"><i class="fas fa-info-circle"></i> About</h3>
                <p class="text-muted mb-0" style="line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($user['bio'] ?: 'A professional dedicated to delivering excellence. Focused on quality, efficiency, and clear communication.')); ?>
                </p>
            </div>

            <div class="pro-card mb-4">
                <h3 class="pro-card-title"><i class="fas fa-bolt"></i> Expertise</h3>
                <div class="expertise-tags">
                    <?php 
                    if (!empty($user['skills'])) {
                        $skills = explode(',', $user['skills']);
                        foreach ($skills as $skill) {
                            echo '<span class="expertise-tag">' . trim(htmlspecialchars($skill)) . '</span>';
                        }
                    } else {
                        echo '<p class="text-muted small italic">No specific skills listed yet.</p>';
                    }
                    ?>
                </div>
            </div>

            <?php if ($user['portfolio_link']): ?>
            <div class="pro-card mb-4">
                <h3 class="pro-card-title"><i class="fas fa-briefcase"></i> Portfolio</h3>
                <a href="<?php echo htmlspecialchars($user['portfolio_link']); ?>" target="_blank" class="btn btn-outline w-100 justify-content-between">
                    <span>View Projects</span>
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            <?php endif; ?>

            <div class="pro-card">
                <h3 class="pro-card-title"><i class="fas fa-shield-alt"></i> Verification</h3>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-center gap-2 mb-2 text-muted">
                        <i class="fas fa-check-circle text-success"></i> Identity Verified
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2 text-muted">
                        <i class="fas fa-check-circle text-success"></i> Payment Verified
                    </li>
                    <li class="d-flex align-items-center gap-2 text-muted">
                        <i class="fas fa-check-circle text-success"></i> Email Verified
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content (Services) -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 font-weight-800 mb-0"><?php echo $user['role'] === 'agency' ? 'Agency' : 'Professional'; ?> Services</h2>
                <span class="text-muted fw-500"><?php echo $gigCount; ?> results found</span>
            </div>

            <?php if (empty($allGigs)): ?>
                <div class="pro-card text-center py-5">
                    <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No active services listed at the moment.</p>
                </div>
            <?php else: ?>
                <div class="service-grid">
                    <?php foreach ($allGigs as $gig): ?>
                        <div class="pro-service-card">
                            <a href="<?php echo SITE_URL; ?>/gig?id=<?php echo $gig['id']; ?>" class="text-decoration-none">
                                <div class="gig-image-wrapper">
                                    <img src="<?php echo $gig['image'] ? SITE_URL . '/uploads/' . $gig['image'] : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80'; ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>" class="gig-image">
                                </div>
                                <div class="gig-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-light text-primary border rounded-pill" style="font-size: 0.65rem;"><?php echo htmlspecialchars($gig['category']); ?></span>
                                    </div>
                                    <h3 class="gig-title"><?php echo htmlspecialchars($gig['title']); ?></h3>
                                    <?php if ($user['role'] === 'agency' && !empty($gig['full_name'])): ?>
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="text-muted smaller"><i class="fas fa-user-tie"></i> Member: <?php echo htmlspecialchars($gig['full_name']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="gig-footer">
                                        <div>
                                            <i class="fas fa-star text-warning small"></i>
                                            <span class="small fw-600">New</span>
                                        </div>
                                        <div>
                                            <span class="text-muted smaller d-block text-end">Starting at</span>
                                            <span class="gig-price">$<?php echo number_format($gig['price'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Local style overrides for this page if needed */
.bg-indigo { background-color: #6366f1; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
.fw-500 { font-weight: 500; }
.smaller { font-size: 0.75rem; }
</style>

<?php include 'views/partials/footer.php'; ?>
