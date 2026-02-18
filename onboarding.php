<?php
/**
 * Skill Owners - Onboarding (Role Selection for Google SSO Users)
 */
require_once 'config.php';
require_once 'models/User.php';

requireLogin();

$userId = $_SESSION['user_id'];
$userModel = new User();
$user = $userModel->findById($userId);

if (!$user) {
    showError("User not found.");
    redirect('/login');
}

// Handle role selection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = sanitizeInput($_POST['role'] ?? '');
    
    if (!in_array($role, ['freelancer', 'agency', 'buyer'])) {
        showError('Invalid role selected.');
    } else {
        $userModel->update($userId, ['role' => $role]);
        $_SESSION['user_role'] = $role;
        showSuccess("Welcome to Skill Owners! You're all set as a " . ucfirst($role) . ".");
        
        if ($role === 'buyer') {
            redirect('/browse');
        } else {
            redirect('/dashboard/' . $role);
        }
    }
}

$error = getError();
$success = getSuccess();

include 'views/partials/header.php';
?>

<style>
    .onboarding-container {
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        margin-top: 64px;
        background: var(--gradient-hero);
    }
    
    .onboarding-card {
        width: 100%;
        max-width: 600px;
        background: var(--card);
        border-radius: var(--radius);
        padding: 3rem;
        box-shadow: var(--shadow-elevated);
        text-align: center;
    }
    
    .onboarding-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .onboarding-header p {
        color: var(--muted-foreground);
        font-size: 1rem;
        margin-bottom: 2rem;
    }
    
    .role-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .role-card {
        position: relative;
        cursor: pointer;
    }
    
    .role-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .role-card-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem;
        border: 2px solid var(--border);
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
    }
    
    .role-card input[type="radio"]:checked + .role-card-label {
        border-color: var(--primary);
        background: hsla(252, 85%, 60%, 0.04);
        box-shadow: 0 0 0 4px hsla(252, 85%, 60%, 0.1);
        transform: translateY(-4px);
    }
    
    .role-card-label:hover {
        border-color: #c7d2fe;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }
    
    .role-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .role-card-icon.buyer { background: #dcfce7; color: #15803d; }
    .role-card-icon.freelancer { background: #e0e7ff; color: #4338ca; }
    .role-card-icon.agency { background: #fae8ff; color: #a21caf; }
    
    .role-card-name {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .role-card-desc {
        font-size: 0.75rem;
        color: var(--muted-foreground);
        line-height: 1.4;
    }
    
    .onboarding-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary);
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
        margin-bottom: 1rem;
    }
    
    .onboarding-avatar-fallback {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
    }

    @media (max-width: 576px) {
        .role-cards {
            grid-template-columns: 1fr;
        }
        .onboarding-card {
            padding: 2rem 1.5rem;
        }
    }
</style>

<div class="onboarding-container">
    <div class="onboarding-card">
        <?php if (!empty($user['profile_image'])): ?>
            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" 
                 class="onboarding-avatar" alt="<?php echo htmlspecialchars($user['full_name']); ?>">
        <?php else: ?>
            <div class="onboarding-avatar-fallback">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
        <?php endif; ?>
        
        <div class="onboarding-header">
            <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?>! 👋</h1>
            <p>How would you like to use Skill Owners?</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error mb-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="role-cards">
                <div class="role-card">
                    <input type="radio" id="role_buyer" name="role" value="buyer" checked>
                    <label for="role_buyer" class="role-card-label">
                        <div class="role-card-icon buyer"><i class="fas fa-shopping-bag"></i></div>
                        <span class="role-card-name">Buyer</span>
                        <span class="role-card-desc">Hire talent for your projects</span>
                    </label>
                </div>
                
                <div class="role-card">
                    <input type="radio" id="role_freelancer" name="role" value="freelancer">
                    <label for="role_freelancer" class="role-card-label">
                        <div class="role-card-icon freelancer"><i class="fas fa-laptop-code"></i></div>
                        <span class="role-card-name">Freelancer</span>
                        <span class="role-card-desc">Offer your skills & services</span>
                    </label>
                </div>
                
                <div class="role-card">
                    <input type="radio" id="role_agency" name="role" value="agency">
                    <label for="role_agency" class="role-card-label">
                        <div class="role-card-icon agency"><i class="fas fa-building"></i></div>
                        <span class="role-card-name">Agency</span>
                        <span class="role-card-desc">Manage a team of freelancers</span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-weight: 700; border-radius: 12px; font-size: 1rem;">
                Get Started <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
