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
    $description = sanitizeInput($_POST['description'] ?? '');
    $icon = sanitizeInput($_POST['icon'] ?? '');
    $order = intval($_POST['order_index'] ?? 0);

    if ($action === 'create') {
        $stmt = $db->prepare("INSERT INTO services (title, description, icon, order_index) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $icon, $order]);
    } elseif ($action === 'update' && $id) {
        $stmt = $db->prepare("UPDATE services SET title = ?, description = ?, icon = ?, order_index = ? WHERE id = ?");
        $stmt->execute([$title, $description, $icon, $order, $id]);
    } elseif ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
    }
    redirect('/alpha/services');
}

// Handle GET delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    redirect('/alpha/services');
}

// Fetch Services
$services = $db->query("SELECT * FROM services ORDER BY order_index ASC")->fetchAll();
?>
<?php
include '../../views/partials/header.php';
?>
<?php include 'alpha_sidebar.php'; ?>

    <main class="content">
        <div class="container-fluid p-0">
            <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="h3 fw-bold mb-1">Agency Services</h1>
                    <p class="text-muted small mb-0">Manage your service offerings and visual identity.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="fas fa-plus me-2"></i> New Service
                </button>
            </header>

            <div class="row g-4">
                <?php foreach($services as $service): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="glass-card p-4 h-100 shadow-sm border transition-all position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary p-3 rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="<?php echo htmlspecialchars($service['icon']); ?> fa-lg"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link link-secondary p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $service['id']; ?>"><i class="fas fa-edit me-2 text-muted"></i> Edit Details</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 text-danger" href="?delete=<?php echo $service['id']; ?>" onclick="return confirm('Archive this service?')"><i class="fas fa-trash-alt me-2"></i> Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($service['title']); ?></h5>
                        <p class="text-muted small mb-4 leading-relaxed"><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="mt-auto pt-3 d-flex align-items-center justify-content-between border-top border-light">
                            <span class="badge bg-light text-muted fw-normal" style="font-size: 0.7rem;">Order: <?php echo $service['order_index']; ?></span>
                            <div class="text-primary small fw-bold">Active</div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <!-- ... same as before ... -->
            <div class="modal fade" id="editModal-<?php echo $service['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content rounded-4 border-0">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                            <div class="modal-header border-0 bg-light">
                                <h5 class="modal-title fw-bold">Update Service</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Title</label>
                                    <input type="text" name="title" class="form-control rounded-3" value="<?php echo htmlspecialchars($service['title']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Icon (FontAwesome class)</label>
                                    <input type="text" name="icon" class="form-control rounded-3" value="<?php echo htmlspecialchars($service['icon']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Description</label>
                                    <textarea name="description" class="form-control rounded-3" rows="4" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Sort Order</label>
                                    <input type="number" name="order_index" class="form-control rounded-3" value="<?php echo $service['order_index']; ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header border-0 bg-primary text-white">
                        <h5 class="modal-title fw-bold">Add New Service</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Service Name</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Web Development" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Icon Class</label>
                            <input type="text" name="icon" class="form-control rounded-3" placeholder="fas fa-code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Description</label>
                            <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Briefly describe what this service involves..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include '../../views/partials/footer.php'; ?>
