<!-- Message Inbox Component -->
<style>
.inbox-section {
    background: var(--card);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border);
}
.inbox-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}
.conversation-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.conversation-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    color: inherit;
}
.conversation-item:hover {
    background: var(--muted);
}
.conversation-item:last-child {
    border-bottom: none;
}
.conversation-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary);
    color: var(--primary-foreground);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 1rem;
    flex-shrink: 0;
    position: relative;
}
.conversation-avatar .unread-dot {
    position: absolute;
    top: 0;
    right: 0;
    width: 12px;
    height: 12px;
    background: #ef4444;
    border: 2px solid var(--card);
    border-radius: 50%;
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
}
.conversation-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}
.conversation-content {
    flex: 1;
    min-width: 0;
}
.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 0.25rem;
}
.conversation-name {
    font-weight: 600;
    color: var(--foreground);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.conversation-time {
    font-size: 0.75rem;
    color: var(--muted-foreground);
    white-space: nowrap;
}
.conversation-preview {
    font-size: 0.875rem;
    color: var(--muted-foreground);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.conversation-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
    margin-left: 1rem;
}
.unread-badge {
    background: var(--primary);
    color: var(--primary-foreground);
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}
.inbox-empty {
    text-align: center;
    padding: 2rem;
    color: var(--muted-foreground);
}
.inbox-empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
.conversation-type-icon {
    font-size: 0.75rem;
    color: var(--muted-foreground);
    margin-left: 0.5rem;
}
</style>

<div class="inbox-section" id="messages">
    <div class="inbox-header">
        <h3 class="h5 mb-0">
            <i class="fas fa-inbox"></i> Messages
            <span id="inboxUnreadBadge" class="unread-badge" style="display: none;">0</span>
        </h3>
        <a href="<?php echo SITE_URL; ?>/browse.php" class="btn btn-sm btn-outline">
            <i class="fas fa-search"></i> Find Someone
        </a>
    </div>
    
    <div id="inboxConversations">
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin"></i> Loading conversations...
        </div>
    </div>
</div>

<script>
// Load inbox on page load
let previousConversationStates = {};

document.addEventListener('DOMContentLoaded', function() {
    loadInbox();
    
    // Refresh inbox every 10 seconds
    setInterval(loadInbox, 10000);
});

function loadInbox() {
    ajaxRequest('<?php echo SITE_URL; ?>/chat_api.php?action=get_inbox&_=' + Date.now())
        .then(data => {
            if (data.success) {
                renderInbox(data.conversations);
                updateUnreadBadge();
            } else {
                console.error('Failed to load inbox:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading inbox:', error);
        });
}

function renderInbox(conversations) {
    const container = document.getElementById('inboxConversations');
    if (!container) return;
    
    if (!conversations || conversations.length === 0) {
        container.innerHTML = `
            <div class="inbox-empty">
                <i class="fas fa-inbox"></i>
                <p>No messages yet</p>
            </div>
        `;
        return;
    }
    
    // Detect new messages
    let hasNewMessages = false;
    conversations.forEach(conv => {
        const convId = conv.id;
        const lastMsgTime = conv.last_message_time;
        
        if (previousConversationStates[convId]) {
            // If last message time changed, there's a new message
            if (lastMsgTime !== previousConversationStates[convId]) {
                hasNewMessages = true;
            }
        }
        
        // Update state
        previousConversationStates[convId] = lastMsgTime;
    });
    
    // Play sound if new messages detected
    if (hasNewMessages && typeof playMessageNotificationSound === 'function') {
        playMessageNotificationSound();
    }
    
    let html = '<div class="conversation-list">';
    
    conversations.forEach(conv => {
        const displayName = escapeHtml(conv.display_name || 'Unknown');
        const lastMessage = escapeHtml(conv.last_message || 'No messages');
        const unreadCount = parseInt(conv.unread_count) || 0;
        const timeStr = formatTimeAgo(conv.last_message_time);
        const conversationUrl = `<?php echo SITE_URL; ?>/chat.php?conversation_id=${conv.id}`;
        
        // Determine avatar
        let avatar = '';
        if (conv.type === 'agency_internal') {
            avatar = '<i class="fas fa-users"></i>';
        } else if (conv.display_image) {
            avatar = `<img src="${escapeHtml(conv.display_image)}" alt="${displayName}">`;
        } else {
            avatar = displayName.charAt(0).toUpperCase();
        }
        
        // Type icon
        let typeIcon = '';
        if (conv.type === 'agency_internal') {
            typeIcon = '<i class="fas fa-building conversation-type-icon" title="Team Chat"></i>';
        }
        
        html += `
            <a href="${conversationUrl}" class="conversation-item" style="display: flex;">
                <div class="conversation-avatar">
                    ${avatar}
                    ${unreadCount > 0 ? '<span class="unread-dot"></span>' : ''}
                </div>
                <div class="conversation-content">
                    <div class="conversation-header">
                        <div class="conversation-name">${displayName}${typeIcon}</div>
                        <div class="conversation-time">${timeStr}</div>
                    </div>
                    <div class="conversation-preview">${lastMessage}</div>
                </div>
                ${unreadCount > 0 ? `<div class="conversation-meta"><span class="unread-badge">${unreadCount}</span></div>` : ''}
            </a>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function updateUnreadBadge() {
    ajaxRequest('<?php echo SITE_URL; ?>/chat_api.php?action=get_unread_count&_=' + Date.now())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('inboxUnreadBadge');
                const count = parseInt(data.unread_count) || 0;
                
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                
                // Update header notification badge if exists
                const headerBadge = document.getElementById('headerUnreadBadge');
                if (headerBadge) {
                    if (count > 0) {
                        headerBadge.textContent = count;
                        headerBadge.style.display = 'inline-block';
                    } else {
                        headerBadge.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error updating unread badge:', error);
        });
}

function formatTimeAgo(timestamp) {
    if (!timestamp) return '';
    
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    if (diff < 604800000) return Math.floor(diff / 86400000) + 'd ago';
    
    return date.toLocaleDateString();
}
</script>
