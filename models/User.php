<?php
require_once __DIR__ . '/../config.php';

/**
 * User Model
 * Database operations for users table
 */

class User {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function create($fullName, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (:full_name, :email, :password, :role)";
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }
    
    public function update($id, $data) {
        $allowedFields = ['full_name', 'bio', 'skills', 'portfolio_link', 'profile_image'];
        $updates = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getGigCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM gigs WHERE user_id = :user_id AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function getProjectCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM projects WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function searchFreelancers($query = '', $filters = []) {
        $sql = "SELECT id, full_name, email, bio, skills, profile_image, created_at 
                FROM users 
                WHERE (role = 'freelancer' OR role = 'agency') AND is_active = 1";
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (full_name LIKE :query OR skills LIKE :query OR bio LIKE :query)";
            $params['query'] = "%$query%";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT 50";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
