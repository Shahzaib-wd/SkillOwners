<?php
/**
 * Authentication Controller
 * Handles user authentication (login, register, logout, password reset)
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function register($fullName, $email, $password, $role) {
        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            showError('Email address is already registered');
            return false;
        }
        
        // Create user
        if ($this->userModel->create($fullName, $email, $password, $role)) {
            showSuccess('Account created successfully! Please log in.');
            redirect('/login.php');
            return true;
        } else {
            showError('Registration failed. Please try again.');
            return false;
        }
    }
    
    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            showError('Invalid email or password');
            return false;
        }
        
        if (!password_verify($password, $user['password'])) {
            showError('Invalid email or password');
            return false;
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_image'] = $user['profile_image'];
        $_SESSION['login_time'] = time();
        
        // Redirect to dashboard
        redirect('/dashboard/' . $user['role'] . '.php');
        return true;
    }
    
    public function logout() {
        session_destroy();
        redirect('/login.php');
    }
    
    public function requestPasswordReset($email) {
        if (!$this->userModel->emailExists($email)) {
            showError('Email address not found');
            return false;
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY);
        
        // Save token to database
        $conn = getDBConnection();
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
        
        // Send reset email
        $this->sendPasswordResetEmail($email, $token);
        
        showSuccess('Password reset link has been sent to your email');
        return true;
    }
    
    public function resetPassword($token, $newPassword) {
        $conn = getDBConnection();
        
        // Check if token is valid and not expired
        $sql = "SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['token' => $token]);
        $reset = $stmt->fetch();
        
        if (!$reset) {
            showError('Invalid or expired reset token');
            return false;
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'password' => $hashedPassword,
            'email' => $reset['email']
        ]);
        
        // Delete used token
        $sql = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['token' => $token]);
        
        showSuccess('Password reset successful! Please log in with your new password.');
        return true;
    }
    
    private function sendPasswordResetEmail($email, $token) {
        // Using PHPMailer (must be installed via Composer)
        // For now, return true (implement PHPMailer integration separately)
        $resetLink = SITE_URL . '/reset_password.php?token=' . $token;
        
        // Log the reset link for development
        error_log("Password reset link for $email: $resetLink");
        
        // TODO: Implement PHPMailer
        /*
        require 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($email);
        $mail->Subject = 'Password Reset - Skill Owners';
        $mail->Body = "Click here to reset your password: $resetLink";
        
        $mail->send();
        */
        
        return true;
    }
}
