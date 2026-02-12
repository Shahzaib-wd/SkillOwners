<?php
require_once 'config.php';
requireLogin();

include 'views/partials/header.php';
?>

<style>
.inbox-page {
    padding: 5rem 0 2rem;
    min-height: 100vh;
}
.inbox-page-header {
    margin-bottom: 2rem;
}
.inbox-page-header h1 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.inbox-page-header .unread-badge {
    background: var(--primary);
    color: var(--primary-foreground);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
}
.inbox-container {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    min-height: 500px;
}
.inbox-actions {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.search-box {
    flex: 1;
    max-width: 400px;
}
.conversation-list-wrapper {
    padding: 0;
}
/* Override inbox section styles for full page */
.inbox-section {
    background: transparent;
    border: none;
    padding: 0;
    margin: 0;
}
.inbox-header {
    display: none; /* Hide the section header since we have page header */
}
</style>

<div class="inbox-page">
    <div class="container">
        <div class="inbox-page-header">
            <h1>
                <i class="fas fa-inbox"></i> 
                Messages
                <span class="unread-badge" id="pageUnreadBadge" style="display: none;">0</span>
            </h1>
            <p class="text-muted">All your conversations in one place</p>
        </div>

        <div class="inbox-container">
            
            <div class="conversation-list-wrapper">
                <!-- Inbox component -->
                <?php include 'views/partials/inbox.php'; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for page to load
document.addEventListener('DOMContentLoaded', function() {
    // Update page unread badge
    window.updatePageUnreadBadge = function() {
        const badge = document.getElementById('pageUnreadBadge');
        const inboxBadge = document.getElementById('inboxUnreadBadge');
        
        if (badge && inboxBadge) {
            const count = parseInt(inboxBadge.textContent) || 0;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    };
    
    // Intercept renderInbox to update badge
    setTimeout(() => {
        if (typeof renderInbox !== 'undefined') {
            const originalRenderInbox = renderInbox;
            window.renderInbox = function(conversations) {
                originalRenderInbox(conversations);
                updatePageUnreadBadge();
            };
        }
        
        // Intercept updateUnreadBadge
        if (typeof updateUnreadBadge !== 'undefined') {
            const originalUpdateUnreadBadge = updateUnreadBadge;
            window.updateUnreadBadge = function() {
                originalUpdateUnreadBadge();
                setTimeout(updatePageUnreadBadge, 100);
            };
        }
    }, 100);
});
</script>

<?php include 'views/partials/footer.php'; ?>
