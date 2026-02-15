<?php
require_once __DIR__ . '/../config.php';

class Category {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function findAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findActive() {
        $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($name, $slug, $icon) {
        $sql = "INSERT INTO categories (name, slug, icon) VALUES (:name, :slug, :icon)";
        $stmt = $this->conn->prepare($sql);
        try {
            return $stmt->execute([
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function update($id, $name, $slug, $icon, $isActive) {
        $sql = "UPDATE categories SET name = :name, slug = :slug, icon = :icon, is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'icon' => $icon,
            'is_active' => $isActive
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
