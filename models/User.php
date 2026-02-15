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
    
    public function findAll() {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
        $allowedFields = ['full_name', 'email', 'role', 'bio', 'skills', 'portfolio_link', 'profile_image', 'professional_title', 'location', 'phone', 'experience_years', 'linkedin_url', 'twitter_url', 'github_url', 'languages'];
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

    public function updateStatus($id, $isActive) {
        $sql = "UPDATE users SET is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'is_active' => $isActive]);
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
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
    
    public function searchWithPagination($role = 'freelancer', $query = '', $page = 1, $perPage = 12) {
        $page = max(1, (int)$page);
        $perPage = min(max(1, (int)$perPage), 50);
        $offset = ($page - 1) * $perPage;
        
        $whereClause = "WHERE role = :role AND is_active = 1";
        $params = ['role' => $role];
        
        if (!empty($query)) {
            $whereClause .= " AND (full_name LIKE :query OR skills LIKE :query OR bio LIKE :query)";
            $params['query'] = "%$query%";
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM users $whereClause";
        $stmt = $this->conn->prepare($countSql);
        $stmt->execute($params);
        $totalResult = $stmt->fetch();
        $totalItems = (int)$totalResult['total'];
        $totalPages = ceil($totalItems / $perPage);
        
        // Get paginated results
        $sql = "SELECT id, full_name, email, bio, skills, profile_image, created_at 
                FROM users 
                $whereClause 
                ORDER BY created_at DESC 
                LIMIT $perPage OFFSET $offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();
        
        return [
            'users' => $users,
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
    public function getStats() {
        $stats = [
            'total_users' => 0,
            'freelancers' => 0,
            'agencies' => 0,
            'buyers' => 0,
            'admins' => 0
        ];
        
        $sql = "SELECT role, COUNT(*) as count FROM users WHERE is_active = 1 GROUP BY role";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $stats['freelancers'] = $results['freelancer'] ?? 0;
        $stats['agencies'] = $results['agency'] ?? 0;
        $stats['buyers'] = $results['buyer'] ?? 0;
        $stats['admins'] = $results['admin'] ?? 0;
        $stats['total_users'] = array_sum($stats);
        
        return $stats;
    }
}
