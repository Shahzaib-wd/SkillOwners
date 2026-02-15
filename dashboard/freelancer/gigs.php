<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Gig.php';
$userId = $_SESSION['user_id'];
$gigModel = new Gig();
$gigs = $gigModel->findByUserId($userId);
$gigCount = count($gigs);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">My Gigs</h1>
                <p class="text-muted">Manage your service offerings (<?php echo $gigCount; ?>/<?php echo MAX_GIGS; ?>)</p>
            </div>
            <?php if ($gigCount < MAX_GIGS): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/create_gig.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Gig
                </a>
            <?php endif; ?>
        </div>

        <div class="dashboard-card">
            <?php if (empty($gigs)): ?>
                <div class="text-center py-5">
                    <div class="stat-icon primary mb-3 mx-auto">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <h3>No Gigs Yet</h3>
                    <p class="text-muted mb-4">Start selling your skills by creating your first gig!</p>
                    <a href="<?php echo SITE_URL; ?>/dashboard/create_gig.php" class="btn btn-primary">
                        Create Your First Gig
                    </a>
                </div>
            <?php else: ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Gig</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gigs as $gig): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($gig['image'])): ?>
                                                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $gig['image']; ?>" alt="" style="width: 48px; height: 32px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <div style="width: 48px; height: 32px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="font-weight-600"><?php echo htmlspecialchars($gig['title']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($gig['category']); ?></td>
                                    <td><span class="font-weight-700">$<?php echo number_format($gig['price'], 2); ?></span></td>
                                    <td>
                                        <?php if ($gig['is_active']): ?>
                                            <span class="badge-success user-role">Active</span>
                                        <?php else: ?>
                                            <span class="badge-warning user-role">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="<?php echo SITE_URL; ?>/gig?id=<?php echo $gig['id']; ?>" class="btn btn-sm btn-outline" title="View Public Page">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/dashboard/edit_gig.php?id=<?php echo $gig['id']; ?>" class="btn btn-sm btn-outline" title="Edit Gig">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" title="Delete Gig" onclick="confirmDelete(<?php echo $gig['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this gig? This action cannot be undone.')) {
        window.location.href = '<?php echo SITE_URL; ?>/dashboard/delete_gig?id=' + id;
    }
}
</script>

<?php include '../../views/partials/footer.php'; ?>
