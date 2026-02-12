<?php
require_once __DIR__ . '/../config.php';

/**
 * Order Model
 * Database operations for orders table
 */

class Order {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO orders (gig_id, buyer_id, seller_id, amount, status, delivery_date) 
                VALUES (:gig_id, :buyer_id, :seller_id, :amount, 'pending', :delivery_date)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function findById($id) {
        $sql = "SELECT o.*, 
                       g.title as gig_title, 
                       u1.full_name as buyer_name,
                       u2.full_name as seller_name
                FROM orders o
                JOIN gigs g ON o.gig_id = g.id
                JOIN users u1 ON o.buyer_id = u1.id
                JOIN users u2 ON o.seller_id = u2.id
                WHERE o.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByBuyerId($buyerId) {
        $sql = "SELECT o.*, 
                       g.title as gig_title,
                       g.image as gig_image,
                       u.full_name as seller_name
                FROM orders o
                JOIN gigs g ON o.gig_id = g.id
                JOIN users u ON o.seller_id = u.id
                WHERE o.buyer_id = :buyer_id
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['buyer_id' => $buyerId]);
        return $stmt->fetchAll();
    }
    
    public function findBySellerId($sellerId) {
        $sql = "SELECT o.*, 
                       g.title as gig_title,
                       g.image as gig_image,
                       u.full_name as buyer_name
                FROM orders o
                JOIN gigs g ON o.gig_id = g.id
                JOIN users u ON o.buyer_id = u.id
                WHERE o.seller_id = :seller_id
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['seller_id' => $sellerId]);
        return $stmt->fetchAll();
    }
    
    public function updateStatus($id, $status, $userId) {
        $sql = "UPDATE orders SET status = :status";
        $params = ['id' => $id, 'status' => $status, 'user_id' => $userId];
        
        if ($status === 'completed') {
            $sql .= ", completed_at = NOW()";
        }
        
        $sql .= ", updated_at = NOW() WHERE id = :id AND (buyer_id = :user_id OR seller_id = :user_id)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getActiveOrders($userId) {
        $sql = "SELECT o.*, 
                       g.title as gig_title,
                       g.image as gig_image
                FROM orders o
                JOIN gigs g ON o.gig_id = g.id
                WHERE (o.buyer_id = :user_id OR o.seller_id = :user_id)
                  AND o.status IN ('pending', 'in_progress')
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getCompletedOrders($userId) {
        $sql = "SELECT o.*, 
                       g.title as gig_title,
                       g.image as gig_image
                FROM orders o
                JOIN gigs g ON o.gig_id = g.id
                WHERE (o.buyer_id = :user_id OR o.seller_id = :user_id)
                  AND o.status = 'completed'
                ORDER BY o.completed_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
