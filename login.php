<?php
/**
 * Skill Owners - Login Page
 */

require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getUserRole();
    if ($role === 'admin') {
        redirect('/dashboard/alpha');
    } else {
        redirect('/dashboard/' . $role);
    }
}

$error = getError();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        showError('Please enter both email and password');
    } else {
        require_once 'controllers/AuthController.php';
        $authController = new AuthController();
        $authController->login($email, $password);
    }
    
    $error = getError();
}

include 'views/partials/header.php';
?>

<style>
    .auth-container {
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        margin-top: 64px;
        background: var(--gradient-hero);
    }
    
    .auth-card {
        width: 100%;
        max-width: 420px;
        background: var(--card);
        border-radius: var(--radius);
        padding: 2.5rem;
        box-shadow: var(--shadow-elevated);
    }
    
    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .auth-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .auth-header p {
        color: var(--muted-foreground);
        font-size: 0.875rem;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Log in to your Skill Owners account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" style="font-size: 0.875rem; margin-left: 0.25rem;">Remember me</label>
                </div>
                <a href="<?php echo SITE_URL; ?>/reset_password" style="font-size: 0.875rem; color: var(--primary);">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                Log In
            </button>
            
            <p class="text-center" style="font-size: 0.875rem; color: var(--muted-foreground);">
                Don't have an account? 
                <a href="<?php echo SITE_URL; ?>/register" style="color: var(--primary); font-weight: 500;">Sign up</a>
            </p>
        </form>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
