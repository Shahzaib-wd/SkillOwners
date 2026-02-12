<?php
require_once __DIR__ . '/../config.php';

/**
 * Message Model - Conversation-based Architecture
 * Handles conversations, participants, and messages
 */

class Message {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Get or create a direct conversation between two users
     * @param int $userId1 First user ID
     * @param int $userId2 Second user ID
     * @return int Conversation ID
     */
    public function getOrCreateDirectConversation($userId1, $userId2) {
        // Normalize order to ensure consistent lookup
        $userA = min($userId1, $userId2);
        $userB = max($userId1, $userId2);
        $directKey = "{$userA}_{$userB}";
        
        // Check if conversation exists using direct_key
        $sql = "SELECT id FROM conversations 
                WHERE type = 'direct' AND direct_key = :direct_key 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['direct_key' => $directKey]);
        $result = $stmt->fetch();
        
        if ($result) {
            return $result['id'];
        }
        
        try {
            // Create new conversation with direct_key
            $sql = "INSERT INTO conversations (type, direct_key) VALUES ('direct', :direct_key)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['direct_key' => $directKey]);
            $conversationId = $this->conn->lastInsertId();
            
            // Add both participants
            $sql = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES (:conv_id, :user_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['conv_id' => $conversationId, 'user_id' => $userId1]);
            $stmt->execute(['conv_id' => $conversationId, 'user_id' => $userId2]);
            
            return $conversationId;
        } catch (PDOException $e) {
            // Handle race conditions where another process created it simultaneously
            if ($e->getCode() == 23000) { // Duplicate entry
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['direct_key' => $directKey]);
                $result = $stmt->fetch();
                return $result ? $result['id'] : null;
            }
            throw $e;
        }
    }
    
    /**
     * Get or create agency internal conversation
     * @param int $agencyId Agency user ID
     * @return int|null Conversation ID or null if not an agency
     */
    public function getOrCreateAgencyConversation($agencyId) {
        // Verify agency exists
        $sql = "SELECT id FROM users WHERE id = :agency_id AND role = 'agency' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['agency_id' => $agencyId]);
        
        if (!$stmt->fetch()) {
            return null;
        }
        
        // Check if agency conversation exists
        $sql = "SELECT id FROM conversations WHERE type = 'agency_internal' AND agency_id = :agency_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['agency_id' => $agencyId]);
        $result = $stmt->fetch();
        
        if ($result) {
            return $result['id'];
        }
        
        // Create agency conversation
        $sql = "INSERT INTO conversations (type, title, agency_id) 
                SELECT 'agency_internal', CONCAT(full_name, ' Team Chat'), id 
                FROM users WHERE id = :agency_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['agency_id' => $agencyId]);
        $conversationId = $this->conn->lastInsertId();
        
        // Add agency owner as participant
        $sql = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES (:conv_id, :user_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['conv_id' => $conversationId, 'user_id' => $agencyId]);
        
        // Add all agency members as participants
        $sql = "INSERT INTO conversation_participants (conversation_id, user_id)
                SELECT :conv_id, freelancer_id FROM agency_members 
                WHERE agency_id = :agency_id AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['conv_id' => $conversationId, 'agency_id' => $agencyId]);
        
        return $conversationId;
    }
    
    /**
     * Legacy method: Create message using receiver_id (auto-converts to conversation)
     * @deprecated Use createMessage() with conversation_id instead
     */
    public function create($senderId, $receiverId, $message) {
        $conversationId = $this->getOrCreateDirectConversation($senderId, $receiverId);
        return $this->createMessage($conversationId, $senderId, $message);
    }
    
    /**
     * Create a message in a conversation
     * @param int $conversationId Conversation ID
     * @param int $senderId Sender user ID
     * @param string $message Message content
     * @return bool Success status
     */
    public function createMessage($conversationId, $senderId, $message) {
        // Verify sender is participant
        if (!$this->isParticipant($conversationId, $senderId)) {
            return false;
        }

        // Insert message
        $sql = "INSERT INTO messages (conversation_id, sender_id, message)
                VALUES (:conversation_id, :sender_id, :message)";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'message' => $message
        ]);

        if ($result) {
            // Update conversation timestamp
            $sql = "UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = :conv_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['conv_id' => $conversationId]);
        }

        return $result;
    }
    
    /**
     * Get messages for a conversation
     * @param int $conversationId Conversation ID
     * @param int $userId User ID (for access validation)
     * @param int $limit Number of messages to retrieve
     * @return array Messages
     */
    public function getConversationMessages($conversationId, $userId, $limit = 100) {
        // Verify user is participant
        if (!$this->isParticipant($conversationId, $userId)) {
            return [];
        }
        
        $sql = "SELECT m.*, u.full_name as sender_name, u.profile_image as sender_image,
                   CASE 
                       WHEN c.type = 'agency_internal' AND m.sender_id = c.agency_id THEN 'Owner'
                       WHEN c.type = 'agency_internal' THEN am.agency_role
                       ELSE NULL 
                   END as sender_role
            FROM messages m
            INNER JOIN users u ON m.sender_id = u.id
            INNER JOIN conversations c ON m.conversation_id = c.id
            LEFT JOIN agency_members am ON c.agency_id = am.agency_id AND m.sender_id = am.freelancer_id
            WHERE m.conversation_id = :conversation_id
            ORDER BY m.created_at ASC
            LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':conversation_id', $conversationId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Legacy method: Get conversation between two users
     * @deprecated Use getConversationMessages() instead
     */
    public function getConversation($userId, $otherUserId) {
        $conversationId = $this->getOrCreateDirectConversation($userId, $otherUserId);
        return $this->getConversationMessages($conversationId, $userId);
    }
    
    /**
     * Get all conversations for a user (inbox)
     * @param int $userId User ID
     * @return array Conversations with metadata
     */
    /**
     * Get all conversations for a user (inbox)
     * @param int $userId User ID
     * @return array Conversations with metadata
     */
    public function getUserConversations($userId) {
        $sql = "SELECT 
                    c.id,
                    c.type,
                    c.title,
                    c.updated_at,
                    -- Get other participant info for direct chats
                    CASE 
                        WHEN c.type = 'direct' THEN (
                            SELECT u.full_name 
                            FROM conversation_participants cp
                            INNER JOIN users u ON cp.user_id = u.id
                            WHERE cp.conversation_id = c.id AND cp.user_id != :user_id_1
                            LIMIT 1
                        )
                        ELSE c.title
                    END as display_name,
                    CASE 
                        WHEN c.type = 'direct' THEN (
                            SELECT u.profile_image 
                            FROM conversation_participants cp
                            INNER JOIN users u ON cp.user_id = u.id
                            WHERE cp.conversation_id = c.id AND cp.user_id != :user_id_2
                            LIMIT 1
                        )
                        ELSE NULL
                    END as display_image,
                    CASE 
                        WHEN c.type = 'direct' THEN (
                            SELECT cp.user_id
                            FROM conversation_participants cp
                            WHERE cp.conversation_id = c.id AND cp.user_id != :user_id_3
                            LIMIT 1
                        )
                        ELSE NULL
                    END as other_user_id,
                    -- Last message info
                    (SELECT m.message FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message,
                    (SELECT m.created_at FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message_time,
                    (SELECT u.full_name FROM messages m INNER JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_sender_name,
                    -- Unread count
                    (SELECT COUNT(*) 
                     FROM messages m 
                     WHERE m.conversation_id = c.id 
                     AND m.sender_id != :user_id_4
                     AND m.created_at > COALESCE(
                         (SELECT cp.last_read_at FROM conversation_participants cp WHERE cp.conversation_id = c.id AND cp.user_id = :user_id_5),
                         '1970-01-01 00:00:00'
                     )
                    ) as unread_count
                FROM conversations c
                INNER JOIN conversation_participants cp ON c.id = cp.conversation_id
                WHERE cp.user_id = :user_id_6
                ORDER BY c.updated_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'user_id_1' => $userId,
            'user_id_2' => $userId,
            'user_id_3' => $userId,
            'user_id_4' => $userId,
            'user_id_5' => $userId,
            'user_id_6' => $userId
        ]);
        return $stmt->fetchAll();
    }
    
    /**
     * Legacy method: Get conversations list
     * @deprecated Use getUserConversations() instead
     */
    public function getConversations($userId) {
        return $this->getUserConversations($userId);
    }
    
    /**
     * Mark conversation as read for a user
     * @param int $conversationId Conversation ID
     * @param int $userId User ID
     * @return bool Success status
     */
    public function markConversationAsRead($conversationId, $userId) {
        $sql = "UPDATE conversation_participants 
                SET last_read_at = CURRENT_TIMESTAMP 
                WHERE conversation_id = :conversation_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'conversation_id' => $conversationId,
            'user_id' => $userId
        ]);
    }
    
    /**
     * Legacy method: Mark messages as read
     * @deprecated Use markConversationAsRead() instead
     */
    public function markAsRead($userId, $senderId) {
        $conversationId = $this->getOrCreateDirectConversation($userId, $senderId);
        return $this->markConversationAsRead($conversationId, $userId);
    }
    
    /**
     * Get total unread count for a user
     * @param int $userId User ID
     * @return int Unread count
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(DISTINCT m.conversation_id) as count
                FROM messages m
                INNER JOIN conversation_participants cp ON m.conversation_id = cp.conversation_id AND cp.user_id = :user_id_1
                WHERE m.sender_id != :user_id_2
                AND m.created_at > COALESCE(cp.last_read_at, '1970-01-01 00:00:00')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'user_id_1' => $userId,
            'user_id_2' => $userId
        ]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Check if user is participant in conversation
     * @param int $conversationId Conversation ID
     * @param int $userId User ID
     * @return bool Is participant
     */
    public function isParticipant($conversationId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM conversation_participants 
                WHERE conversation_id = :conversation_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'conversation_id' => $conversationId,
            'user_id' => $userId
        ]);
        
        $result = $stmt->fetch();
        return ($result['count'] ?? 0) > 0;
    }
    
    /**
     * Check if user has access to agency conversation
     * @param int $conversationId Conversation ID
     * @param int $userId User ID
     * @return bool Has access
     */
    public function hasAgencyAccess($conversationId, $userId) {
        $sql = "SELECT c.agency_id 
                FROM conversations c
                WHERE c.id = :conversation_id AND c.type = 'agency_internal'
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['conversation_id' => $conversationId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        $agencyId = $result['agency_id'];
        
        // Check if user is agency owner or member
        $sql = "SELECT COUNT(*) as count FROM (
                    SELECT id FROM users WHERE id = :user_id_1 AND id = :agency_id_1
                    UNION
                    SELECT id FROM agency_members WHERE freelancer_id = :user_id_2 AND agency_id = :agency_id_2 AND status = 'active'
                ) as access";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'user_id_1' => $userId,
            'agency_id_1' => $agencyId,
            'user_id_2' => $userId,
            'agency_id_2' => $agencyId
        ]);
        
        $result = $stmt->fetch();
        return ($result['count'] ?? 0) > 0;
    }
    
    /**
     * Add user to agency conversation (when they join agency)
     * @param int $agencyId Agency ID
     * @param int $userId User ID to add
     * @return bool Success status
     */
    public function addUserToAgencyConversation($agencyId, $userId) {
        $conversationId = $this->getOrCreateAgencyConversation($agencyId);
        
        if (!$conversationId) {
            return false;
        }
        
        // Check if already participant
        if ($this->isParticipant($conversationId, $userId)) {
            return true;
        }
        
        $sql = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES (:conv_id, :user_id)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['conv_id' => $conversationId, 'user_id' => $userId]);
    }
    
    /**
     * Get conversation details
     * @param int $conversationId Conversation ID
     * @param int $userId User ID (for access validation)
     * @return array|null Conversation details
     */
    public function getConversationDetails($conversationId, $userId) {
        if (!$this->isParticipant($conversationId, $userId)) {
            return null;
        }
        
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM conversation_participants WHERE conversation_id = c.id) as participant_count
                FROM conversations c
                WHERE c.id = :conversation_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['conversation_id' => $conversationId]);
        return $stmt->fetch();
    }
}
