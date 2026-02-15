<?php
/**
 * Skill Owners - Password Reset Page (OTP Version)
 */

require_once 'config.php';
require_once 'controllers/AuthController.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getUserRole();
    redirect('/dashboard/' . $role);
}

$authController = new AuthController();
$error = getError();
$success = getSuccess();

$step = $_SESSION['reset_step'] ?? 1;
$resetEmail = $_SESSION['reset_email'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("RESET_PASSWORD POST: " . print_r($_POST, true));
    error_log("RESET_PASSWORD SESSION: " . print_r($_SESSION, true));
    
    // Step 1: Request OTP
    if (isset($_POST['request_otp'])) {
        $email = sanitizeInput($_POST['email'] ?? '');
        if (empty($email)) {
            $error = 'Please enter your email address';
        } else {
            if ($authController->requestPasswordReset($email)) {
                $_SESSION['reset_step'] = 2;
                $_SESSION['reset_email'] = $email;
                $success = "An OTP has been sent to your email."; // Local success message
                $step = 2;
                $resetEmail = $email;
            } else {
                // If request failed (e.g. email not found), AuthController called showError()
                // We need to fetch it
                $error = getError();
            }
        }
    } 
    
    // Step 2: Verify OTP
    elseif (isset($_POST['verify_otp'])) {
        $otp = $_POST['otp'] ?? '';
        if (empty($otp)) {
            $error = 'Please enter the OTP';
        } else {
            // Debug logging
            error_log("Attempting to verify OTP for $resetEmail");
            
            if ($authController->verifyOTP($resetEmail, $otp)) {
                $_SESSION['reset_step'] = 3;
                $_SESSION['reset_otp'] = $otp; // Store verified OTP
                $step = 3;
            } else {
                $error = 'Invalid or expired OTP';
            }
        }
    } 
    
    // Step 3: Set New Password
    elseif (isset($_POST['set_password'])) {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $otp = $_SESSION['reset_otp'] ?? '';
        
        if (empty($password) || empty($confirmPassword)) {
            $error = 'Please fill in all fields';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } else {
            if ($authController->resetPassword($resetEmail, $otp, $password)) {
                // Cleanup session
                unset($_SESSION['reset_step']);
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_otp']);
                redirect('/login');
            } else {
                // AuthController sets session error
                $error = getError();
            }
        }
    }
    
    // Allow going back / restarting
    elseif (isset($_POST['restart'])) {
        unset($_SESSION['reset_step']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_otp']);
        redirect('/reset_password'); // Reload clean
    }
    
    if (empty($error)) {
        $error = getError();
    }
    //$success = getSuccess(); // Don't overwrite local success if set
}

include 'views/partials/header.php';
?>

<div class="auth-container" style="min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; margin-top: 64px; background: var(--gradient-hero);">
    <div class="auth-card" style="width: 100%; max-width: 420px; background: var(--card); border-radius: var(--radius); padding: 2.5rem; box-shadow: var(--shadow-elevated);">
        <div class="auth-header" style="text-align: center; margin-bottom: 2rem;">
            <h1>Reset Password</h1>
            <?php if ($step === 1): ?>
                <p>Enter your email to receive a One-Time Password</p>
            <?php elseif ($step === 2): ?>
                <p>Enter the 6-digit code sent to <strong><?php echo htmlspecialchars($resetEmail); ?></strong></p>
            <?php elseif ($step === 3): ?>
                <p>Create a new password for your account</p>
            <?php endif; ?>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 0.5rem; background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 0.5rem; background: #f0fdf4; color: #166534; border: 1px solid #dcfce7;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?php if ($step === 1): ?>
                <div class="form-group mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="your@email.com">
                </div>
                <button type="submit" name="request_otp" class="btn btn-primary w-100">Send OTP</button>
            
            <?php elseif ($step === 2): ?>
                <div class="form-group mb-4">
                    <label for="otp" class="form-label">One-Time Password</label>
                    <input type="text" id="otp" name="otp" class="form-control" required placeholder="123456" maxlength="6" style="letter-spacing: 5px; text-align: center; font-size: 1.5rem;">
                </div>
                <button type="submit" name="verify_otp" class="btn btn-primary w-100">Verify Code</button>
                <div class="text-center mt-3">
                     <button type="submit" name="restart" class="btn btn-link text-muted" style="text-decoration: none;">Resend / Change Email</button>
                </div>

            <?php elseif ($step === 3): ?>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="At least 8 characters">
                </div>
                <div class="form-group mb-4">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Repeat your new password">
                </div>
                <button type="submit" name="set_password" class="btn btn-primary w-100">Reset Password</button>
            <?php endif; ?>
        </form>

        <div class="text-center mt-4">
            <a href="login" style="color: var(--primary); font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
