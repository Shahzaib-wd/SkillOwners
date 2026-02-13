<?php
class Review {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO reviews (order_id, gig_id, buyer_id, rating, comment) 
                VALUES (:order_id, :gig_id, :buyer_id, :rating, :comment)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function findByGigId($gigId) {
        $sql = "SELECT r.*, u.full_name as buyer_name, u.profile_image
                FROM reviews r
                JOIN users u ON r.buyer_id = u.id
                WHERE r.gig_id = :gig_id
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['gig_id' => $gigId]);
        return $stmt->fetchAll();
    }

    public function getAverageRating($gigId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as count 
                FROM reviews 
                WHERE gig_id = :gig_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['gig_id' => $gigId]);
        return $stmt->fetch();
    }

    public function hasReviewed($orderId) {
        $sql = "SELECT id FROM reviews WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetch() ? true : false;
    }
}
