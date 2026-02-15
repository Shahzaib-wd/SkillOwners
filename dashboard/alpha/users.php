<?php
require_once '../../config.php';
requireLogin();

// if (getUserRole() !== 'admin') {
//     redirect('/dashboard/' . getUserRole() . '.php');
// }

require_once '../../models/User.php';
$userModel = new User();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();
    
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'update_status') {
            $id = (int)$_POST['id'];
            $isActive = (int)$_POST['is_active'];
            if ($userModel->updateStatus($id, $isActive)) {
                showSuccess('User status updated successfully.');
            } else {
                showError('Failed to update status.');
            }
        } elseif ($action === 'update_user') {
            $id = (int)$_POST['id'];
            $data = [
                'full_name' => sanitizeInput($_POST['full_name']),
                'email' => sanitizeInput($_POST['email']),
                'role' => sanitizeInput($_POST['role'])
            ];
            
            // Basic validation
            if (empty($data['full_name']) || empty($data['email'])) {
                showError('Name and Email are required.');
            } else {
                if ($userModel->update($id, $data)) {
                    showSuccess('User details updated successfully.');
                } else {
                    showError('Failed to update user details.');
                }
            }
        } elseif ($action === 'delete_user') {
             $id = (int)$_POST['id'];
             // Prevent deleting self
             if ($id == $_SESSION['user_id']) {
                 showError("You cannot delete yourself.");
             } else {
                 if ($userModel->delete($id)) {
                     showSuccess('User deleted successfully.');
                 } else {
                     showError('Failed to delete user.');
                 }
             }
        }
    }
    // Refresh to clear post
    redirect('/dashboard/alpha/users');
}

$users = $userModel->findAll();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">User Management</h1>
            <p class="text-muted">Manage system users, roles, and access.</p>
        </div>

        <?php if ($success = getSuccess()): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error = getError()): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card-pro">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Joined</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4">
                                    <?php 
                                    $badges = [
                                        'freelancer' => 'bg-info bg-opacity-10 text-info', 
                                        'agency' => 'bg-primary bg-opacity-10 text-primary', 
                                        'buyer' => 'bg-warning bg-opacity-10 text-warning',
                                        'admin' => 'bg-danger bg-opacity-10 text-danger'
                                    ];
                                    $class = $badges[$user['role']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $class; ?>"><?php echo ucfirst($user['role']); ?></span>
                                </td>
                                <td class="px-4">
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 text-muted small">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="px-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick='editUser(<?php echo json_encode($user); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <?php if ($user['is_active']): ?>
                                                <input type="hidden" name="is_active" value="0">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Suspend User" onclick="return confirm('Suspend this user?');">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="is_active" value="1">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activate User">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        
                                        <form method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to DELETE this user? This action cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" id="edit_user_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="freelancer">Freelancer</option>
                            <option value="agency">Agency</option>
                            <option value="buyer">Buyer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
</script>

<?php include '../../views/partials/footer.php'; ?>
