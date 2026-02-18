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
    public function searchWithPagination($query = '', $category = '', $page = 1, $perPage = 12, $filters = []) {
        $page = max(1, (int)$page);
        $perPage = min(max(1, (int)$perPage), 50);
        $offset = ($page - 1) * $perPage;
        
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

        // Advanced Filters
        if (!empty($filters['min_price'])) {
            $whereClause .= " AND g.price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $whereClause .= " AND g.price <= ?";
            $params[] = (float)$filters['max_price'];
        }
        if (!empty($filters['delivery_time'])) {
            $whereClause .= " AND g.delivery_time <= ?";
            $params[] = (int)$filters['delivery_time'];
        }

        // Rating filter needs a HAVING clause since it's an aggregate
        $havingClause = "";
        $havingParams = [];
        if (!empty($filters['min_rating'])) {
            $havingClause = "HAVING avg_rating >= ?";
            $havingParams[] = (float)$filters['min_rating'];
        }
        
        // Get total count (using a subquery for accuracy with GROUP BY and HAVING)
        $countSql = "SELECT COUNT(*) as total FROM (
                        SELECT g.id, COALESCE(AVG(r.rating), 0) as avg_rating
                        FROM gigs g 
                        JOIN users u ON g.user_id = u.id 
                        LEFT JOIN reviews r ON g.id = r.gig_id
                        $whereClause
                        GROUP BY g.id
                        $havingClause
                    ) as filtered_gigs";
        
        $stmt = $this->conn->prepare($countSql);
        $stmt->execute(array_merge($params, $havingParams));
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
                $havingClause
                ORDER BY g.created_at DESC 
                LIMIT $perPage OFFSET $offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array_merge($params, $havingParams));
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
        
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        $sql = "INSERT IGNORE INTO gig_analytics (gig_id, user_id, ip_address, type) 
                SELECT id, :user_id, :ip, 'impression' FROM gigs 
                WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")
                AND NOT EXISTS (
                    SELECT 1 FROM gig_analytics 
                    WHERE gig_id = gigs.id 
                    AND type = 'impression' 
                    AND (
                        (:user_id_check IS NOT NULL AND user_id = :user_id_check2)
                        OR (ip_address = :ip_check)
                    )
                )";
        
        // This logic is slightly complex for a single query with IN. 
        // Let's simplify: loop through and check each.
        $count = 0;
        foreach ($ids as $id) {
            if ($this->recordAnalytics($id, 'impression')) {
                $count++;
            }
        }
        return $count > 0;
    }

    public function incrementClick($id) {
        return $this->recordAnalytics($id, 'click');
    }

    private function recordAnalytics($gigId, $type) {
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Check for existing entry
        $checkSql = "SELECT id FROM gig_analytics 
                    WHERE gig_id = :gig_id AND type = :type 
                    AND (";
        $params = ['gig_id' => $gigId, 'type' => $type];
        
        if ($userId) {
            $checkSql .= "user_id = :user_id";
            $params['user_id'] = $userId;
        } else {
            $checkSql .= "ip_address = :ip AND user_id IS NULL";
            $params['ip'] = $ip;
        }
        $checkSql .= ")";

        $stmt = $this->conn->prepare($checkSql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            return false; // Already recorded
        }

        // Insert new record
        $insertSql = "INSERT INTO gig_analytics (gig_id, user_id, ip_address, type) 
                     VALUES (:gig_id, :user_id, :ip, :type)";
        $istmt = $this->conn->prepare($insertSql);
        return $istmt->execute([
            'gig_id' => $gigId,
            'user_id' => $userId,
            'ip' => $ip,
            'type' => $type
        ]);
    }

    public function getGigStats($gigId) {
        $sql = "SELECT 
                    SUM(CASE WHEN type = 'impression' THEN 1 ELSE 0 END) as impressions,
                    SUM(CASE WHEN type = 'click' THEN 1 ELSE 0 END) as clicks
                FROM gig_analytics 
                WHERE gig_id = :gig_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['gig_id' => $gigId]);
        $stats = $stmt->fetch();
        
        $stats['impressions'] = (int)($stats['impressions'] ?? 0);
        $stats['clicks'] = (int)($stats['clicks'] ?? 0);
        $stats['ctr'] = $stats['impressions'] > 0 ? round(($stats['clicks'] / $stats['impressions']) * 100, 2) : 0;
        
        return $stats;
    }

    public function getUserStats($userId) {
        $sql = "SELECT 
                    COUNT(DISTINCT a.id) as total_interactions,
                    (SELECT COUNT(*) FROM gigs WHERE user_id = :user_id) as total_gigs,
                    SUM(CASE WHEN a.type = 'impression' THEN 1 ELSE 0 END) as total_impressions,
                    SUM(CASE WHEN a.type = 'click' THEN 1 ELSE 0 END) as total_clicks
                FROM gigs g
                LEFT JOIN gig_analytics a ON g.id = a.gig_id
                WHERE g.user_id = :user_id_main";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'user_id_main' => $userId]);
        $res = $stmt->fetch();
        
        return [
            'total_impressions' => (int)($res['total_impressions'] ?? 0),
            'total_clicks' => (int)($res['total_clicks'] ?? 0),
            'total_gigs' => (int)($res['total_gigs'] ?? 0)
        ];
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
