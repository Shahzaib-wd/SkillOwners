<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole());
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
            <h1 class="page-title">Profile Settings</h1>
            <p class="text-muted">Manage your public professional identity</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <form method="POST" action="<?php echo SITE_URL; ?>/dashboard/update_profile.php" enctype="multipart/form-data">
                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Profile Picture</h3>
                            <div class="d-flex align-items-center gap-4 mb-3">
                                <div class="avatar-circle" style="width: 80px; height: 80px; font-size: 2rem; overflow: hidden;">
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
                            <h3 class="h6 font-weight-700 mb-3">Basic Information</h3>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Professional Title</label>
                                <input type="text" name="professional_title" class="form-control" value="<?php echo htmlspecialchars($user['professional_title'] ?? ''); ?>" placeholder="e.g. Web Developer, Graphic Designer">
                                <small class="text-muted">This will appear on your public profile as your primary headline.</small>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Professional Bio</label>
                                <textarea name="bio" class="form-control" rows="5" placeholder="Tell clients about your expertise..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Expertise & Contact</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">Skills (comma separated)</label>
                                        <input type="text" name="skills" class="form-control" value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>" placeholder="PHP, JavaScript, Graphic Design...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">Experience (Years)</label>
                                        <input type="number" name="experience_years" class="form-control" value="<?php echo htmlspecialchars($user['experience_years'] ?? '0'); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">Location</label>
                                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="City, Country">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1 234 567 890">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Portfolio Website</label>
                                <input type="url" name="portfolio_link" class="form-control" value="<?php echo htmlspecialchars($user['portfolio_link'] ?? ''); ?>" placeholder="https://yourportfolio.com">
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Social Presence</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">LinkedIn URL</label>
                                        <input type="url" name="linkedin_url" class="form-control" value="<?php echo htmlspecialchars($user['linkedin_url'] ?? ''); ?>" placeholder="https://linkedin.com/in/...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">Twitter URL</label>
                                        <input type="url" name="twitter_url" class="form-control" value="<?php echo htmlspecialchars($user['twitter_url'] ?? ''); ?>" placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-600 mb-2">GitHub URL</label>
                                        <input type="url" name="github_url" class="form-control" value="<?php echo htmlspecialchars($user['github_url'] ?? ''); ?>" placeholder="https://github.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Languages</h3>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Languages Spoken (comma separated)</label>
                                <input type="text" name="languages" class="form-control" value="<?php echo htmlspecialchars($user['languages'] ?? ''); ?>" placeholder="English, Spanish, French...">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="dashboard-card mb-4 text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; overflow: hidden;">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $user['profile_image']; ?>" class="w-100 h-100" style="object-fit: cover;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <h4 class="h6 font-weight-700 mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="<?php echo SITE_URL; ?>/profile?id=<?php echo $userId; ?>" class="btn btn-outline btn-sm btn-block" target="_blank">
                        Preview Public Profile
                    </a>
                </div>
                
                <div class="dashboard-card" style="border-top: 4px solid var(--primary);">
                    <h4 class="h6 font-weight-700 mb-2">Need Help?</h4>
                    <p class="text-muted small mb-0">Updating your profile information helps you appear in relevant search results for buyers.</p>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
