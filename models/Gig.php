<?php
require_once __DIR__ . '/../config.php';

/**
 * Gig Model
 * Database operations for gigs table
 */

class Gig {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO gigs (user_id, title, description, category, price, delivery_time, image, tags) 
                VALUES (:user_id, :title, :description, :category, :price, :delivery_time, :image, :tags)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function findAll() {
        $sql = "SELECT g.*, u.full_name as seller_name 
                FROM gigs g 
                JOIN users u ON g.user_id = u.id 
                ORDER BY g.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT g.*, u.full_name, u.profile_image as seller_image 
                FROM gigs g 
                JOIN users u ON g.user_id = u.id 
                WHERE g.id = :id AND g.is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByUserId($userId) {
        $sql = "SELECT * FROM gigs WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function search($query = '', $category = '', $filters = []) {
        $sql = "SELECT g.*, u.full_name, u.profile_image as seller_image,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM gigs g
                JOIN users u ON g.user_id = u.id
                LEFT JOIN reviews r ON g.id = r.gig_id
                WHERE g.is_active = 1";
        $params = [];

        if (!empty($query)) {
            $sql .= " AND (g.title LIKE ? OR g.description LIKE ? OR g.tags LIKE ?)";
            $params[] = "%$query%";
            $params[] = "%$query%";
            $params[] = "%$query%";
        }

        if (!empty($category)) {
            $sql .= " AND g.category = ?";
            $params[] = $category;
        }

        $sql .= " GROUP BY g.id ORDER BY g.created_at DESC LIMIT 100";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Search gigs with pagination
     * @param string $query Search query
     * @param string $category Category filter
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page
     * @return array Array containing 'gigs' and 'pagination' info
     */
    public function searchWithPagination($query = '', $category = '', $page = 1, $perPage = 12) {
        $page = max(1, (int)$page);
        $perPage = min(max(1, (int)$perPage), 50); // Limit to 50 items max
        $offset = ($page - 1) * $perPage;
        
        // Base WHERE clause
        $whereClause = "WHERE g.is_active = 1";
        $params = [];
        
        if (!empty($query)) {
            $whereClause .= " AND (g.title LIKE ? OR g.description LIKE ? OR g.tags LIKE ?)";
            $params[] = "%$query%";
            $params[] = "%$query%";
            $params[] = "%$query%";
        }
        
        if (!empty($category)) {
            $whereClause .= " AND g.category = ?";
            $params[] = $category;
        }
        
        // Get total count
        $countSql = "SELECT COUNT(DISTINCT g.id) as total 
                      FROM gigs g 
                      JOIN users u ON g.user_id = u.id 
                      $whereClause";
        $stmt = $this->conn->prepare($countSql);
        $stmt->execute($params);
        $totalResult = $stmt->fetch();
        $totalItems = (int)$totalResult['total'];
        $totalPages = ceil($totalItems / $perPage);
        
        // Get paginated results
        $sql = "SELECT g.*, u.full_name, u.profile_image as seller_image,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM gigs g
                JOIN users u ON g.user_id = u.id
                LEFT JOIN reviews r ON g.id = r.gig_id
                $whereClause
                GROUP BY g.id 
                ORDER BY g.created_at DESC 
                LIMIT $perPage OFFSET $offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $gigs = $stmt->fetchAll();
        
        return [
            'gigs' => $gigs,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
    }
    
    public function update($id, $userId, $data) {
        $allowedFields = ['title', 'description', 'category', 'price', 'delivery_time', 'image', 'tags', 'is_active'];
        $updates = [];
        $params = ['id' => $id, 'user_id' => $userId];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE gigs SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id, $userId) {
        $sql = "DELETE FROM gigs WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function setStatus($id, $isActive) {
        $sql = "UPDATE gigs SET is_active = :is_active, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'is_active' => $isActive
        ]);
    }
    
    /**
     * Get related gigs by category (excluding current gig)
     * @param int $gigId Current gig ID to exclude
     * @param string $category Category to match
     * @param int $limit Number of related gigs to return
     * @return array Array of related gigs
     */
    public function getRelatedGigs($gigId, $category, $limit = 3) {
        $sql = "SELECT g.*, u.full_name, u.profile_image as seller_image,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM gigs g
                JOIN users u ON g.user_id = u.id
                LEFT JOIN reviews r ON g.id = r.gig_id
                WHERE g.id != :gig_id 
                  AND g.category = :category 
                  AND g.is_active = 1
                GROUP BY g.id 
                ORDER BY g.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'gig_id' => $gigId,
            'category' => $category,
            'limit' => $limit
        ]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all gigs contributed to an agency
     * @param int $agencyId
     * @param string|null $status Filter by status (null = all)
     */
    public function getAgencyGigs($agencyId, $status = null) {
        $sql = "SELECT g.*, u.full_name, u.profile_image as seller_image,
                       ag.freelancer_id, ag.created_at as contributed_at, ag.status as contribution_status,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM agency_gigs ag
                INNER JOIN gigs g ON ag.gig_id = g.id
                INNER JOIN users u ON ag.freelancer_id = u.id
                LEFT JOIN reviews r ON g.id = r.gig_id
                WHERE ag.agency_id = :agency_id AND g.is_active = 1";
        
        $params = ['agency_id' => $agencyId];
        
        if ($status !== null) {
            $sql .= " AND ag.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " GROUP BY g.id ORDER BY ag.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Add a freelancer's gig to an agency (1 gig per freelancer per agency)
     */
    public function addGigToAgency($agencyId, $freelancerId, $gigId) {
        // Check if freelancer already contributed a gig
        $existing = $this->getFreelancerAgencyGig($agencyId, $freelancerId);
        if ($existing) {
            return ['success' => false, 'message' => 'You have already contributed a gig to this agency.'];
        }
        
        // Check if gig is already contributed to another agency
        $sql = "SELECT id FROM agency_gigs WHERE gig_id = :gig_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['gig_id' => $gigId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'This gig is already contributed to an agency.'];
        }
        
        // Verify the gig belongs to the freelancer
        $sql = "SELECT id FROM gigs WHERE id = :gig_id AND user_id = :user_id AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['gig_id' => $gigId, 'user_id' => $freelancerId]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Invalid gig selected.'];
        }
        
        $sql = "INSERT INTO agency_gigs (agency_id, freelancer_id, gig_id, status) 
                VALUES (:agency_id, :freelancer_id, :gig_id, 'pending')";
        $stmt = $this->conn->prepare($sql);
        
        try {
            $stmt->execute([
                'agency_id' => $agencyId,
                'freelancer_id' => $freelancerId,
                'gig_id' => $gigId
            ]);
            return ['success' => true, 'message' => 'Gig submitted for agency approval!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to contribute gig. Please try again.'];
        }
    }
    
    /**
     * Remove a gig from an agency
     */
    public function removeGigFromAgency($gigId, $agencyId) {
        $sql = "DELETE FROM agency_gigs WHERE gig_id = :gig_id AND agency_id = :agency_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'gig_id' => $gigId,
            'agency_id' => $agencyId
        ]);
    }
    
    /**
     * Check if a freelancer already contributed a gig to an agency
     */
    public function getFreelancerAgencyGig($agencyId, $freelancerId) {
        $sql = "SELECT ag.*, g.title as gig_title
                FROM agency_gigs ag
                INNER JOIN gigs g ON ag.gig_id = g.id
                WHERE ag.agency_id = :agency_id AND ag.freelancer_id = :freelancer_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'agency_id' => $agencyId,
            'freelancer_id' => $freelancerId
        ]);
        return $stmt->fetch();
    }
    
    /**
     * Approve or reject a contributed gig
     */
    public function updateAgencyGigStatus($gigId, $agencyId, $status) {
        $validStatuses = ['approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $sql = "UPDATE agency_gigs SET status = :status WHERE gig_id = :gig_id AND agency_id = :agency_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'gig_id' => $gigId,
            'agency_id' => $agencyId
        ]);
    }
    public function incrementImpressions($ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE gigs SET impressions = impressions + 1 WHERE id IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(array_values($ids));
    }

    public function incrementClick($id) {
        $sql = "UPDATE gigs SET clicks = clicks + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getUserStats($userId) {
        $sql = "SELECT 
                    SUM(impressions) as total_impressions, 
                    SUM(clicks) as total_clicks,
                    COUNT(*) as total_gigs
                FROM gigs 
                WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    public function getStats() {
        $stats = [
            'total_gigs' => 0,
            'active_gigs' => 0
        ];
        
        $sql = "SELECT COUNT(*) as total FROM gigs";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['total_gigs'] = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as active FROM gigs WHERE is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['active_gigs'] = $stmt->fetchColumn();
        
        return $stats;
    }
}
