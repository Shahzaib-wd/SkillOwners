<?php
/**
 * Skill Owners - Configuration File
 * Database and site configuration
 */

// Force PHP to tell us what is wrong
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    // We read the file line by line to avoid the 'parse_ini_file' syntax crashes
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;

        // Only process lines with an equal sign
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim(trim($value), '"'); // Removes quotes if present

            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}



// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'skill_owners');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Site Configuration
define('SITE_NAME', 'Skill Owners');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/skill_owners');
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@skillowners.com');

// Security
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour

// Limits
define('MAX_GIGS', 5);
define('MAX_PROJECTS', 3);
define('MAX_PORTFOLIO_LINKS', 1);

// File Upload
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// SMTP Configuration for PHPMailer
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM', getenv('SMTP_FROM') ?: 'noreply@skillowners.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Skill Owners');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Timezone
date_default_timezone_set('UTC');

// Start session
session_start();

// Database Connection
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $conn;
}

// Security Functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function checkRole($allowedRoles) {
    if (!isLoggedIn()) {
        return false;
    }
    return in_array(getUserRole(), (array)$allowedRoles);
}

function redirect($path) {
    header('Location: ' . SITE_URL . $path);
    exit();
}

function showError($message) {
    $_SESSION['error'] = $message;
}

function showSuccess($message) {
    $_SESSION['success'] = $message;
}

function getError() {
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

function getSuccess() {
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
        return $success;
    }
    return null;
}

// ============================================================
// Agency Permission Functions
// ============================================================

/**
 * Check if current user has permission in agency
 */
function hasAgencyPermission($agencyId, $permission) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Agency owner has all permissions
    if ($agencyId == $userId) {
        return true;
    }
    
    require_once __DIR__ . '/models/AgencyMember.php';
    $memberModel = new AgencyMember();
    
    return $memberModel->hasPermission($agencyId, $userId, $permission);
}

/**
 * Check if current user is agency admin
 */
function isAgencyAdmin($agencyId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Agency owner is admin
    if ($agencyId == $userId) {
        return true;
    }
    
    require_once __DIR__ . '/models/AgencyMember.php';
    $memberModel = new AgencyMember();
    
    $role = $memberModel->getMemberRole($agencyId, $userId);
    return $role === 'admin';
}

/**
 * Get current user's role in agency
 */
function getAgencyRole($agencyId) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Agency owner is admin
    if ($agencyId == $userId) {
        return 'admin';
    }
    
    require_once __DIR__ . '/models/AgencyMember.php';
    $memberModel = new AgencyMember();
    
    return $memberModel->getMemberRole($agencyId, $userId);
}

/**
 * Check if user is member of agency
 */
function isAgencyMember($agencyId, $userId = null) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = $userId ?? $_SESSION['user_id'];
    
    // Agency owner is a member
    if ($agencyId == $userId) {
        return true;
    }
    
    require_once __DIR__ . '/models/AgencyMember.php';
    $memberModel = new AgencyMember();
    
    return $memberModel->isMember($agencyId, $userId);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require CSRF token validation
 */
function requireCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
            die('CSRF token validation failed.');
        }
    }
}


function containsProfanity($text) {
    if (empty($text)) return false;
    
    // List of common abusive words (simplified for this task)
    $badWords = [
        'abuse', 'stupid', 'idiot', 'garbage', 'trash', 
        'scam', 'fraud', 'fake', 'liar', 'bastard',
        'damn', 'hell', 'shit', 'fuck', 'bitch'
    ];
    
    $lowerText = strtolower($text);
    foreach ($badWords as $word) {
        if (strpos($lowerText, $word) !== false) {
            return true;
        }
    }
    
    return false;
}
