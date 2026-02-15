<?php
require_once __DIR__ . '/../config.php';

class ContactMessage {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function create($name, $email, $subject, $message) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                VALUES (:name, :email, :subject, :message)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);
    }
    
    public function findAll() {
        $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE contact_messages SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM contact_messages WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findById($id) {
        $sql = "SELECT * FROM contact_messages WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
