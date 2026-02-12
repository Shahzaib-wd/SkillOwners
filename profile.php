<?php
require_once 'config.php';
require_once 'models/User.php';
require_once 'models/Gig.php';

$userId = $_GET['id'] ?? null;

if (!$userId) {
    redirect('/index.php');
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
                <a href="' . SITE_URL . '/browse.php" class="btn btn-primary mt-3">Browse Services</a>
            </div>
          </div>';
    include 'views/partials/footer.php';
    exit;
}

$gigs = $gigModel->findByUserId($userId);
$gigCount = count($gigs);

include 'views/partials/header.php';
?>

<div class="profile-page py-5 mt-5">
    <div class="container">
        <!-- Profile Header Card -->
        <div class="dashboard-card mb-5 reveal-up" style="border-top: 5px solid var(--primary);">
            <div class="row align-items-center">
                <div class="col-md-auto text-center mb-4 mb-md-0">
                    <div class="avatar-circle mx-auto" style="width: 120px; height: 120px; font-size: 3rem; background: <?php echo $user['role'] === 'agency' ? 'var(--secondary)' : 'var(--primary)'; ?>; color: white;">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $user['profile_image']; ?>" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                        <h1 class="h2 font-weight-800 mb-0"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                        <span class="badge <?php echo $user['role'] === 'agency' ? 'badge-agency' : 'badge-primary'; ?> text-uppercase tracking-widest px-3 py-2" style="font-size: 0.75rem;">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                    <p class="text-muted mb-3"><i class="fas fa-calendar-alt me-2"></i>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                    <div class="d-flex flex-wrap gap-4 mt-1">
                        <div class="stat-item">
                            <span class="d-block h5 font-weight-700 mb-0"><?php echo $gigCount; ?></span>
                            <span class="text-muted small text-uppercase font-weight-600">Active Services</span>
                        </div>
                        <div class="stat-item">
                            <span class="d-block h5 font-weight-700 mb-0">100%</span>
                            <span class="text-muted small text-uppercase font-weight-600">Response Rate</span>
                        </div>
                        <?php if ($user['portfolio_link']): ?>
                        <div class="stat-item">
                            <a href="<?php echo htmlspecialchars($user['portfolio_link']); ?>" target="_blank" class="text-primary font-weight-700 hover-lift d-flex align-items-center gap-2">
                                <i class="fas fa-external-link-alt"></i> Portfolio
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-auto mt-4 mt-md-0">
                    <a href="javascript:void(0);" onclick="alert('Messaging system coming soon to public profiles!')" class="btn btn-primary px-5 py-3 rounded-pill font-weight-700">
                        Contact <?php echo $user['role'] === 'agency' ? 'Agency' : 'Me'; ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Info -->
            <div class="col-lg-4 mb-4">
                <div class="dashboard-card h-100 reveal-up" style="animation-delay: 0.1s;">
                    <h3 class="h5 font-weight-700 mb-4 border-bottom pb-3">About</h3>
                    <div class="mb-4">
                        <p class="text-muted" style="line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($user['bio'] ?? 'No bio provided.')); ?>
                        </p>
                    </div>

                    <h3 class="h5 font-weight-700 mb-4 border-bottom pb-3">Expertise</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <?php 
                        if (!empty($user['skills'])) {
                            $skills = explode(',', $user['skills']);
                            foreach ($skills as $skill) {
                                echo '<span class="tag">' . trim(htmlspecialchars($skill)) . '</span>';
                            }
                        } else {
                            echo '<p class="text-muted small italic">No skills listed.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Gig Listing -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 font-weight-800 mb-0"><?php echo $user['role'] === 'agency' ? 'Our' : 'My'; ?> Services</h2>
                </div>

                <div class="gig-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                    <?php if (empty($gigs)): ?>
                        <div class="dashboard-card w-100 text-center py-5">
                            <p class="text-muted mb-0">No active services listed at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($gigs as $index => $gig): ?>
                            <a href="<?php echo SITE_URL; ?>/gig.php?id=<?php echo $gig['id']; ?>" class="gig-card reveal-up" style="animation-delay: <?php echo 0.2 + ($index * 0.1); ?>s;">
                                <img src="<?php echo $gig['image'] ? SITE_URL . '/uploads/' . $gig['image'] : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80'; ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>" class="gig-image">
                                <div class="gig-content">
                                    <h3 class="gig-title mb-2"><?php echo htmlspecialchars($gig['title']); ?></h3>
                                    <div class="gig-footer pt-3 mt-auto border-top">
                                        <span class="badge badge-primary px-3"><?php echo htmlspecialchars($gig['category']); ?></span>
                                        <div class="text-end">
                                            <span class="text-muted smaller d-block mb-1">Starting at</span>
                                            <span class="gig-price h5 font-weight-800 text-primary mb-0">$<?php echo number_format($gig['price'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-page .avatar-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid white;
    box-shadow: var(--shadow-card);
    overflow: hidden;
}

.stat-item {
    padding-right: 2rem;
    border-right: 1px solid #f3f4f6;
}

.stat-item:last-child {
    border-right: none;
}

.badge-agency {
    background-color: var(--secondary);
    color: white;
}

.gig-grid .gig-card {
    background: white;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.gig-grid .gig-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-elevated);
}

.gig-grid .gig-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.gig-grid .gig-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.gig-grid .gig-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--foreground);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gig-grid .gig-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<?php include 'views/partials/footer.php'; ?>
