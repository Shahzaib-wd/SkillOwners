<?php
require_once __DIR__ . '/../config.php';

/**
 * Project Model
 * Database operations for projects table
 */

class Project {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO projects (user_id, title, description, image, project_url) 
                VALUES (:user_id, :title, :description, :image, :project_url)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function findById($id) {
        $sql = "SELECT p.*, u.full_name 
                FROM projects p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByUserId($userId) {
        $sql = "SELECT * FROM projects WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function update($id, $userId, $data) {
        $allowedFields = ['title', 'description', 'image', 'project_url'];
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
        
        $sql = "UPDATE projects SET " . implode(', ', $updates) . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id, $userId) {
        $sql = "DELETE FROM projects WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}
