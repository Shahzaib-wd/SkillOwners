<?php
require_once '../../config.php';
require_once '../../models/ContactMessage.php';

requireLogin();

$current_page = 'messages';
$contactModel = new ContactMessage();

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'read') {
        $contactModel->updateStatus($id, 'read');
        showSuccess("Message marked as read.");
    } elseif ($_GET['action'] === 'delete') {
        $contactModel->delete($id);
        showSuccess("Message deleted successfully.");
    }
    redirect('/dashboard/alpha/messages');
}

$messages = $contactModel->findAll();

include '../../views/partials/header.php';
?>

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: 1px solid rgba(255, 255, 255, 0.2);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    body {
        background-color: #f3f4f6;
    }

    .dashboard-layout {
        display: flex;
        min-height: calc(100vh - 70px);
    }

    .dashboard-content {
        flex: 1;
        padding: 2rem;
        overflow-x: hidden;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .card-pro {
        background: var(--glass-bg);
        border: var(--glass-border);
        box-shadow: var(--glass-shadow);
        border-radius: 1rem;
        overflow: hidden;
    }

    .table thead th {
        background: #f9fafb;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .badge-status {
        padding: 0.35rem 0.75rem;
        border-radius: 50rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-read { background: #f3f4f6; color: #4b5563; }

    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .modal-glass {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1.5rem;
    }
</style>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header">
            <h1 class="page-title">Contact Messages</h1>
            <p class="text-muted">Review and manage inquiries from the contact form.</p>
        </div>

        <?php if ($success = getSuccess()): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card-pro">
            <?php if (empty($messages)): ?>
                <div class="text-center py-5">
                    <div class="mb-3 text-muted" style="font-size: 3rem;">
                        <i class="far fa-envelope-open"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Inbox is empty</h3>
                    <p class="text-muted px-4">New inquiries from the contact form will appear here.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-700 text-dark"><?php echo htmlspecialchars($msg['name']); ?></div>
                                        <div class="small text-muted font-weight-500"><?php echo htmlspecialchars($msg['email']); ?></div>
                                    </td>
                                    <td>
                                        <div class="text-dark font-weight-600"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                    </td>
                                    <td class="small text-muted font-weight-500">
                                        <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                                    </td>
                                    <td>
                                        <?php if ($msg['status'] === 'pending'): ?>
                                            <span class="badge-status badge-pending">New Message</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-read">Opened</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn-action btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#msgModal<?php echo $msg['id']; ?>"
                                                    title="View Message">
                                                <i class="fas fa-eye fa-sm"></i>
                                            </button>
                                            
                                            <?php if ($msg['status'] === 'pending'): ?>
                                                <a href="?action=read&id=<?php echo $msg['id']; ?>" 
                                                   class="btn-action btn btn-outline-success" 
                                                   title="Mark as Read">
                                                    <i class="fas fa-check fa-sm"></i>
                                                </a>
                                            <?php endif; ?>

                                            <a href="?action=delete&id=<?php echo $msg['id']; ?>" 
                                               class="btn-action btn btn-outline-danger" 
                                               onclick="return confirm('Permanently delete this message?')"
                                               title="Delete">
                                                <i class="fas fa-trash fa-sm"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Message Detail Modal -->
                                <div class="modal fade" id="msgModal<?php echo $msg['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content modal-glass border-0 overflow-hidden shadow-lg">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title font-weight-800">Inquiry Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-4">
                                                    <div class="d-flex align-items-center gap-3 mb-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700;">
                                                            <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-700 h6 mb-0"><?php echo htmlspecialchars($msg['name']); ?></div>
                                                            <div class="small text-muted"><?php echo htmlspecialchars($msg['email']); ?></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="small text-muted text-uppercase tracking-wider font-weight-800 mb-1 d-block">Subject</label>
                                                    <div class="font-weight-700 h6 text-dark"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="small text-muted text-uppercase tracking-wider font-weight-800 mb-1 d-block">Message</label>
                                                    <div class="bg-light p-3 rounded-4" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6; color: #374151;"><?php echo htmlspecialchars($msg['message']); ?></div>
                                                </div>

                                                <div class="d-grid">
                                                    <a href="mailto:<?php echo $msg['email']; ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" 
                                                       class="btn btn-primary btn-lg rounded-3">
                                                        <i class="fas fa-reply me-2"></i> Reply to Sender
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../../views/partials/footer.php'; ?>
