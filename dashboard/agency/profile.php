<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/User.php';
$userId = $_SESSION['user_id'];
$userModel = new User();
$user = $userModel->findById($userId);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Agency Profile</h1>
            <p class="text-muted">Manage your agency public profile and settings</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <form method="POST" action="<?php echo SITE_URL; ?>/dashboard/update_profile.php" enctype="multipart/form-data">
                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Agency Brand Logo</h3>
                            <div class="d-flex align-items-center gap-4 mb-3">
                                <div class="avatar-circle" style="width: 80px; height: 80px; font-size: 2rem; overflow: hidden; background: var(--secondary);">
                                    <?php if ($user['profile_image']): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $user['profile_image']; ?>" class="w-100 h-100" style="object-fit: cover;">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="profile_image" class="form-control mb-2" accept=".webp">
                                    <p class="text-muted small mb-0">Max size: <strong>500kb</strong>. Format: <strong>WebP only</strong>.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Agency Information</h3>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Agency Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">About the Agency</label>
                                <textarea name="bio" class="form-control" rows="5"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Expertise & Links</h3>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Agency Specializations (comma separated)</label>
                                <input type="text" name="skills" class="form-control" value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>" placeholder="Web Development, Marketing, SEO...">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Company Website</label>
                                <input type="url" name="portfolio_link" class="form-control" value="<?php echo htmlspecialchars($user['portfolio_link'] ?? ''); ?>" placeholder="https://agency.com">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="submit" class="btn btn-primary px-5">Update Agency Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="dashboard-card mb-4 text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; background: var(--secondary); overflow: hidden;">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $user['profile_image']; ?>" class="w-100 h-100" style="object-fit: cover;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <h4 class="h6 font-weight-700 mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <span class="user-role badge-agency mb-3 d-inline-block">Agency</span>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $userId; ?>" class="btn btn-outline btn-sm btn-block" target="_blank">
                        View Agency Page
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
