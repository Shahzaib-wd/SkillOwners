<?php
require_once __DIR__ . '/../config.php';

class Report {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    /**
     * Create a new report.
     */
    public function create($data) {
        $sql = "INSERT INTO reports (reporter_id, reported_id, conversation_id, message_id, reason) 
                VALUES (:reporter_id, :reported_id, :conversation_id, :message_id, :reason)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'reporter_id' => $data['reporter_id'],
            'reported_id' => $data['reported_id'],
            'conversation_id' => $data['conversation_id'],
            'message_id' => $data['message_id'] ?? null,
            'reason' => $data['reason']
        ]);
    }

    /**
     * Get reports for admin review.
     */
    public function getAllReports($status = null) {
        $sql = "SELECT r.*, u1.full_name as reporter_name, u2.full_name as reported_name 
                FROM reports r
                JOIN users u1 ON r.reporter_id = u1.id
                JOIN users u2 ON r.reported_id = u2.id";
        
        if ($status) {
            $sql .= " WHERE r.status = :status";
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        
        if ($status) {
            $stmt->execute(['status' => $status]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Update report status
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE reports SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }
}
