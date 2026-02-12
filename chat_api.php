<?php
require_once 'config.php';
require_once 'models/Message.php';
requireLogin();

header('Content-Type: application/json');
// Prevent caching (polling-based chat must always hit server)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

function jsonResponse($payload, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($payload);
    exit;
}

function requirePositiveInt($value, $fieldName) {
    if ($value === null || $value === '' || !is_numeric($value)) {
        throw new Exception($fieldName . ' is required');
    }
    $intVal = (int)$value;
    if ($intVal <= 0) {
        throw new Exception($fieldName . ' must be a positive integer');
    }
    return $intVal;
}

$action = $_GET['action'] ?? '';
$messageModel = new Message();

// Handle JSON POST data
$rawBody = file_get_contents('php://input');
$input = json_decode($rawBody, true) ?? [];
$postAction = $input['action'] ?? '';
if ($postAction) {
    $action = $postAction;
}

try {
    switch ($action) {
        case 'get_messages':
            // Support both conversation_id (new) and receiver_id (legacy)
            $conversationId = $_GET['conversation_id'] ?? null;
            $receiverId = $_GET['receiver_id'] ?? null;
            
            if ($conversationId) {
                // New conversation-based approach
                $conversationId = requirePositiveInt($conversationId, 'Conversation ID');
                
                // Verify access
                if (!$messageModel->isParticipant($conversationId, $_SESSION['user_id'])) {
                    throw new Exception('Access denied to this conversation');
                }
                
                // For agency conversations, additional check
                $convDetails = $messageModel->getConversationDetails($conversationId, $_SESSION['user_id']);
                if ($convDetails && $convDetails['type'] === 'agency_internal') {
                    if (!$messageModel->hasAgencyAccess($conversationId, $_SESSION['user_id'])) {
                        throw new Exception('Access denied to agency conversation');
                    }
                }
                
                $messages = $messageModel->getConversationMessages($conversationId, $_SESSION['user_id']);
                
                // Mark as read
                $messageModel->markConversationAsRead($conversationId, $_SESSION['user_id']);
                
            } elseif ($receiverId) {
                // Legacy approach - auto-convert to conversation
                $receiverId = requirePositiveInt($receiverId, 'Receiver ID');
                if ($receiverId === (int)($_SESSION['user_id'] ?? 0)) {
                    throw new Exception('Invalid receiver');
                }
                
                // Get or create conversation
                $conversationId = $messageModel->getOrCreateDirectConversation($_SESSION['user_id'], $receiverId);
                $messages = $messageModel->getConversationMessages($conversationId, $_SESSION['user_id']);
                
                // Mark as read
                $messageModel->markConversationAsRead($conversationId, $_SESSION['user_id']);
                
            } else {
                throw new Exception('Either conversation_id or receiver_id is required');
            }

            $formattedMessages = [];
            foreach ($messages as $msg) {
                $formattedMessages[] = [
                    'message' => $msg['message'],
                    'created_at' => $msg['created_at'],
                    'is_sender' => ((int)$msg['sender_id'] === (int)$_SESSION['user_id']),
                    'sender_name' => (!empty($msg['sender_name']) ? $msg['sender_name'] : 'User ' . $msg['sender_id']),
                    'sender_role' => $msg['sender_role'] ?? null,
                ];
            }

            jsonResponse([
                'success' => true,
                'messages' => $formattedMessages,
                'conversation_id' => $conversationId
            ]);

        case 'send_message':
            // Support both conversation_id (new) and receiver_id (legacy)
            $conversationId = $input['conversation_id'] ?? null;
            $receiverId = $input['receiver_id'] ?? null;
            
            $message = trim($input['message'] ?? '');
            if ($message === '') {
                throw new Exception('Message cannot be empty');
            }
            
            if ($conversationId) {
                // New conversation-based approach
                $conversationId = requirePositiveInt($conversationId, 'Conversation ID');
                
                // Verify access
                if (!$messageModel->isParticipant($conversationId, $_SESSION['user_id'])) {
                    throw new Exception('Access denied to this conversation');
                }
                
                // For agency conversations, additional check
                $convDetails = $messageModel->getConversationDetails($conversationId, $_SESSION['user_id']);
                if ($convDetails && $convDetails['type'] === 'agency_internal') {
                    if (!$messageModel->hasAgencyAccess($conversationId, $_SESSION['user_id'])) {
                        throw new Exception('Access denied to agency conversation');
                    }
                }
                
                $success = $messageModel->createMessage($conversationId, $_SESSION['user_id'], $message);
                
            } elseif ($receiverId) {
                // Legacy approach - auto-convert to conversation
                $receiverId = requirePositiveInt($receiverId, 'Receiver ID');
                if ($receiverId === (int)($_SESSION['user_id'] ?? 0)) {
                    throw new Exception('Invalid receiver');
                }

                // All receiver_id lookups should use direct conversations.
                // For agency internal chats, the frontend must pass conversation_id.
                $success = $messageModel->create($_SESSION['user_id'], $receiverId, $message);
                $conversationId = $messageModel->getOrCreateDirectConversation($_SESSION['user_id'], $receiverId);

            } else {
                throw new Exception('Either conversation_id or receiver_id is required');
            }
            
            if (!$success) {
                throw new Exception('Failed to send message');
            }

            jsonResponse([
                'success' => true,
                'message' => 'Message sent successfully',
                'conversation_id' => $conversationId
            ]);

        case 'get_inbox':
            // Get all conversations for current user
            $conversations = $messageModel->getUserConversations($_SESSION['user_id']);
            
            jsonResponse([
                'success' => true,
                'conversations' => $conversations
            ]);

        case 'get_unread_count':
            // Get total unread message count
            $count = $messageModel->getUnreadCount($_SESSION['user_id']);
            
            jsonResponse([
                'success' => true,
                'unread_count' => $count
            ]);

        case 'get_agency_conversation':
            // Get or create agency internal conversation
            $agencyId = requirePositiveInt($input['agency_id'] ?? $_GET['agency_id'] ?? null, 'Agency ID');
            
            // Verify user has access to this agency
            $sql = "SELECT COUNT(*) as count FROM (
                        SELECT id FROM users WHERE id = :user_id_1 AND id = :agency_id_1
                        UNION
                        SELECT id FROM agency_members WHERE freelancer_id = :user_id_2 AND agency_id = :agency_id_2 AND status = 'active'
                    ) as access";
            
            $conn = getDBConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id_1' => $_SESSION['user_id'],
                'agency_id_1' => $agencyId,
                'user_id_2' => $_SESSION['user_id'],
                'agency_id_2' => $agencyId
            ]);
            $result = $stmt->fetch();
            
            if (($result['count'] ?? 0) == 0) {
                throw new Exception('Access denied to this agency');
            }
            
            $conversationId = $messageModel->getOrCreateAgencyConversation($agencyId);
            
            if (!$conversationId) {
                throw new Exception('Failed to create agency conversation');
            }
            
            // Auto-sync: Ensure current user is a participant if they have access
            $messageModel->addUserToAgencyConversation($agencyId, $_SESSION['user_id']);
            
            jsonResponse([
                'success' => true,
                'conversation_id' => $conversationId
            ]);

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
