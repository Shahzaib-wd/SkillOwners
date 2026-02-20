<?php
require_once '../../config.php';

// Check if logged in specifically as the designated admin
$admin_email = ADMIN_LOGIN_EMAIL;
if (!isset($_SESSION['user_id']) || $_SESSION['user_email'] !== $admin_email) {
    redirect('/alpha');
}

$db = getDBConnection();

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $title = sanitizeInput($_POST['title'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $excerpt = sanitizeInput($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? ''; // Allow HTML
    $status = sanitizeInput($_POST['status'] ?? 'draft');
    $status = strtolower($status); // Force lowercase to match DB enum and query

    // Slug generation
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $image = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            move_uploaded_file($_FILES['featured_image']['tmp_name'], '../../uploads/' . $image);
        }
    }

    if ($action === 'create') {
        $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, category, excerpt, content, featured_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $category, $excerpt, $content, $image, $status]);
    } elseif ($action === 'update' && $id) {
        if ($image) {
            $stmt = $db->prepare("UPDATE blog_posts SET title = ?, slug = ?, category = ?, excerpt = ?, content = ?, featured_image = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $category, $excerpt, $content, $image, $status, $id]);
        } else {
            $stmt = $db->prepare("UPDATE blog_posts SET title = ?, slug = ?, category = ?, excerpt = ?, content = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $category, $excerpt, $content, $status, $id]);
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
    }
    redirect('/alpha/blog');
}

// Fetch Posts
$posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog - SkillOwners Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .blog-card-img { height: 160px; object-fit: cover; border-radius: 0.75rem; }
    </style>
</head>
<body>
    <?php include 'alpha_sidebar.php'; ?>

    <main class="content">
        <div class="container-fluid p-0">
            <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="h3 fw-bold mb-1">Manage Blog Posts</h1>
                    <p class="text-muted small mb-0">Share your stories and agency updates.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPostModal">
                    <i class="fas fa-plus me-2"></i> Create New Post
                </button>
            </header>

            <div class="row g-4">
                <?php foreach($posts as $post): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="glass-card shadow-sm border h-100 overflow-hidden transition-all">
                        <div class="position-relative">
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" class="w-100 blog-card-img" alt="Post" style="height: 160px; object-fit: cover;">
                            <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-primary rounded-pill px-3 py-1 small"><?php echo htmlspecialchars($post['category']); ?></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="text-muted small mb-4 line-clamp-3"><?php echo htmlspecialchars(strip_tags($post['content'])); ?></p>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div class="small text-muted"><i class="far fa-calendar-alt me-1"></i> <?php echo date('M d', strtotime($post['created_at'])); ?></div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $post['id']; ?>"><i class="fas fa-edit text-primary"></i></button>
                                    <form method="POST" onsubmit="return confirm('Delete this post?')" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm"><i class="fas fa-trash text-danger"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
            <div class="modal fade" id="editModal-<?php echo $post['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form class="modal-content rounded-4 border-0" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                        <div class="modal-header border-0 bg-light">
                            <h5 class="modal-title fw-bold">Update Blog Post</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-muted">Post Title</label>
                                    <input type="text" name="title" class="form-control rounded-3" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Category</label>
                                    <input type="text" name="category" class="form-control rounded-3" value="<?php echo htmlspecialchars($post['category']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Article Content (HTML supported)</label>
                                    <textarea name="content" class="form-control rounded-3" rows="12" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Header Image (Optional)</label>
                                    <input type="file" name="featured_image" class="form-control rounded-3" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase">Status</label>
                                    <select name="status" class="form-select text-capitalize">
                                        <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Update Post</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Add Modal -->
    <div class="modal fade" id="addPostModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content rounded-4 border-0" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="modal-header border-0 bg-primary text-white">
                    <h5 class="modal-title fw-bold">Compose New Article</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold mb-1">Post Title</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="Enter a catchy title..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Category</label>
                            <input type="text" name="category" class="form-control rounded-3" placeholder="e.g. Technology" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1">Content</label>
                            <textarea name="content" class="form-control rounded-3" rows="10" placeholder="Write your insights here..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1">Feature Image</label>
                            <input type="file" name="featured_image" class="form-control rounded-3" accept="image/*" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft">Draft</option>
                                <option value="published" selected>Published</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Publish Post</button>
                </div>
            </form>
        </div>
    </div>

<?php include '../../views/partials/footer.php'; ?>
