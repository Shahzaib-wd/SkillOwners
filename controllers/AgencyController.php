<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/AgencyMember.php';
require_once __DIR__ . '/../models/AgencyInvitation.php';

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
        
        return $this->invitationModel->create($agencyId, $email, $role, $invitedBy);
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
}
