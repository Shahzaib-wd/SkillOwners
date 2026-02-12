<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'admin') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Gig.php';
$gigModel = new Gig();
$gigs = $gigModel->findAll();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Gig Moderation</h1>
            <p class="text-muted">Review and manage platform service offerings</p>
        </div>

        <div class="dashboard-card">
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Gig Title</th>
                            <th>Freelancer</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gigs as $gig): ?>
                            <tr>
                                <td><span class="font-weight-600"><?php echo htmlspecialchars($gig['title']); ?></span></td>
                                <td><?php echo htmlspecialchars($gig['seller_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($gig['category']); ?></td>
                                <td><span class="font-weight-700">$<?php echo number_format($gig['price'], 2); ?></span></td>
                                <td>
                                    <?php if ($gig['is_active']): ?>
                                        <span class="badge-success user-role">Live</span>
                                    <?php else: ?>
                                        <span class="badge-warning user-role">Off</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?php echo SITE_URL; ?>/gig.php?id=<?php echo $gig['id']; ?>" class="btn btn-sm btn-outline" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Disable Gig">
                                            <i class="fas fa-toggle-off"></i>
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
