<?php
require_once '../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../models/Gig.php';

$gigModel = new Gig();
$userId = $_SESSION['user_id'];

// Check gig limit
$gigs = $gigModel->findByUserId($userId);
if (count($gigs) >= MAX_GIGS) {
    showError('You have reached the maximum number of gigs allowed.');
    redirect('/dashboard/freelancer.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $delivery_time = $_POST['delivery_time'] ?? '';
    $tags = sanitizeInput($_POST['tags'] ?? '');
    $image = null;

    // Validation
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($description)) $errors[] = 'Description is required';
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
            } else {
                $errors[] = 'Failed to upload image';
            }
        }
    }

    if (empty($errors)) {
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'price' => $price,
            'delivery_time' => $delivery_time,
            'image' => $image,
            'tags' => $tags
        ];

        if ($gigModel->create($data)) {
            showSuccess('Gig created successfully!');
            redirect('/dashboard/freelancer.php');
        } else {
            $errors[] = 'Failed to create gig. Please try again.';
        }
    }
}

include '../views/partials/header.php';
?>

<style>
.create-gig {
    padding: 5rem 0 3rem;
    min-height: 100vh;
    background: var(--background);
}
.form-container {
    max-width: 800px;
    margin: 0 auto;
    background: var(--card);
    border-radius: var(--radius);
    padding: 2rem;
    border: 1px solid var(--border);
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: var(--background);
    color: var(--foreground);
}
.form-control:focus {
    outline: none;
    border-color: var(--primary);
}
.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-primary {
    background: var(--primary);
    color: var(--primary-foreground);
}
.btn-primary:hover {
    background: var(--primary-hover);
}
.alert {
    padding: 1rem;
    border-radius: var(--radius);
    margin-bottom: 1rem;
}
.alert-danger {
    background: #fee;
    border: 1px solid #fcc;
    color: #c33;
}
</style>

<div class="create-gig">
    <div class="container">
        <div class="form-container">
            <h1 class="mb-4">Create New Gig</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Gig Title *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="web-development" <?php echo ($_POST['category'] ?? '') === 'web-development' ? 'selected' : ''; ?>>Web Development</option>
                        <option value="mobile-development" <?php echo ($_POST['category'] ?? '') === 'mobile-development' ? 'selected' : ''; ?>>Mobile Development</option>
                        <option value="design" <?php echo ($_POST['category'] ?? '') === 'design' ? 'selected' : ''; ?>>Design</option>
                        <option value="writing" <?php echo ($_POST['category'] ?? '') === 'writing' ? 'selected' : ''; ?>>Writing</option>
                        <option value="marketing" <?php echo ($_POST['category'] ?? '') === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                        <option value="other" <?php echo ($_POST['category'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Price (USD) *</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="1" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Delivery Time (days) *</label>
                    <input type="number" name="delivery_time" class="form-control" min="1" value="<?php echo htmlspecialchars($_POST['delivery_time'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>" placeholder="e.g. php, mysql, web development">
                </div>

                <div class="form-group">
                    <label class="form-label">Gig Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="form-text">Optional. Max size: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB. Allowed: <?php echo implode(', ', ALLOWED_EXTENSIONS); ?></small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Gig</button>
                    <a href="<?php echo SITE_URL; ?>/dashboard/freelancer.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../views/partials/footer.php'; ?>
