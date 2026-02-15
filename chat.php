<?php
require_once 'config.php';
requireLogin();

// Support both conversation_id (new) and receiver_id/seller_id (legacy)
$conversationId = $_GET['conversation_id'] ?? null;
$receiverId = $_GET['seller_id'] ?? $_GET['receiver_id'] ?? null;

// If receiver_id provided, convert to conversation_id
if (!$conversationId && $receiverId) {
    require_once 'models/Message.php';
    $messageModel = new Message();
    // All receiver_id lookups should create or get a direct conversation.
    // Team chats are handled by passing conversation_id directly.
    $conversationId = $messageModel->getOrCreateDirectConversation($_SESSION['user_id'], $receiverId);
}

// Get conversation details if we have an ID
$conversationDetails = null;
$conversationType = 'direct';
$conversationTitle = 'Chat';

if ($conversationId) {
    require_once 'models/Message.php';
    $messageModel = new Message();
    $conversationDetails = $messageModel->getConversationDetails($conversationId, $_SESSION['user_id']);
    
    if ($conversationDetails) {
        $conversationType = $conversationDetails['type'];
        if ($conversationType === 'agency_internal') {
            $conversationTitle = $conversationDetails['title'] ?? 'Team Chat';
        } elseif ($conversationType === 'direct') {
            // Get the other participant's name
            $conn = getDBConnection();
            $stmt = $conn->prepare("
                SELECT u.full_name, u.id
                FROM conversation_participants cp
                INNER JOIN users u ON cp.user_id = u.id
                WHERE cp.conversation_id = :conv_id 
                AND cp.user_id != :current_user
                LIMIT 1
            ");
            $stmt->execute([
                'conv_id' => $conversationId,
                'current_user' => $_SESSION['user_id']
            ]);
            $otherUser = $stmt->fetch();
            
            if ($otherUser) {
                $conversationTitle = htmlspecialchars($otherUser['full_name']);
            } else {
                $conversationTitle = 'Direct Chat';
            }
        }
    }
}

include 'views/partials/header.php';
?>

<style>
.chat-container {
    padding: 5rem 0 2rem;
    min-height: 100vh;
}
.chat-box {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    height: 600px;
    display: flex;
    flex-direction: column;
}
.chat-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chat-messages {
    flex: 1;
    padding: 1.5rem;
    overflow-y: auto;
    background: #f8fafc;
}
.message-wrapper {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    max-width: 80%;
}
.message-sent-wrapper {
    margin-left: auto;
    flex-direction: row-reverse;
}
.message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    flex-shrink: 0;
    overflow: hidden;
}
.message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.message-body {
    display: flex;
    flex-direction: column;
}
.message-sent-wrapper .message-body {
    align-items: flex-end;
}
.message-content {
    padding: 0.75rem 1rem;
    border-radius: 12px;
    background: white;
    color: var(--foreground);
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border: 1px solid var(--border);
    position: relative;
}
.message-sent .message-content {
    background: var(--primary);
    color: var(--primary-foreground);
    border: none;
}
.message-sender {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--muted-foreground);
    margin-bottom: 4px;
}
.message-sent .message-sender {
    display: none; /* Hide sender name for self on sent messages */
}
.role-tag {
    font-size: 0.6rem;
    padding: 1px 4px;
    border-radius: 4px;
    margin-left: 4px;
    text-transform: uppercase;
    vertical-align: middle;
}
.role-owner { background: #fefce8; color: #a16207; border: 1px solid #fef3c7; }
.role-admin { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
.role-manager { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
.role-member { background: #f9fafb; color: #4b5563; border: 1px solid #f3f4f6; }
.message-time {
    font-size: 0.75rem;
    color: var(--muted-foreground);
    margin-top: 0.25rem;
}
.chat-input-area {
    padding: 1rem;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 0.75rem;
}
.chat-input-area input {
    flex: 1;
}
.chat-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--accent);
    color: var(--accent-foreground);
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 500;
}

    .report-link {
        font-size: 0.7rem;
        color: #ef4444; /* Red color */
        margin-left: 8px;
        text-decoration: underline;
        cursor: pointer;
        display: inline-block;
    }
    .report-link:hover {
        color: #dc2626;
    }
</style>

<div class="chat-container">
    <div class="container">

        
        <div class="chat-box">
            <div class="chat-header">
                <div>
                    <h3 class="h5 mb-0">
                        <i class="fas fa-comments"></i> <?php echo htmlspecialchars($conversationTitle); ?>
                    </h3>
                    <?php if ($conversationType === 'agency_internal'): ?>
                        <span class="chat-type-badge"><i class="fas fa-users"></i> Team Chat</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <p class="text-center text-muted">Loading messages...</p>
            </div>
            
            <div class="chat-input-area">
                <input 
                    type="text" 
                    id="chatInput" 
                    class="form-control" 
                    placeholder="Type your message..." 
                    onkeypress="if(event.key==='Enter'){sendMessageConversation(<?php echo $conversationId ?? 0; ?>, this.value);}">
                <button 
                    class="btn btn-primary" 
                    onclick="sendMessageConversation(<?php echo $conversationId ?? 0; ?>, document.getElementById('chatInput').value);">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// New conversation-based chat functions
let currentConversationId = <?php echo $conversationId ?? 0; ?>;
let conversationLastSignature = null;
let conversationIsFetching = false;
let conversationIsSending = false;
let conversationPollInterval = null;
let currentUserRole = '<?php echo getUserRole(); ?>';

// Local definitions to ensure no caching issues or loading order issues
function displayMessagesLocally(messages) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.innerHTML = '';
    messages.forEach(msg => {
        const messageWrapper = document.createElement('div');
        messageWrapper.className = msg.is_sender ? 'message-wrapper message-sent-wrapper message-sent' : 'message-wrapper message-received';
        
        let roleTag = '';
        if (msg.sender_role) {
            const roleClass = 'role-' + msg.sender_role.toLowerCase();
            roleTag = `<span class="role-tag ${roleClass}">${escapeHtml(msg.sender_role)}</span>`;
        }

        let avatar = '';
        if (msg.sender_image) {
            avatar = `<img src="<?php echo SITE_URL; ?>/uploads/${escapeHtml(msg.sender_image)}" alt="${escapeHtml(msg.sender_name)}">`;
        } else {
            avatar = (msg.sender_name || 'U').charAt(0).toUpperCase();
        }

        let reportLink = '';
        if (currentUserRole === 'buyer' && msg.is_abusive && !msg.is_sender) {
            reportLink = `<span class="report-link" onclick="reportMessage(${msg.id}, ${msg.sender_id})">Report</span>`;
        }

        messageWrapper.innerHTML = `
            <div class="message-avatar">${avatar}</div>
            <div class="message-body">
                <div class="message-sender">
                    ${escapeHtml(msg.sender_name || 'Unknown')}
                    ${roleTag}
                </div>
                <div class="message-content">
                    ${escapeHtml(msg.message)}
                    ${reportLink}
                </div>
                <div class="message-time">${formatTime(msg.created_at)}</div>
            </div>
        `;
        container.appendChild(messageWrapper);
    });
}

function buildSignatureLocally(messages) {
    if (!Array.isArray(messages) || messages.length === 0) return '0';
    const last = messages[messages.length - 1];
    return `${messages.length}|${last.created_at}|${last.message}|${last.sender_name}`;
}

function sendMessageConversation(conversationId, message) {
    if (!conversationId || conversationId <= 0) {
        alert('Invalid conversation');
        return;
    }
    
    if (conversationIsSending) return;
    if (!message || !message.trim()) return;

    const input = document.getElementById('chatInput');
    const button = document.querySelector('.chat-input-area button');

    conversationIsSending = true;
    if (button) button.disabled = true;

    ajaxRequest('chat_api', 'POST', {
        action: 'send_message',
        conversation_id: conversationId,
        message: message
    }).then(data => {
        if (data.success) {
            if (input) input.value = '';
            loadConversationMessages(conversationId);
        } else {
            console.error('Failed to send message:', data.message);
            alert('Failed to send message: ' + data.message);
        }
    }).catch(error => {
        console.error('Error sending message:', error);
        alert('Error sending message. Check console for details.');
    }).finally(() => {
        conversationIsSending = false;
        if (button) button.disabled = false;
    });
}

function loadConversationMessages(conversationId) {
    if (conversationIsFetching || !conversationId || conversationId <= 0) return;
    conversationIsFetching = true;

    const container = document.getElementById('chatMessages');
    const wasNearBottom = container ? isNearBottom(container, 80) : true;

    const url = `chat_api?action=get_messages&conversation_id=${conversationId}&_=${Date.now()}`;

    ajaxRequest(url)
        .then(data => {
            if (data.success) {
                const signature = buildSignatureLocally(data.messages);
                if (signature !== conversationLastSignature) {
                    displayMessagesLocally(data.messages);
                    conversationLastSignature = signature;

                    if (wasNearBottom) {
                        scrollToBottom('chatMessages');
                    }
                }
            } else {
                console.error('Failed to load messages:', data.message);
                if (container) {
                    container.innerHTML = '<p class="text-center text-danger">Failed to load messages: ' + data.message + '</p>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        })
        .finally(() => {
            conversationIsFetching = false;
        });
}

function startConversationPolling(conversationId) {
    // Clear any existing interval
    if (conversationPollInterval) clearInterval(conversationPollInterval);
    
    conversationPollInterval = setInterval(() => {
        loadConversationMessages(conversationId);
    }, 3000); // Poll every 3 seconds
}

function reportMessage(messageId, reportedId) {
    if (!confirm('Are you sure you want to report this message as abusive? This action cannot be undone.')) {
        return;
    }

    const reason = prompt('Please provide a reason for reporting (optional):', 'Abusive language');
    if (reason === null) return; // Cancelled

    ajaxRequest('chat_api', 'POST', {
        action: 'submit_report',
        conversation_id: currentConversationId,
        message_id: messageId,
        reported_id: reportedId,
        reason: reason || 'Abusive language'
    }).then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Failed to submit report: ' + data.message);
        }
    }).catch(error => {
        console.error('Error reporting message:', error);
        alert('An error occurred while reporting the message.');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (currentConversationId > 0) {
        loadConversationMessages(currentConversationId);
        startConversationPolling(currentConversationId);
    } else {
        const container = document.getElementById('chatMessages');
        if (container) {
            container.innerHTML = '<p class="text-center text-muted">Select a conversation to start chatting</p>';
        }
    }
});

// Stop polling when user leaves the page
window.addEventListener('beforeunload', function() {
    if (conversationPollInterval) clearInterval(conversationPollInterval);
});
</script>

<?php include 'views/partials/footer.php'; ?>
