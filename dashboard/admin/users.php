<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'admin') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/User.php';
$userModel = new User();
$users = $userModel->findAll();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">User Management</h1>
            <p class="text-muted">Manage system users and their access levels</p>
        </div>

        <div class="dashboard-card">
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <span class="font-weight-600"><?php echo htmlspecialchars($user['full_name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php
                                    $roleClass = 'badge-freelancer';
                                    if ($user['role'] === 'admin') $roleClass = 'badge-agency';
                                    elseif ($user['role'] === 'agency') $roleClass = 'badge-primary';
                                    ?>
                                    <span class="<?php echo $roleClass; ?> user-role">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-success user-role">Active</span>
                                </td>
                                <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span></td>
                                <td class="text-right">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-outline" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Suspend User">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
