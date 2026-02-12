<?php
require_once __DIR__ . '/../config.php';

/**
 * AgencyMember Model
 * Manages agency team members and their roles
 */

class AgencyMember {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Add a member to an agency
     */
    public function addMember($agencyId, $freelancerId, $role = 'member', $invitedBy = null) {
        $sql = "INSERT INTO agency_members (agency_id, freelancer_id, agency_role, invited_by, status) 
                VALUES (:agency_id, :freelancer_id, :agency_role, :invited_by, 'active')";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'agency_id' => $agencyId,
            'freelancer_id' => $freelancerId,
            'agency_role' => $role,
            'invited_by' => $invitedBy
        ]);
    }
    
    /**
     * Get all members of an agency
     */
    public function getAgencyMembers($agencyId) {
        $sql = "SELECT 
                    am.id,
                    am.agency_role,
                    am.status,
                    am.joined_at,
                    u.id as user_id,
                    u.full_name,
                    u.email,
                    u.profile_image,
                    u.bio,
                    u.skills,
                    inviter.full_name as invited_by_name
                FROM agency_members am
                INNER JOIN users u ON am.freelancer_id = u.id
                LEFT JOIN users inviter ON am.invited_by = inviter.id
                WHERE am.agency_id = :agency_id AND am.status = 'active'
                ORDER BY 
                    FIELD(am.agency_role, 'admin', 'manager', 'member'),
                    am.joined_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['agency_id' => $agencyId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get member details by ID
     */
    public function getMemberById($memberId) {
        $sql = "SELECT 
                    am.*,
                    u.full_name,
                    u.email,
                    u.profile_image
                FROM agency_members am
                INNER JOIN users u ON am.freelancer_id = u.id
                WHERE am.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $memberId]);
        return $stmt->fetch();
    }
    
    /**
     * Check if user is member of agency
     */
    public function isMember($agencyId, $freelancerId) {
        $sql = "SELECT id FROM agency_members 
                WHERE agency_id = :agency_id 
                AND freelancer_id = :freelancer_id 
                AND status = 'active'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'agency_id' => $agencyId,
            'freelancer_id' => $freelancerId
        ]);
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get member's role in agency
     */
    public function getMemberRole($agencyId, $freelancerId) {
        $sql = "SELECT agency_role FROM agency_members 
                WHERE agency_id = :agency_id 
                AND freelancer_id = :freelancer_id 
                AND status = 'active'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'agency_id' => $agencyId,
            'freelancer_id' => $freelancerId
        ]);
        
        $result = $stmt->fetch();
        return $result ? $result['agency_role'] : null;
    }
    
    /**
     * Update member role
     */
    public function updateMemberRole($memberId, $newRole) {
        $sql = "UPDATE agency_members 
                SET agency_role = :role, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'role' => $newRole,
            'id' => $memberId
        ]);
    }
    
    /**
     * Remove member from agency
     */
    public function removeMember($memberId) {
        $sql = "DELETE FROM agency_members WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $memberId]);
    }
    
    /**
     * Get team statistics
     */
    public function getTeamStats($agencyId) {
        $sql = "SELECT 
                    COUNT(*) as total_members,
                    SUM(CASE WHEN agency_role = 'admin' THEN 1 ELSE 0 END) as admins,
                    SUM(CASE WHEN agency_role = 'manager' THEN 1 ELSE 0 END) as managers,
                    SUM(CASE WHEN agency_role = 'member' THEN 1 ELSE 0 END) as members
                FROM agency_members 
                WHERE agency_id = :agency_id AND status = 'active'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['agency_id' => $agencyId]);
        return $stmt->fetch();
    }
    
    /**
     * Get agencies where user is a member
     */
    public function getUserAgencies($freelancerId) {
        $sql = "SELECT 
                    am.id,
                    am.agency_id,
                    am.agency_role,
                    am.joined_at,
                    u.full_name as agency_name,
                    u.email as agency_email
                FROM agency_members am
                INNER JOIN users u ON am.agency_id = u.id
                WHERE am.freelancer_id = :freelancer_id 
                AND am.status = 'active'
                ORDER BY am.joined_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['freelancer_id' => $freelancerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if user has specific permission in agency
     */
    public function hasPermission($agencyId, $freelancerId, $permission) {
        $role = $this->getMemberRole($agencyId, $freelancerId);
        
        if (!$role) {
            return false;
        }
        
        $sql = "SELECT id FROM role_permissions 
                WHERE role = :role AND permission = :permission";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'role' => $role,
            'permission' => $permission
        ]);
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get all permissions for a role
     */
    public function getRolePermissions($role) {
        $sql = "SELECT permission, description 
                FROM role_permissions 
                WHERE role = :role 
                ORDER BY permission";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }
}
