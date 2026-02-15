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
            redirect('/login');
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
        
        // Redirect buyers to browse.php, others to their dashboard
        if ($user['role'] === 'buyer') {
            redirect('/browse');
        } elseif ($user['role'] === 'admin') {
            redirect('/dashboard/alpha');
        } else {
            redirect('/dashboard/' . $user['role']);
        }
        return true;
    }
    
    public function logout() {
        session_destroy();
        redirect('/login');
    }
    
    public function requestPasswordReset($email) {
        if (!$this->userModel->emailExists($email)) {
            showError('Email address not found');
            return false;
        }
        
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        // OTP expires in 15 minutes
        $expiresAt = date('Y-m-d H:i:s', time() + 900); 
        
        // Delete any existing tokens for this email to keep it clean
        $conn = getDBConnection();
        $sql = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);

        // Save OTP to database
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'token' => $otp,
            'expires_at' => $expiresAt
        ]);
        
        // Send OTP email
        $this->sendPasswordResetEmail($email, $otp);
        
        return true;
    }
    
    public function verifyOTP($email, $otp) {
        $conn = getDBConnection();
        $sql = "SELECT token FROM password_resets WHERE email = :email AND expires_at > NOW()"; // Get token for email
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $storedToken = $stmt->fetchColumn();
        
        // Debug
        error_log("VerifyOTP: Input='$otp', Stored='$storedToken'");
        
        if ($storedToken && (string)$storedToken === (string)trim($otp)) {
            return true;
        }
        return false;
    }

    public function resetPassword($email, $otp, $newPassword) {
        if (!$this->verifyOTP($email, $otp)) {
            showError('Invalid or expired OTP');
            return false;
        }

        $conn = getDBConnection();
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'password' => $hashedPassword,
            'email' => $email
        ]);
        
        // Delete used OTP
        $sql = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        showSuccess('Password reset successful! Please log in with your new password.');
        return true;
    }
    
    private function sendPasswordResetEmail($email, $otp) {
        require_once __DIR__ . '/../helpers/MailHelper.php';
        
        $subject = 'Password Reset OTP - ' . SITE_NAME;
        $body = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2>Password Reset Request</h2>
            <p>Your One-Time Password (OTP) to reset your password is:</p>
            <h1 style='background-color: #f3f4f6; padding: 10px; display: inline-block; letter-spacing: 5px; border-radius: 5px;'>$otp</h1>
            <p>This code will expire in 15 minutes.</p>
            <p>If you did not request this change, please ignore this email.</p>
        </div>";
        
        error_log("Sending OTP $otp to $email");
        
        return MailHelper::send($email, $subject, $body);
    }
}
