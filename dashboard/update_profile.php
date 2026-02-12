<?php
require_once '../config.php';
require_once '../models/User.php';

requireLogin();

$userId = $_SESSION['user_id'];
$userModel = new User();
$user = $userModel->findById($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => sanitizeInput($_POST['full_name'] ?? ''),
        'bio' => sanitizeInput($_POST['bio'] ?? ''),
        'skills' => sanitizeInput($_POST['skills'] ?? ''),
        'portfolio_link' => sanitizeInput($_POST['portfolio_link'] ?? '')
    ];

    // Handle Profile Image Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileType = $file['type'];
        
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validation Rules
        $maxSize = 500 * 1024; // 500kb
        $allowedExt = ['webp'];
        $allowedType = ['image/webp'];

        if ($fileSize > $maxSize) {
            showError("Profile image must be less than 500kb.");
            redirect('/dashboard/' . getUserRole() . '/profile.php');
        }

        if (!in_array($fileExt, $allowedExt) || !in_array($fileType, $allowedType)) {
            showError("Only WebP images are allowed for profile pictures.");
            redirect('/dashboard/' . getUserRole() . '/profile.php');
        }

        // Generate unique filename
        $newFileName = 'avatar_' . $userId . '_' . time() . '.webp';
        $uploadPath = UPLOAD_DIR . $newFileName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            // Delete old image if exists
            if (!empty($user['profile_image'])) {
                $oldPath = UPLOAD_DIR . $user['profile_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['profile_image'] = $newFileName;
            $_SESSION['user_image'] = $newFileName;
        } else {
            showError("Failed to upload image. Please try again.");
            redirect('/dashboard/' . getUserRole() . '/profile.php');
        }
    }

    if ($userModel->update($userId, $data)) {
        showSuccess("Profile updated successfully.");
    } else {
        showError("Failed to update profile information.");
    }

    redirect('/dashboard/' . getUserRole() . '/profile.php');
}

redirect('/dashboard/' . getUserRole() . '/profile.php');
