<?php
// models/SavedPropertyModel.php
require_once __DIR__ . '/../config/database.php';

class SavedPropertyModel {
    private $conn;
    private $table_name = "saved_properties";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if ($this->conn) {
            $this->createTable();
        }
    }

    // Get saved property IDs for user
    public function getSavedPropertyIds($user_id) {
        if (!$this->conn) {
            return [];
        }
        
        try {
            $query = "SELECT property_id FROM " . $this->table_name . " WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Save property
    public function saveProperty($user_id, $property_id) {
        if (!$this->conn) {
            return false;
        }
        
        try {
            // Check if already saved
            if ($this->isSaved($user_id, $property_id)) {
                return $this->unsaveProperty($user_id, $property_id);
            }
            
            $query = "INSERT INTO " . $this->table_name . " (user_id, property_id, saved_at) 
                      VALUES (:user_id, :property_id, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':property_id', $property_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Unsave property
    public function unsaveProperty($user_id, $property_id) {
        if (!$this->conn) {
            return false;
        }
        
        try {
            $query = "DELETE FROM " . $this->table_name . " 
                      WHERE user_id = :user_id AND property_id = :property_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':property_id', $property_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Check if property is saved
    public function isSaved($user_id, $property_id) {
        if (!$this->conn) {
            return false;
        }
        
        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE user_id = :user_id AND property_id = :property_id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Count saved properties for user
    public function countSaved($user_id) {
        if (!$this->conn) {
            return 0;
        }
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Create table if it doesn't exist
    private function createTable() {
        if (!$this->conn) {
            return;
        }
        
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            property_id INT NOT NULL,
            saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_save (user_id, property_id)
        )";
        
        try {
            $this->conn->exec($query);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }
}
?>