<?php
require_once '../../config.php';

// Check if logged in specifically as the designated admin
$admin_email = ADMIN_LOGIN_EMAIL;
if (!isset($_SESSION['user_id']) || $_SESSION['user_email'] !== $admin_email) {
    redirect('/alpha');
}

$db = getDBConnection();

// Mark all as read when viewing the list
$db->prepare("UPDATE contact_submissions SET status = 'read' WHERE status = 'new'")->execute();

// Fetch Contacts
$contacts = $db->query("SELECT * FROM contact_submissions ORDER BY created_at DESC")->fetchAll();
?>
<?php
include '../../views/partials/header.php';
?>
<?php include 'alpha_sidebar.php'; ?>

    <main class="content">
        <div class="container-fluid p-0">
            <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="h3 fw-bold mb-1">Contact Messages</h1>
                    <p class="text-muted small mb-0">Respond to general inquiries and service leads.</p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 w-fit">Total Messages: <?php echo count($contacts); ?></span>
            </header>

            <div class="glass-card overflow-hidden shadow-sm border">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Sender</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Service</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Status</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Date</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($contacts as $contact): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold small"><?php echo htmlspecialchars($contact['name']); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($contact['email']); ?></div>
                            </td>
                            <td class="px-4 py-3 small text-muted"><?php echo htmlspecialchars($contact['service_interested'] ?: 'General'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($contact['status'] === 'new'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1" style="font-size: 0.65rem;">NEW</span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size: 0.65rem;">READ</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 small text-muted"><?php echo date('M d, Y', strtotime($contact['created_at'])); ?></td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-light btn-sm rounded-3 border" data-bs-toggle="modal" data-bs-target="#viewModal-<?php echo $contact['id']; ?>">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; if(empty($contacts)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No messages found.</td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals Section (Outside main content for performance) -->
    <?php foreach($contacts as $contact): ?>
    <!-- View Modal -->
    <div class="modal fade" id="viewModal-<?php echo $contact['id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Sender</label>
                            <p class="fw-bold mb-0"><?php echo htmlspecialchars($contact['name']); ?></p>
                            <p class="text-primary small"><?php echo htmlspecialchars($contact['email']); ?></p>
                            <?php if(!empty($contact['phone'])): ?>
                                <p class="text-muted small"><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($contact['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Date Received</label>
                            <p class="mb-0 small"><?php echo date('F j, Y, g:i a', strtotime($contact['created_at'])); ?></p>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Service Interested</label>
                            <h6 class="fw-bold"><?php echo htmlspecialchars($contact['service_interested'] ?: 'General Inquiry'); ?></h6>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Message Content</label>
                            <div class="bg-light p-4 rounded-4 small leading-relaxed" style="white-space: pre-line;">
                                <?php echo htmlspecialchars($contact['message']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
