<?php
require_once '../../config.php';

// Local Development Bypass Logic
$admin_email = ADMIN_LOGIN_EMAIL;
if ($_SERVER['HTTP_HOST'] === 'localhost' && isset($_POST['local_bypass']) && $_POST['local_bypass'] == '1') {
    $db = getDBConnection();
    try {
        $u = $db->query("SELECT * FROM users WHERE email = '$admin_email'")->fetch();
        if ($u) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['user_email'] = $u['email'];
            $_SESSION['user_name'] = $u['full_name'];
            $_SESSION['user_role'] = 'admin';
            $_SESSION['login_time'] = time();
            redirect('/alpha');
        }
    } catch (Exception $e) {
        showError("Bypass failed: " . $e->getMessage());
    }
}

// Check if logged in specifically as the designated admin
if (!isLoggedIn() || $_SESSION['user_email'] !== $admin_email) {
    if (isLoggedIn()) {
        session_destroy();
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Alpha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="bg-dark text-white d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card bg-secondary p-5 shadow-lg border-0" style="max-width: 400px; width: 100%; border-radius: 20px;">
        <div class="text-center mb-4">
            <div class="logo mx-auto mb-3" style="background: #10b981; color: white; width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">SO</div>
            <h2 class="fw-bold">SkillOwners Admin</h2>
            <p class="text-white-50">Secure Access Only</p>
        </div>

        <div id="g_id_onload"
             data-client_id="<?php echo GOOGLE_CLIENT_ID; ?>"
             data-login_uri="<?php echo SITE_URL; ?>/alpha/auth_callback.php"
             data-auto_prompt="false">
        </div>
        <div class="g_id_signin w-100 d-flex justify-content-center" data-type="standard" data-size="large" data-theme="filled_black" data-text="signin_with" data-shape="rectangular" data-logo_alignment="left"></div>
        
        <?php if ($_SERVER['HTTP_HOST'] === 'localhost'): ?>
            <div class="mt-4 pt-3 border-top border-secondary text-center">
                <p class="small text-white-50 mb-2">Local Development Only</p>
                <form action="index.php" method="POST">
                    <input type="hidden" name="local_bypass" value="1">
                    <button type="submit" class="btn btn-outline-success btn-sm w-100">Bypass Google Login</button>
                </form>
            </div>
        <?php endif; ?>
        
        <?php if ($error = getError()): ?>
            <div class="alert alert-danger mt-4 small"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
    exit();
}

// Set admin session if needed and available...
// If we reach here, user IS the admin (see config.php)

$db = getDBConnection();

// Fetch Stats
$stats = [
    'contacts' => $db->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn(),
    'quotes' => $db->query("SELECT COUNT(*) FROM quote_requests")->fetchColumn(),
    'blogs' => $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
    'portfolio' => $db->query("SELECT COUNT(*) FROM portfolio_projects")->fetchColumn(),
];

// Recent Quotes
$recentQuotes = $db->query("SELECT * FROM quote_requests ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<?php
// (Same session and login check logic as before)
// ... keeping logic intact ...
include '../../views/partials/header.php';
?>
<?php include 'alpha_sidebar.php'; ?>

<main class="content">
    <div class="container-fluid p-0">
        <header class="mb-5">
            <h1 class="h3 fw-bold mb-0">Overview Dashboard</h1>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3">
                <div class="glass-card p-4 stat-card border">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                    <h5 class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Quotes</h5>
                    <h2 class="fw-bold mb-0"><?php echo $stats['quotes']; ?></h2>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="glass-card p-4 stat-card border">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-envelope"></i></div>
                    </div>
                    <h5 class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Contacts</h5>
                    <h2 class="fw-bold mb-0"><?php echo $stats['contacts']; ?></h2>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="glass-card p-4 stat-card border">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-blog"></i></div>
                    </div>
                    <h5 class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Posts</h5>
                    <h2 class="fw-bold mb-0"><?php echo $stats['blogs']; ?></h2>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="glass-card p-4 stat-card border">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-project-diagram"></i></div>
                    </div>
                    <h5 class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Projects</h5>
                    <h2 class="fw-bold mb-0"><?php echo $stats['portfolio']; ?></h2>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100 shadow-sm transition-all border">
                    <h5 class="fw-bold mb-4">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <a href="services.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none text-dark hover-bg-light transition-all h-100">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3"><i class="fas fa-plus-circle"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Add New Service</h6>
                                    <small class="text-muted">Launch a new agency offering</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="portfolio.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none text-dark hover-bg-light transition-all h-100">
                                <div class="icon-box bg-success bg-opacity-10 text-success p-3 rounded-3 me-3"><i class="fas fa-folder-plus"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">New Portfolio Project</h6>
                                    <small class="text-muted">Showcase recent work</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="blog.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none text-dark hover-bg-light transition-all h-100">
                                <div class="icon-box bg-info bg-opacity-10 text-info p-3 rounded-3 me-3"><i class="fas fa-edit"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Write Blog Post</h6>
                                    <small class="text-muted">Share industry insights</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="<?php echo SITE_URL; ?>" target="_blank" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none text-dark hover-bg-light transition-all h-100">
                                <div class="icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3"><i class="fas fa-eye"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">View Live Site</h6>
                                    <small class="text-muted">See changes in real-time</small>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <h5 class="fw-bold mb-4">System Information</h5>
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3 text-center">
                                    <small class="text-muted d-block mb-1">PHP</small>
                                    <span class="fw-bold small"><?php echo phpversion(); ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3 text-center">
                                    <small class="text-muted d-block mb-1">Mode</small>
                                    <span class="fw-bold text-success small">Live</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3 text-center">
                                    <small class="text-muted d-block mb-1">DB</small>
                                    <span class="fw-bold small">Online</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100 shadow-sm border">
                    <h5 class="fw-bold mb-4">Recent Activity</h5>
                    <div class="timeline px-2">
                        <?php foreach($recentQuotes as $quote): ?>
                        <div class="d-flex gap-3 mb-4 position-relative">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 42px; height: 42px; flex-shrink: 0; z-index: 2;">
                                <i class="fas fa-file-contract text-primary small"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 small">New Quote Request</h6>
                                <p class="text-muted mb-1" style="font-size: 0.8rem;"><?php echo htmlspecialchars($quote['full_name']); ?></p>
                                <span class="text-muted opacity-50" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i><?php echo date('H:i, M d', strtotime($quote['created_at'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; if(empty($recentQuotes)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-box-open text-muted mb-2 d-block" style="font-size: 2rem;"></i>
                                <p class="text-muted small">No activity detected.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="quotes.php" class="btn btn-outline-primary w-100 btn-sm mt-3 rounded-pill">View All Quotes</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../views/partials/footer.php'; ?>
