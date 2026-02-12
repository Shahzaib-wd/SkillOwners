<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'freelancer') {
    redirect('/dashboard/' . getUserRole() . '.php');
}

require_once '../../models/Project.php';
$userId = $_SESSION['user_id'];
$projectModel = new Project();
$projects = $projectModel->findByUserId($userId);
$projectCount = count($projects);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Portfolio Projects</h1>
                <p class="text-muted">Showcase your best work to potential clients (<?php echo $projectCount; ?>/<?php echo MAX_PROJECTS; ?>)</p>
            </div>
            <?php if ($projectCount < MAX_PROJECTS): ?>
                <a href="<?php echo SITE_URL; ?>/dashboard/create_project.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Project
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($projects)): ?>
            <div class="dashboard-card text-center py-5">
                <div class="stat-icon success mb-3 mx-auto">
                    <i class="fas fa-folder-open fa-2x"></i>
                </div>
                <h3>Your Portfolio is Empty</h3>
                <p class="text-muted mb-4">Add projects to demonstrate your expertise and attract more clients!</p>
                <a href="<?php echo SITE_URL; ?>/dashboard/create_project.php" class="btn btn-primary">
                    Add Your First Project
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="dashboard-card h-100 p-0 overflow-hidden">
                            <div style="height: 180px; position: relative; background: #f1f5f9;">
                                <?php if (!empty($project['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $project['image']; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                                <div style="position: absolute; top: 12px; right: 12px; display: flex; gap: 8px;">
                                    <a href="<?php echo SITE_URL; ?>/dashboard/edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-white btn-sm shadow-sm" style="background: white; border: none; padding: 4px 8px;">
                                        <i class="fas fa-edit text-primary"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $project['id']; ?>)" class="btn btn-white btn-sm shadow-sm" style="background: white; border: none; padding: 4px 8px;">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="h6 font-weight-700 mb-2"><?php echo htmlspecialchars($project['title']); ?></h3>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars(substr($project['description'], 0, 80)); ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this project from your portfolio?')) {
        window.location.href = '<?php echo SITE_URL; ?>/dashboard/delete_project.php?id=' + id;
    }
}
</script>

<?php include '../../views/partials/footer.php'; ?>
