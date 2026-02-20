<?php
require_once '../../config.php';

// Check if logged in specifically as the designated admin
$admin_email = ADMIN_LOGIN_EMAIL;
if (!isset($_SESSION['user_id']) || $_SESSION['user_email'] !== $admin_email) {
    redirect('/alpha');
}

$db = getDBConnection();

// Mark all as read (Contacted) when viewing the list
$db->prepare("UPDATE quote_requests SET status = 'Contacted' WHERE status = 'New'")->execute();

// Fetch Quotes
$quotes = $db->query("SELECT * FROM quote_requests ORDER BY created_at DESC")->fetchAll();
?>
<?php
include '../../views/partials/header.php';
?>
<?php include 'alpha_sidebar.php'; ?>

    <main class="content">
        <div class="container-fluid p-0">
            <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
                <div>
                    <h1 class="h3 fw-bold mb-1">Project Quotes</h1>
                    <p class="text-muted small mb-0">Review and respond to quote requests efficiently.</p>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 w-fit">Total Requests: <?php echo count($quotes); ?></span>
            </header>

            <div class="glass-card overflow-hidden shadow-sm border">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Client</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Project</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Budget</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Status</th>
                                <th class="px-4 py-3 small text-uppercase fw-bold text-muted text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ... -->
                        <?php foreach($quotes as $quote): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold small"><?php echo htmlspecialchars($quote['full_name']); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($quote['company_name'] ?: 'No Company'); ?></div>
                            </td>
                            <td class="px-4 py-3 small fw-500"><?php echo htmlspecialchars($quote['service_type']); ?></td>
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;"><?php echo htmlspecialchars($quote['budget_range']); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1" style="font-size: 0.65rem;"><?php echo strtoupper($quote['status']); ?></span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quoteModal-<?php echo $quote['id']; ?>">
                                    Details
                                </button>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="quoteModal-<?php echo $quote['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 overflow-hidden border-0">
                                    <div class="modal-header bg-dark text-white border-0 py-3">
                                        <h5 class="modal-title h6 fw-bold mb-0">Quote Request Breakdown</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-6 border-end">
                                                <h6 class="small text-muted text-uppercase fw-bold mb-3" style="letter-spacing: 1px;">Contact Information</h6>
                                                <div class="mb-3">
                                                    <label class="d-block text-muted small">Full Name</label>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($quote['full_name']); ?></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="d-block text-muted small">Email Address</label>
                                                    <span class="fw-bold text-primary"><?php echo htmlspecialchars($quote['email']); ?></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="d-block text-muted small">Company</label>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($quote['company_name'] ?: 'Not specified'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="small text-muted text-uppercase fw-bold mb-3" style="letter-spacing: 1px;">Project Scope</h6>
                                                <div class="mb-3">
                                                    <label class="d-block text-muted small">Service Type</label>
                                                    <span class="badge bg-primary px-3 py-2"><?php echo htmlspecialchars($quote['service_type']); ?></span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="d-block text-muted small">Target Budget</label>
                                                    <span class="fw-bold h5 text-success mb-0"><?php echo htmlspecialchars($quote['budget_range']); ?></span>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="d-block text-muted small">Submission Date</label>
                                                    <span class="text-muted small"><?php echo date('F j, Y, g:i a', strtotime($quote['created_at'])); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4 pt-4 border-top">
                                                <h6 class="small text-muted text-uppercase fw-bold mb-3" style="letter-spacing: 1px;">Project Description</h6>
                                                <div class="p-3 bg-light rounded-3 small leading-relaxed" style="white-space: pre-line;">
                                                    <?php echo htmlspecialchars($quote['project_description']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                        <a href="mailto:<?php echo $quote['email']; ?>" class="btn btn-primary rounded-pill px-4">Reply via Email</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
