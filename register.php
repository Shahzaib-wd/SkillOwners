<?php
/**
 * Skill Owners - Registration Page
 */

require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getUserRole();
    redirect('/dashboard/' . $role . '.php');
}

$error = getError();
$success = getSuccess();
$selectedRole = $_GET['role'] ?? 'buyer';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? 'buyer');
    
    // Validation
    if (empty($fullName) || empty($email) || empty($password) || empty($role)) {
        showError('All fields are required');
    } elseif ($password !== $confirmPassword) {
        showError('Passwords do not match');
    } elseif (strlen($password) < 8) {
        showError('Password must be at least 8 characters');
    } elseif (!in_array($role, ['freelancer', 'agency', 'buyer'])) {
        showError('Invalid role selected');
    } else {
        require_once 'controllers/AuthController.php';
        $authController = new AuthController();
        $authController->register($fullName, $email, $password, $role);
    }
    
    $error = getError();
    $success = getSuccess();
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
        max-width: 520px;
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
    
    .role-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .role-option {
        position: relative;
    }
    
    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .role-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1rem 0.5rem;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .role-option input[type="radio"]:checked + .role-label {
        border-color: var(--primary);
        background: hsla(252, 85%, 60%, 0.05);
    }
    
    .role-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--primary);
    }
    
    .role-name {
        font-size: 0.875rem;
        font-weight: 500;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Join Skill Owners</h1>
            <p>Create your account and start your journey</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" id="buyer" name="role" value="buyer" <?php echo $selectedRole === 'buyer' ? 'checked' : ''; ?>>
                    <label for="buyer" class="role-label">
                        <div class="role-icon"><i class="fas fa-shopping-cart"></i></div>
                        <span class="role-name">Buyer</span>
                    </label>
                </div>
                
                <div class="role-option">
                    <input type="radio" id="freelancer" name="role" value="freelancer" <?php echo $selectedRole === 'freelancer' ? 'checked' : ''; ?>>
                    <label for="freelancer" class="role-label">
                        <div class="role-icon"><i class="fas fa-user"></i></div>
                        <span class="role-name">Freelancer</span>
                    </label>
                </div>
                
                <div class="role-option">
                    <input type="radio" id="agency" name="role" value="agency" <?php echo $selectedRole === 'agency' ? 'checked' : ''; ?>>
                    <label for="agency" class="role-label">
                        <div class="role-icon"><i class="fas fa-users"></i></div>
                        <span class="role-name">Agency</span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                <small class="form-text">Must be at least 8 characters</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.813rem; color: var(--muted-foreground);">
                    <input type="checkbox" required>
                    I agree to the <a href="<?php echo SITE_URL; ?>/terms_conditions.php" style="color: var(--primary);">Terms & Conditions</a> 
                    and <a href="<?php echo SITE_URL; ?>/privacy_policy.php" style="color: var(--primary);">Privacy Policy</a>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                Create Account
            </button>
            
            <p class="text-center" style="font-size: 0.875rem; color: var(--muted-foreground);">
                Already have an account? 
                <a href="<?php echo SITE_URL; ?>/login.php" style="color: var(--primary); font-weight: 500;">Log in</a>
            </p>
        </form>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
