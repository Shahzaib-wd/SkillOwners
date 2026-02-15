<?php
require_once __DIR__ . '/../config.php';

/**
 * AgencyInvitation Model
 * Manages freelancer invitations to agencies
 */

class AgencyInvitation {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Create a new invitation
     */
    public function create($agencyId, $email, $role, $invitedBy) {
        // Check if user exists and is a freelancer
        $userSql = "SELECT id, role FROM users WHERE email = :email AND is_active = 1";
        $userStmt = $this->conn->prepare($userSql);
        $userStmt->execute(['email' => $email]);
        $user = $userStmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'No user found with this email address.'];
        }
        
        if ($user['role'] !== 'freelancer') {
            return ['success' => false, 'message' => 'Only freelancers can be invited to agencies.'];
        }
        
        // Check if already a member
        $memberSql = "SELECT id FROM agency_members 
                      WHERE agency_id = :agency_id AND freelancer_id = :freelancer_id";
        $memberStmt = $this->conn->prepare($memberSql);
        $memberStmt->execute([
            'agency_id' => $agencyId,
            'freelancer_id' => $user['id']
        ]);
        
        if ($memberStmt->fetch()) {
            return ['success' => false, 'message' => 'This user is already a member of your agency.'];
        }
        
        // Check for pending invitation
        $pendingSql = "SELECT id FROM agency_invitations 
                       WHERE agency_id = :agency_id 
                       AND email = :email 
                       AND status = 'pending' 
                       AND expires_at > NOW()";
        $pendingStmt = $this->conn->prepare($pendingSql);
        $pendingStmt->execute([
            'agency_id' => $agencyId,
            'email' => $email
        ]);
        
        if ($pendingStmt->fetch()) {
            return ['success' => false, 'message' => 'A pending invitation already exists for this email.'];
        }
        
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + (7 * 24 * 3600)); // 7 days
        
        // Create invitation
        $sql = "INSERT INTO agency_invitations 
                (agency_id, email, token, agency_role, invited_by, expires_at) 
                VALUES (:agency_id, :email, :token, :agency_role, :invited_by, :expires_at)";
        
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            'agency_id' => $agencyId,
            'email' => $email,
            'token' => $token,
            'agency_role' => $role,
            'invited_by' => $invitedBy,
            'expires_at' => $expiresAt
        ]);
        
        if ($result) {
            return [
                'success' => true, 
                'message' => 'Invitation sent successfully!',
                'token' => $token,
                'invitation_id' => $this->conn->lastInsertId()
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create invitation.'];
    }
    
    /**
     * Get invitation by token
     */
    public function getByToken($token) {
        $sql = "SELECT 
                    ai.*,
                    u.full_name as agency_name,
                    u.email as agency_email,
                    inviter.full_name as inviter_name
                FROM agency_invitations ai
                INNER JOIN users u ON ai.agency_id = u.id
                INNER JOIN users inviter ON ai.invited_by = inviter.id
                WHERE ai.token = :token";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }
    
    /**
     * Accept invitation
     */
    public function accept($token, $freelancerId) {
        $invitation = $this->getByToken($token);
        
        if (!$invitation) {
            return ['success' => false, 'message' => 'Invitation not found.'];
        }
        
        if ($invitation['status'] !== 'pending') {
            return ['success' => false, 'message' => 'This invitation is no longer valid.'];
        }
        
        if (strtotime($invitation['expires_at']) < time()) {
            // Mark as expired
            $this->updateStatus($invitation['id'], 'expired');
            return ['success' => false, 'message' => 'This invitation has expired.'];
        }
        
        // Verify the email matches the logged-in user
        $userSql = "SELECT email FROM users WHERE id = :id";
        $userStmt = $this->conn->prepare($userSql);
        $userStmt->execute(['id' => $freelancerId]);
        $user = $userStmt->fetch();
        
        if ($user['email'] !== $invitation['email']) {
            return ['success' => false, 'message' => 'This invitation is not for your account.'];
        }
        
        // Start transaction
        $this->conn->beginTransaction();
        
        try {
            // Add to agency_members
            $memberSql = "INSERT INTO agency_members 
                          (agency_id, freelancer_id, agency_role, invited_by, status) 
                          VALUES (:agency_id, :freelancer_id, :agency_role, :invited_by, 'active')";
            $memberStmt = $this->conn->prepare($memberSql);
            $memberStmt->execute([
                'agency_id' => $invitation['agency_id'],
                'freelancer_id' => $freelancerId,
                'agency_role' => $invitation['agency_role'],
                'invited_by' => $invitation['invited_by']
            ]);
            
            // Update invitation status
            $updateSql = "UPDATE agency_invitations 
                          SET status = 'accepted', accepted_at = NOW() 
                          WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->execute(['id' => $invitation['id']]);
            
            // Add to agency internal conversation
            require_once __DIR__ . '/Message.php';
            $messageModel = new Message();
            $messageModel->addUserToAgencyConversation($invitation['agency_id'], $freelancerId);
            
            $this->conn->commit();
            
            return [
                'success' => true, 
                'message' => 'You have successfully joined the agency!',
                'agency_name' => $invitation['agency_name']
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Accept invitation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to accept invitation. Please try again.'];
        }
    }
    
    /**
     * Reject invitation
     */
    public function reject($token, $freelancerId) {
        $invitation = $this->getByToken($token);
        
        if (!$invitation) {
            return ['success' => false, 'message' => 'Invitation not found.'];
        }
        
        // Verify the email matches the logged-in user
        $userSql = "SELECT email FROM users WHERE id = :id";
        $userStmt = $this->conn->prepare($userSql);
        $userStmt->execute(['id' => $freelancerId]);
        $user = $userStmt->fetch();
        
        if ($user['email'] !== $invitation['email']) {
            return ['success' => false, 'message' => 'This invitation is not for your account.'];
        }
        
        $sql = "UPDATE agency_invitations 
                SET status = 'rejected' 
                WHERE token = :token";
        
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute(['token' => $token])) {
            return ['success' => true, 'message' => 'Invitation rejected.'];
        }
        
        return ['success' => false, 'message' => 'Failed to reject invitation.'];
    }
    
    /**
     * Get all invitations for an agency
     */
    public function getAgencyInvitations($agencyId) {
        $sql = "SELECT 
                    ai.*,
                    inviter.full_name as inviter_name
                FROM agency_invitations ai
                INNER JOIN users inviter ON ai.invited_by = inviter.id
                WHERE ai.agency_id = :agency_id
                ORDER BY ai.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['agency_id' => $agencyId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get pending invitations for a user by email
     */
    public function getUserPendingInvitations($email) {
        $sql = "SELECT 
                    ai.*,
                    u.full_name as agency_name,
                    inviter.full_name as inviter_name
                FROM agency_invitations ai
                INNER JOIN users u ON ai.agency_id = u.id
                INNER JOIN users inviter ON ai.invited_by = inviter.id
                WHERE ai.email = :email 
                AND ai.status = 'pending' 
                AND ai.expires_at > NOW()
                ORDER BY ai.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update invitation status
     */
    private function updateStatus($invitationId, $status) {
        $sql = "UPDATE agency_invitations SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $invitationId
        ]);
    }
    
    /**
     * Cancel invitation (by agency)
     */
    public function cancel($invitationId, $agencyId) {
        $sql = "UPDATE agency_invitations 
                SET status = 'expired' 
                WHERE id = :id AND agency_id = :agency_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $invitationId,
            'agency_id' => $agencyId
        ]);
    }
    
    /**
     * Clean up expired invitations
     */
    public function cleanupExpired() {
        $sql = "UPDATE agency_invitations 
                SET status = 'expired' 
                WHERE status = 'pending' AND expires_at < NOW()";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Approve an application (move to members)
     */
    public function approveApplication($invitationId, $agencyId) {
        $sql = "SELECT * FROM agency_invitations WHERE id = :id AND agency_id = :agency_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $invitationId, 'agency_id' => $agencyId]);
        $invitation = $stmt->fetch();

        if (!$invitation) {
            return ['success' => false, 'message' => 'Application not found.'];
        }

        if ($invitation['status'] !== 'pending') {
            return ['success' => false, 'message' => 'This application is already ' . $invitation['status'] . '.'];
        }

        // Get freelancer ID from the invitation (it's in invited_by for applications)
        $freelancerId = $invitation['invited_by'];
        
        $this->conn->beginTransaction();
        try {
            // Check if already a member
            $checkSql = "SELECT id FROM agency_members WHERE agency_id = :agency_id AND freelancer_id = :freelancer_id";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->execute(['agency_id' => $agencyId, 'freelancer_id' => $freelancerId]);
            
            if (!$checkStmt->fetch()) {
                // Add to agency_members
                $memberSql = "INSERT INTO agency_members (agency_id, freelancer_id, agency_role, status) 
                             VALUES (:agency_id, :freelancer_id, :agency_role, 'active')";
                $memberStmt = $this->conn->prepare($memberSql);
                $memberStmt->execute([
                    'agency_id' => $agencyId,
                    'freelancer_id' => $freelancerId,
                    'agency_role' => $invitation['agency_role']
                ]);
            }

            // Update invitation status
            $updateSql = "UPDATE agency_invitations SET status = 'accepted', accepted_at = NOW() WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->execute(['id' => $invitationId]);

            $this->conn->commit();
            return ['success' => true, 'message' => 'Application approved successfully!'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Reject an application
     */
    public function rejectApplication($invitationId, $agencyId) {
        $sql = "UPDATE agency_invitations 
                SET status = 'rejected' 
                WHERE id = :id AND agency_id = :agency_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute(['id' => $invitationId, 'agency_id' => $agencyId])) {
            return ['success' => true, 'message' => 'Application rejected successfully.'];
        }
        
        return ['success' => false, 'message' => 'Failed to reject application.'];
    }
}
