<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/AgencyMember.php';
require_once __DIR__ . '/../models/AgencyInvitation.php';
require_once __DIR__ . '/../models/User.php';

/**
 * AgencyController
 * Handles agency-specific business logic
 */

class AgencyController {
    private $memberModel;
    private $invitationModel;
    
    public function __construct() {
        $this->memberModel = new AgencyMember();
        $this->invitationModel = new AgencyInvitation();
    }
    
    /**
     * Invite a freelancer to join the agency
     */
    public function inviteMember($agencyId, $email, $role, $invitedBy) {
        // Validate permission
        if (!$this->hasPermission($agencyId, $invitedBy, 'invite_members')) {
            return ['success' => false, 'message' => 'You do not have permission to invite members.'];
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email address.'];
        }
        
        // Validate role
        $validRoles = ['admin', 'manager', 'member'];
        if (!in_array($role, $validRoles)) {
            return ['success' => false, 'message' => 'Invalid role selected.'];
        }
        
        // Only admins can invite other admins
        if ($role === 'admin') {
            $inviterRole = $this->memberModel->getMemberRole($agencyId, $invitedBy);
            if ($inviterRole !== 'admin') {
                return ['success' => false, 'message' => 'Only admins can invite other admins.'];
            }
        }
        
        $result = $this->invitationModel->create($agencyId, $email, $role, $invitedBy);
        
        if ($result['success']) {
            // Send invitation email
            require_once __DIR__ . '/../helpers/MailHelper.php';
            
            $userModel = new User();
            $inviter = $userModel->findById($invitedBy);
            $agency = $userModel->findById($agencyId); // Agency is also a user record
            
            $subject = "You're invited to join " . $agency['full_name'];
            $inviteLink = SITE_URL . "/dashboard/agency/accept_invitation?token=" . ($result['token'] ?? '');
            
            // If token isn't returned by create, we might need to fetch it or just point to dashboard
            // Assuming create returns the token or we point them to login
            $actionUrl = SITE_URL . "/login";
            
            $body = "
            <h3>Agency Invitation</h3>
            <p>Hello,</p>
            <p><strong>" . htmlspecialchars($inviter['full_name']) . "</strong> has invited you to join their agency <strong>" . htmlspecialchars($agency['full_name']) . "</strong> on " . SITE_NAME . ".</p>
            <p>To accept this invitation, please log in to your account and check your notifications.</p>
            <p><a href='$actionUrl' style='background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Log In to Accept</a></p>";
            
            MailHelper::send($email, $subject, $body);
        }
        
        return $result;
    }
    
    /**
     * Remove a member from the agency
     */
    public function removeMember($memberId, $agencyId, $requesterId) {
        // Check permission
        if (!$this->hasPermission($agencyId, $requesterId, 'remove_members')) {
            return ['success' => false, 'message' => 'You do not have permission to remove members.'];
        }
        
        $member = $this->memberModel->getMemberById($memberId);
        
        if (!$member || $member['agency_id'] != $agencyId) {
            return ['success' => false, 'message' => 'Member not found.'];
        }
        
        // Prevent removing the last admin
        if ($member['agency_role'] === 'admin') {
            $stats = $this->memberModel->getTeamStats($agencyId);
            if ($stats['admins'] <= 1) {
                return ['success' => false, 'message' => 'Cannot remove the last admin. Promote another member first.'];
            }
        }
        
        // Cannot remove yourself
        if ($member['freelancer_id'] == $requesterId) {
            return ['success' => false, 'message' => 'You cannot remove yourself. Contact another admin.'];
        }
        
        if ($this->memberModel->removeMember($memberId)) {
            return ['success' => true, 'message' => 'Member removed successfully.'];
        }
        
        return ['success' => false, 'message' => 'Failed to remove member.'];
    }
    
    /**
     * Change member role
     */
    public function changeMemberRole($memberId, $newRole, $agencyId, $requesterId) {
        // Check permission
        if (!$this->hasPermission($agencyId, $requesterId, 'change_roles')) {
            return ['success' => false, 'message' => 'You do not have permission to change roles.'];
        }
        
        $member = $this->memberModel->getMemberById($memberId);
        
        if (!$member || $member['agency_id'] != $agencyId) {
            return ['success' => false, 'message' => 'Member not found.'];
        }
        
        // Validate role
        $validRoles = ['admin', 'manager', 'member'];
        if (!in_array($newRole, $validRoles)) {
            return ['success' => false, 'message' => 'Invalid role.'];
        }
        
        // Prevent demoting the last admin
        if ($member['agency_role'] === 'admin' && $newRole !== 'admin') {
            $stats = $this->memberModel->getTeamStats($agencyId);
            if ($stats['admins'] <= 1) {
                return ['success' => false, 'message' => 'Cannot demote the last admin. Promote another member first.'];
            }
        }
        
        if ($this->memberModel->updateMemberRole($memberId, $newRole)) {
            return ['success' => true, 'message' => 'Member role updated successfully.'];
        }
        
        return ['success' => false, 'message' => 'Failed to update role.'];
    }
    
    /**
     * Check if user has permission in agency
     */
    public function hasPermission($agencyId, $userId, $permission) {
        // Agency owner (the agency user itself) has all permissions
        if ($agencyId == $userId) {
            return true;
        }
        
        return $this->memberModel->hasPermission($agencyId, $userId, $permission);
    }
    
    /**
     * Get dashboard stats
     */
    public function getDashboardStats($agencyId) {
        $stats = [
            'team' => $this->memberModel->getTeamStats($agencyId),
            'invitations' => $this->getInvitationStats($agencyId)
        ];
        
        return $stats;
    }
    
    /**
     * Get invitation statistics
     */
    private function getInvitationStats($agencyId) {
        $invitations = $this->invitationModel->getAgencyInvitations($agencyId);
        
        $stats = [
            'total' => count($invitations),
            'pending' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'expired' => 0
        ];
        
        foreach ($invitations as $inv) {
            $stats[$inv['status']]++;
        }
        
        return $stats;
    }
    
    /**
     * Validate agency access
     */
    public function validateAgencyAccess($userId) {
        $conn = getDBConnection();
        $sql = "SELECT role FROM users WHERE id = :id AND is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        
        return $user && $user['role'] === 'agency';
    }
    
    /**
     * Check if user is agency owner or member
     */
    public function isAgencyMemberOrOwner($agencyId, $userId) {
        // Check if user is the agency owner
        if ($agencyId == $userId) {
            return true;
        }
        
        // Check if user is a team member
        return $this->memberModel->isMember($agencyId, $userId);
    }

    /**
     * Freelancer applies to join an agency
     */
    public function applyToAgency($agencyId, $freelancerId) {
        // Check if user is a freelancer
        $userModel = new User();
        $user = $userModel->findById($freelancerId);
        if (!$user || $user['role'] !== 'freelancer') {
            return ['success' => false, 'message' => 'Only freelancers can apply to join agencies.'];
        }
        
        // Get agency email
        $agency = $userModel->findById($agencyId);
        if (!$agency || $agency['role'] !== 'agency') {
            return ['success' => false, 'message' => 'Invalid agency selected.'];
        }
        
        // Use invitation model to create a "request"
        // We can reuse the invitation system by setting a special flag or status
        // For now, let's use the standard "create" but we'll need to handle it differently in the UI
        // Actually, let's add a proper application method to Invitation model later if needed
        // For now, let's just use the existing one with a 'member' role request
        $result = $this->invitationModel->create($agencyId, $user['email'], 'member', $freelancerId);
        if (isset($result['success']) && $result['success']) {
            $result['message'] = 'Application submitted successfully! The agency will review your request.';
        }
        
        return $result;
    }

    /**
     * Resolve user ID to email for direct invitations
     */
    public function inviteMemberById($agencyId, $targetUserId, $role, $invitedBy) {
        $userModel = new User();
        $user = $userModel->findById($targetUserId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        return $this->inviteMember($agencyId, $user['email'], $role, $invitedBy);
    }

    /**
     * Approve a freelancer's application to join the agency
     */
    public function approveApplication($invitationId, $agencyId, $approverId) {
        if (!$this->hasPermission($agencyId, $approverId, 'invite_members')) {
            return ['success' => false, 'message' => 'You do not have permission to approve applications.'];
        }
        return $this->invitationModel->approveApplication($invitationId, $agencyId);
    }

    /**
     * Reject a freelancer's application to join the agency
     */
    public function rejectApplication($invitationId, $agencyId, $rejecterId) {
        if (!$this->hasPermission($agencyId, $rejecterId, 'invite_members')) {
            return ['success' => false, 'message' => 'You do not have permission to reject applications.'];
        }
        return $this->invitationModel->rejectApplication($invitationId, $agencyId);
    }
}