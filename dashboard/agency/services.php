<?php
require_once '../../config.php';
requireLogin();

if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole());
}

require_once '../../models/Gig.php';
require_once '../../models/AgencyMember.php';

$agencyId = $_SESSION['user_id'];
$gigModel = new Gig();
$memberModel = new AgencyMember();

// Handle actions (approve, reject, remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $gigId = (int)($_POST['gig_id'] ?? 0);
    
    if ($gigId > 0) {
        switch ($action) {
            case 'approve':
                $gigModel->updateAgencyGigStatus($gigId, $agencyId, 'approved');
                showSuccess('Service approved and now visible in agency catalog.');
                break;
            case 'reject':
                $gigModel->removeGigFromAgency($gigId, $agencyId);
                showSuccess('Service request rejected.');
                break;
            case 'remove':
                $gigModel->removeGigFromAgency($gigId, $agencyId);
                showSuccess('Service removed from agency.');
                break;
        }
    }
    redirect('/dashboard/agency/services');
}

// Get all gigs (no status filter = show all for management)
$allAgencyGigs = $gigModel->getAgencyGigs($agencyId);
$pendingGigs = array_filter($allAgencyGigs, fn($g) => $g['contribution_status'] === 'pending');
$approvedGigs = array_filter($allAgencyGigs, fn($g) => $g['contribution_status'] === 'approved');
$pendingCount = count($pendingGigs);
$approvedCount = count($approvedGigs);

include '../../views/partials/header.php';
?>

<div class="dashboard-layout">
    <?php include '../../views/partials/sidebar.php'; ?>
    
    <main class="dashboard-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Agency Services</h1>
                <p class="text-muted"><?php echo $approvedCount; ?> active service<?php echo $approvedCount !== 1 ? 's' : ''; ?><?php if ($pendingCount > 0): ?> · <span style="color: #f59e0b; font-weight: 600;"><?php echo $pendingCount; ?> pending approval</span><?php endif; ?></p>
            </div>
        </div>

        <!-- Pending Approvals Section -->
        <?php if (!empty($pendingGigs)): ?>
            <div class="pending-section mb-5">
                <h2 class="h5 mb-3 d-flex align-items-center gap-2">
                    <span class="pending-dot"></span> Pending Approval
                </h2>
                <div class="agency-services-grid">
                    <?php foreach ($pendingGigs as $gig): ?>
                        <div class="agency-service-card pending-card">
                            <div class="service-card-image">
                                <?php if (!empty($gig['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($gig['image']); ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>">
                                <?php else: ?>
                                    <div class="service-card-placeholder">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="service-card-badge"><?php echo htmlspecialchars($gig['category']); ?></span>
                                <span class="status-badge status-pending">Pending</span>
                            </div>
                            <div class="service-card-body">
                                <h3 class="service-card-title">
                                    <a href="<?php echo SITE_URL; ?>/gig?id=<?php echo $gig['id']; ?>" target="_blank"><?php echo htmlspecialchars($gig['title']); ?></a>
                                </h3>
                                <div class="service-card-contributor">
                                    <div class="contributor-avatar">
                                        <?php if (!empty($gig['seller_image'])): ?>
                                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($gig['seller_image']); ?>" alt="">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($gig['full_name'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="contributor-name"><?php echo htmlspecialchars($gig['full_name']); ?></span>
                                        <span class="contributor-label">Requesting to contribute</span>
                                    </div>
                                </div>
                                <div class="service-card-price mb-3">
                                    <span class="price-label">Starting at</span>
                                    <span class="price-value">$<?php echo number_format($gig['price'], 2); ?></span>
                                </div>
                                <div class="approval-actions">
                                    <form method="POST" style="display: inline-flex; gap: 8px; width: 100%;">
                                        <input type="hidden" name="gig_id" value="<?php echo $gig['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-approve flex-1">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-reject flex-1" onclick="return confirm('Reject this service request?');">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Active Services Section -->
        <h2 class="h5 mb-3"><i class="fas fa-check-circle text-success"></i> Active Services</h2>
        <?php if (empty($approvedGigs)): ?>
            <div class="dashboard-card text-center py-5">
                <div class="agency-services-empty">
                    <div class="stat-icon primary mb-3 mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3 class="h4 font-weight-700 mb-2">No Active Services</h3>
                    <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                        <?php if ($pendingCount > 0): ?>
                            You have <?php echo $pendingCount; ?> pending request<?php echo $pendingCount > 1 ? 's' : ''; ?> waiting for your approval above.
                        <?php else: ?>
                            Invite freelancers to your team — they can each contribute 1 gig to showcase here.
                        <?php endif; ?>
                    </p>
                    <?php if ($pendingCount === 0): ?>
                        <a href="<?php echo SITE_URL; ?>/dashboard/agency/team?action=invite" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Invite Team Members
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="agency-services-grid">
                <?php foreach ($approvedGigs as $gig): ?>
                    <div class="agency-service-card">
                        <div class="service-card-image">
                            <?php if (!empty($gig['image'])): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($gig['image']); ?>" alt="<?php echo htmlspecialchars($gig['title']); ?>">
                            <?php else: ?>
                                <div class="service-card-placeholder">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            <?php endif; ?>
                            <span class="service-card-badge"><?php echo htmlspecialchars($gig['category']); ?></span>
                        </div>
                        <div class="service-card-body">
                            <h3 class="service-card-title">
                                <a href="<?php echo SITE_URL; ?>/gig?id=<?php echo $gig['id']; ?>"><?php echo htmlspecialchars($gig['title']); ?></a>
                            </h3>
                            <div class="service-card-contributor">
                                <div class="contributor-avatar">
                                    <?php if (!empty($gig['seller_image'])): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($gig['seller_image']); ?>" alt="">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($gig['full_name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="contributor-name"><?php echo htmlspecialchars($gig['full_name']); ?></span>
                                    <span class="contributor-label">Team Member</span>
                                </div>
                            </div>
                            <div class="service-card-meta">
                                <div class="service-card-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo $gig['avg_rating'] > 0 ? round($gig['avg_rating'], 1) : 'New'; ?></span>
                                    <span class="text-muted">(<?php echo $gig['review_count']; ?>)</span>
                                </div>
                                <div class="service-card-delivery">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo $gig['delivery_time']; ?>d</span>
                                </div>
                            </div>
                            <div class="service-card-footer">
                                <div class="service-card-price">
                                    <span class="price-label">Starting at</span>
                                    <span class="price-value">$<?php echo number_format($gig['price'], 2); ?></span>
                                </div>
                                <form method="POST" onsubmit="return confirm('Remove this service from the agency?');" style="margin: 0;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="gig_id" value="<?php echo $gig['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from agency">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.agency-services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.agency-service-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.agency-service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
}

.pending-card {
    border: 2px solid #fbbf24;
    box-shadow: 0 0 0 1px rgba(251, 191, 36, 0.1);
}

.service-card-image {
    position: relative;
    height: 180px;
    background: #f1f5f9;
    overflow: hidden;
}

.service-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.agency-service-card:hover .service-card-image img {
    transform: scale(1.05);
}

.service-card-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    color: #6366f1;
    font-size: 2.5rem;
}

.service-card-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(4px);
}

.status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.pending-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #f59e0b;
    display: inline-block;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.service-card-body {
    padding: 1.25rem;
}

.service-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.service-card-title a {
    color: var(--foreground);
    text-decoration: none;
}

.service-card-title a:hover {
    color: var(--primary);
}

.service-card-contributor {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}

.contributor-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
    overflow: hidden;
    flex-shrink: 0;
}

.contributor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.contributor-name {
    display: block;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--foreground);
    line-height: 1.2;
}

.contributor-label {
    display: block;
    font-size: 0.7rem;
    color: #94a3b8;
    font-weight: 500;
}

.service-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
}

.service-card-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 600;
}

.service-card-rating i {
    color: #f59e0b;
}

.service-card-delivery {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #64748b;
}

.service-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
}

.service-card-price {
    display: flex;
    flex-direction: column;
}

.price-label {
    font-size: 0.7rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 600;
}

.price-value {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--primary);
}

.btn-outline-danger {
    background: transparent;
    border: 1px solid #fca5a5;
    color: #ef4444;
    padding: 6px 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.8rem;
}

.btn-outline-danger:hover {
    background: #fef2f2;
    border-color: #ef4444;
}

.approval-actions {
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
}

.btn-approve {
    background: #10b981;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
}

.btn-approve:hover {
    background: #059669;
}

.btn-reject {
    background: transparent;
    border: 1px solid #fca5a5;
    color: #ef4444;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
}

.btn-reject:hover {
    background: #fef2f2;
}

.flex-1 {
    flex: 1;
}
</style>

<?php include '../../views/partials/footer.php'; ?>
