<?php
require_once '../../config.php';
requireLogin();

// if (getUserRole() !== 'admin') {
//     redirect('/dashboard/' . getUserRole() . '.php');
// }

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
            <h1 class="page-title">Admin Profile</h1>
            <p class="text-muted">Manage your administrative account settings</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <form method="POST" action="<?php echo SITE_URL; ?>/dashboard/update_profile.php">
                        <div class="mb-4">
                            <h3 class="h6 font-weight-700 mb-3">Account Details</h3>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-600 mb-2">Email Address</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <small class="text-muted">Email address cannot be changed from the dashboard.</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="submit" class="btn btn-primary px-5">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="dashboard-card mb-4 text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; background: var(--primary);">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h4 class="h6 font-weight-700 mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <span class="user-role badge-agency mb-3 d-inline-block">System Administrator</span>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
