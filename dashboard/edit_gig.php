<?php
require_once '../config.php';
requireLogin();

$userRole = getUserRole();
if ($userRole !== 'freelancer' && $userRole !== 'agency') {
    redirect('/dashboard/' . $userRole);
}

require_once '../models/Gig.php';

$gigModel = new Gig();
$userId = $_SESSION['user_id'];
$gigId = $_GET['id'] ?? 0;

// Fetch gig and verify ownership
$gig = $gigModel->findById($gigId);

// Note: findById has g.is_active = 1, but we might want to edit inactive gigs too.
// However, the current model findById is restrictive. 
// Let's check if we need a more permissive find for owner.
if (!$gig || $gig['user_id'] != $userId) {
    // Try to find even if inactive if the user is the owner
    $sql = "SELECT * FROM gigs WHERE id = :id AND user_id = :user_id";
    $stmt = getDBConnection()->prepare($sql);
    $stmt->execute(['id' => $gigId, 'user_id' => $userId]);
    $gig = $stmt->fetch();
    
    if (!$gig) {
        showError('Gig not found or access denied.');
        redirect('/dashboard/freelancer/gigs');
    }
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $delivery_time = $_POST['delivery_time'] ?? '';
    $tags = sanitizeInput($_POST['tags'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image = $gig['image']; // Keep existing by default

    // Validation
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($description)) $errors[] = 'Description is required';
    
    // Handle Custom Category
    if ($category === 'other') {
        $custom_category = sanitizeInput($_POST['custom_category'] ?? '');
        if (empty($custom_category)) {
            $errors[] = 'Please specify your custom category';
        } else {
            $category = $custom_category;
        }
    }

    if (empty($category)) $errors[] = 'Category is required';
    if (!is_numeric($price) || $price <= 0) $errors[] = 'Valid price is required';
    if (!is_numeric($delivery_time) || $delivery_time <= 0) $errors[] = 'Valid delivery time is required';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
            $errors[] = 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS);
        } elseif ($fileSize > MAX_FILE_SIZE) {
            $errors[] = 'File size too large. Maximum: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB';
        } else {
            $newFileName = uniqid() . '.' . $fileExt;
            $uploadPath = UPLOAD_DIR . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $image = $newFileName;
                // Optional: Delete old image file if it exists
            } else {
                $errors[] = 'Failed to upload image';
            }
        }
    }

    if (empty($errors)) {
        $updateData = [
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'price' => $price,
            'delivery_time' => $delivery_time,
            'image' => $image,
            'tags' => $tags,
            'is_active' => $is_active
        ];

        if ($gigModel->update($gigId, $userId, $updateData)) {
            showSuccess('Gig updated successfully!');
            redirect('/dashboard/' . ($userRole === 'agency' ? 'agency/services' : 'freelancer/gigs'));
        } else {
            $errors[] = 'No changes made or failed to update gig.';
        }
    }
}

include '../views/partials/header.php';
?>

<div class="dashboard-layout" style="margin-top: 0; padding-top: 80px; background: var(--background);">
    <div class="container py-5">
        <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 1.25rem; padding: 2.5rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.03);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 font-weight-600">Edit Gig</h1>
                <a href="<?php echo SITE_URL; ?>/gig?id=<?php echo $gigId; ?>" class="btn btn-outline" target="_blank">
                    <i class="fas fa-eye"></i> View Live
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label font-weight-500">Gig Title *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? $gig['title']); ?>" required 
                               style="padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label font-weight-500">Description *</label>
                        <textarea name="description" class="form-control" rows="6" required 
                                  style="padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; resize: vertical;"><?php echo htmlspecialchars($_POST['description'] ?? $gig['description']); ?></textarea>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label font-weight-500">Category *</label>
                        <?php 
                        $currentCat = $_POST['category'] ?? $gig['category'];
                        $standardCats = ['web-development', 'mobile-development', 'design', 'writing', 'marketing'];
                        $isOther = !empty($currentCat) && !in_array($currentCat, $standardCats);
                        ?>
                        <select name="category" id="categorySelect" class="form-control" required
                                style="padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; height: auto;">
                            <option value="">Select Category</option>
                            <option value="web-development" <?php echo $currentCat === 'web-development' ? 'selected' : ''; ?>>Web Development</option>
                            <option value="mobile-development" <?php echo $currentCat === 'mobile-development' ? 'selected' : ''; ?>>Mobile Development</option>
                            <option value="design" <?php echo $currentCat === 'design' ? 'selected' : ''; ?>>Design</option>
                            <option value="writing" <?php echo $currentCat === 'writing' ? 'selected' : ''; ?>>Writing</option>
                            <option value="marketing" <?php echo $currentCat === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                            <option value="other" <?php echo $isOther ? 'selected' : ''; ?>>Other (Custom)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4" id="customCategoryGroup" style="<?php echo $isOther ? 'display: block;' : 'display: none;'; ?>">
                        <label class="form-label font-weight-500">Custom Category Name *</label>
                        <input type="text" name="custom_category" class="form-control" value="<?php echo $isOther ? htmlspecialchars($currentCat) : ''; ?>"
                               style="padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label font-weight-500">Price (USD) *</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #f8fafc; border: 1px solid #e2e8f0; border-right: none; border-radius: 0.75rem 0 0 0.75rem;">$</span>
                            <input type="number" name="price" class="form-control" step="0.01" min="1" value="<?php echo htmlspecialchars($_POST['price'] ?? $gig['price']); ?>" required
                                   style="padding: 0.75rem; border-radius: 0 0.75rem 0.75rem 0; border: 1px solid #e2e8f0; border-left: none;">
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label font-weight-500">Delivery Time (days) *</label>
                        <input type="number" name="delivery_time" class="form-control" min="1" value="<?php echo htmlspecialchars($_POST['delivery_time'] ?? $gig['delivery_time']); ?>" required
                               style="padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label font-weight-500">Tags (comma separated)</label>
                        <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($_POST['tags'] ?? $gig['tags']); ?>"
                               style="padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label font-weight-500">Gig Image</label>
                        <?php if (!empty($gig['image'])): ?>
                            <div class="mb-3">
                                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $gig['image']; ?>" alt="Current Image" style="width: 150px; border-radius: 0.5rem; border: 1px solid #f1f5f9;">
                                <p class="text-xs text-muted mt-1">Leave empty to keep current image</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*"
                               style="padding: 0.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; height: auto;">
                    </div>

                    <div class="col-md-12 mb-4">
                        <div class="form-check form-switch p-0" style="display: flex; align-items: center; gap: 10px;">
                            <input class="form-check-input" type="checkbox" name="is_active" style="margin: 0; cursor: pointer; width: 2.5rem; height: 1.25rem;" id="activeToggle" <?php echo ($gig['is_active'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label font-weight-500" for="activeToggle" style="cursor: pointer;">Active (Visible on marketplace)</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem;">Save Changes</button>
                    <a href="<?php echo SITE_URL; ?>/dashboard/freelancer/gigs" class="btn btn-outline" style="padding: 0.75rem 2rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const customGroup = document.getElementById('customCategoryGroup');
    const customInput = customGroup.querySelector('input');

    function toggleCustomCategory() {
        if (categorySelect.value === 'other') {
            customGroup.style.display = 'block';
            customInput.required = true;
        } else {
            customGroup.style.display = 'none';
            customInput.required = false;
        }
    }

    categorySelect.addEventListener('change', toggleCustomCategory);
});
</script>

<?php include '../views/partials/footer.php'; ?>
