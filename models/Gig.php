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
        $sql = "SELECT g.*, u.full_name, u.profile_image as seller_image
                FROM gigs g
                JOIN users u ON g.user_id = u.id
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

        $sql .= " ORDER BY g.created_at DESC LIMIT 100";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
}
