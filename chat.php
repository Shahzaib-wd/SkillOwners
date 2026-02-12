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
    padding: 1rem;
    overflow-y: auto;
}
.message {
    margin-bottom: 1rem;
    max-width: 70%;
}
.message-sent {
    margin-left: auto;
    text-align: right;
}
.message-content {
    display: inline-block;
    padding: 0.75rem 1rem;
    border-radius: var(--radius);
    background: var(--primary);
    color: var(--primary-foreground);
}
.message-received .message-content {
    background: var(--muted);
    color: var(--foreground);
}
.message-sender {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 2px;
    display: block;
}
.message-sent .message-sender {
    text-align: right;
    color: var(--muted-foreground);
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
</style>

<div class="chat-container">
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <a href="<?php echo getUserRole() === 'agency' ? '/dashboard/agency.php' : '/dashboard/' . getUserRole() . '.php'; ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
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

// Local definitions to ensure no caching issues or loading order issues
function displayMessagesLocally(messages) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.innerHTML = '';
    messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = msg.is_sender ? 'message message-sent' : 'message message-received';
        
        let roleTag = '';
        if (msg.sender_role) {
            const roleClass = 'role-' + msg.sender_role.toLowerCase();
            roleTag = `<span class="role-tag ${roleClass}">${escapeHtml(msg.sender_role)}</span>`;
        }

        messageDiv.innerHTML = `
            <div class="message-sender">
                ${escapeHtml(msg.sender_name || 'Unknown')}
                ${roleTag}
            </div>
            <div class="message-content">${escapeHtml(msg.message)}</div>
            <div class="message-time">${formatTime(msg.created_at)}</div>
        `;
        container.appendChild(messageDiv);
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

    ajaxRequest('chat_api.php', 'POST', {
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

    const url = `chat_api.php?action=get_messages&conversation_id=${conversationId}&_=${Date.now()}`;

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
