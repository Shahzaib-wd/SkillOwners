<?php
require_once '../../config.php';
requireLogin();

require_once '../../models/User.php';
$userId = $_SESSION['user_id'];
$userModel = new User();
$user = $userModel->findById($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - SkillOwners Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-bg: #111827;
            --secondary-bg: #1f2937;
            --accent: #10b981;
        }
        body { background: #f3f4f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .nav-link { color: #9ca3af; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 1rem; text-decoration: none; transition: all 0.3s; }
        .nav-link:hover, .nav-link.active { background: var(--secondary-bg); color: white; }
        .nav-link.active { border-left: 4px solid var(--accent); }
        .content { margin-left: var(--sidebar-width); padding: 2rem; width: calc(100% - var(--sidebar-width)); }
        .glass-card { background: white; border: none; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include 'alpha_sidebar.php'; ?>
    
    <main class="content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="h3 fw-bold mb-0">Admin Profile</h1>
        </header>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card p-4 shadow-sm">
                    <form method="POST" action="<?php echo SITE_URL; ?>/dashboard/update_profile.php">
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Account Details</h5>
                            <div class="mb-3">
                                <label class="form-label font-weight-500 mb-2">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label font-weight-500 mb-2">Email Address</label>
                                <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <small class="text-muted">Managed via system administration.</small>
                            </div>
                        </div>
                        
                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="glass-card mb-4 text-center p-4 shadow-sm">
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
