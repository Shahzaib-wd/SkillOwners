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
    $problem = sanitizeInput($_POST['problem'] ?? '');
    $solution = sanitizeInput($_POST['solution'] ?? '');
    $result = sanitizeInput($_POST['result'] ?? '');
    $url = sanitizeInput($_POST['website_url'] ?? '');

    $image = null;
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $image = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            move_uploaded_file($_FILES['main_image']['tmp_name'], '../../uploads/' . $image);
        }
    }

    if ($action === 'create') {
        $stmt = $db->prepare("INSERT INTO portfolio_projects (title, category, problem, solution, result, main_image, website_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $problem, $solution, $result, $image, $url]);
    } elseif ($action === 'update' && $id) {
        if ($image) {
            $stmt = $db->prepare("UPDATE portfolio_projects SET title = ?, category = ?, problem = ?, solution = ?, result = ?, main_image = ?, website_url = ? WHERE id = ?");
            $stmt->execute([$title, $category, $problem, $solution, $result, $image, $url, $id]);
        } else {
            $stmt = $db->prepare("UPDATE portfolio_projects SET title = ?, category = ?, problem = ?, solution = ?, result = ?, website_url = ? WHERE id = ?");
            $stmt->execute([$title, $category, $problem, $solution, $result, $url, $id]);
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM portfolio_projects WHERE id = ?");
        $stmt->execute([$id]);
    }
    redirect('/alpha/portfolio');
}

// Fetch Projects
$projects = $db->query("SELECT * FROM portfolio_projects ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php include 'alpha_sidebar.php'; ?>

    <main class="content">
        <div class="container-fluid p-0">
            <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="h3 fw-bold mb-1">Portfolio Showcase</h1>
                    <p class="text-muted small mb-0">Manage your project gallery and case studies.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class="fas fa-plus me-2"></i> New Project
                </button>
            </header>

            <div class="row g-4">
                <?php foreach($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card shadow-sm border h-100 overflow-hidden transition-all">
                        <div class="position-relative">
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($project['main_image']); ?>" class="w-100 project-img" alt="Project" style="height: 180px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge bg-dark bg-opacity-75 rounded-pill px-3 py-2 small"><?php echo htmlspecialchars($project['category']); ?></span>
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column">
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <p class="text-muted small mb-4 line-clamp-2"><?php echo htmlspecialchars($project['problem']); ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $project['id']; ?>"><i class="fas fa-edit me-1"></i> Edit</button>
                                <form method="POST" onsubmit="return confirm('Archive this project?')" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="btn btn-link text-danger p-0"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <!-- ... -->
            <div class="modal fade" id="editModal-<?php echo $project['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content rounded-4 border-0" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <div class="modal-header border-0 bg-light">
                            <h5 class="modal-title fw-bold">Edit Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Project Title</label>
                                <input type="text" name="title" class="form-control rounded-3" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Category</label>
                                <select name="category" class="form-select rounded-3" required>
                                    <option value="Web Development" <?php echo $project['category'] == 'Web Development' ? 'selected' : ''; ?>>Web Development</option>
                                    <option value="Mobile App" <?php echo $project['category'] == 'Mobile App' ? 'selected' : ''; ?>>Mobile App</option>
                                    <option value="UI/UX Design" <?php echo $project['category'] == 'UI/UX Design' ? 'selected' : ''; ?>>UI/UX Design</option>
                                    <option value="Cloud Solutions" <?php echo $project['category'] == 'Cloud Solutions' ? 'selected' : ''; ?>>Cloud Solutions</option>
                                    <option value="AI & Data" <?php echo $project['category'] == 'AI & Data' ? 'selected' : ''; ?>>AI & Data</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Description</label>
                                <textarea name="description" class="form-control rounded-3" rows="4" required><?php echo htmlspecialchars($project['description']); ?></textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">Project Image (Optional)</label>
                                <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
