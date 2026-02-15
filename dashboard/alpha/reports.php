<?php
require_once '../../config.php';
requireLogin();

// if (getUserRole() !== 'admin') {
//     redirect('/dashboard/' . getUserRole() . '.php');
// }

require_once '../../models/Report.php';
require_once '../../models/User.php';

$reportModel = new Report();
$userModel = new User();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();
    
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $reportId = (int)$_POST['report_id'];
        
        if ($action === 'dismiss') {
            if ($reportModel->updateStatus($reportId, 'resolved')) {
                showSuccess('Report dismissed.');
            } else {
                showError('Failed to update report.');
            }
        } elseif ($action === 'ban_user') {
            $userId = (int)$_POST['user_id'];
            if ($userModel->updateStatus($userId, 0)) {
                $reportModel->updateStatus($reportId, 'resolved');
                showSuccess('User banned and report resolved.');
            } else {
                showError('Failed to ban user.');
            }
        }
    }
    redirect('/dashboard/alpha/reports');
}

$reports = $reportModel->getAllReports();

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Abuse Reports</h1>
            <p class="text-muted">Review and act on user reports</p>
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
                            <th class="px-4 py-3">Reported By</th>
                            <th class="px-4 py-3">Reported User</th>
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                    <p>No reports found. Good job!</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td class="px-4">
                                        <span class="fw-bold"><?php echo htmlspecialchars($report['reporter_name']); ?></span>
                                    </td>
                                    <td class="px-4 text-danger fw-bold">
                                        <?php echo htmlspecialchars($report['reported_name']); ?>
                                    </td>
                                    <td class="px-4">
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($report['reason']); ?></span>
                                    </td>
                                    <td class="px-4 text-muted small">
                                        <?php echo date('M d, Y', strtotime($report['created_at'])); ?>
                                    </td>
                                    <td class="px-4">
                                        <?php if ($report['status'] === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Resolved</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 text-end">
                                        <?php if ($report['status'] === 'pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="dismiss">
                                                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Dismiss / Resolve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="d-inline ms-1" onsubmit="return confirm('Ban this user?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="ban_user">
                                                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $report['reported_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Ban User">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">No actions</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
