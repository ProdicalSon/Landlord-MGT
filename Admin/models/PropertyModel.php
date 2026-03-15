<?php
// admin/models/PropertyModel.php
require_once __DIR__ . '/../config/database.php';

class PropertyModel {
    private $conn;
    private $table_name = "properties";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get recent properties
     */
    public function getRecentProperties($limit = 5) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT p.*, u.username as landlord_name 
                      FROM " . $this->table_name . " p
                      LEFT JOIN users u ON p.landlord_id = u.id
                      ORDER BY p.created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent properties error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get property by ID
     */
    public function getPropertyById($id) {
        if (!$this->conn) return null;
        
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get property error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all properties with pagination
     */
    public function getAllProperties($limit = 20, $offset = 0) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT p.*, u.username as landlord_name 
                      FROM " . $this->table_name . " p
                      LEFT JOIN users u ON p.landlord_id = u.id
                      ORDER BY p.created_at DESC 
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all properties error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total properties
     */
    public function countProperties() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count properties error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update property status
     */
    public function updatePropertyStatus($id, $status) {
        if (!$this->conn) return false;
        
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update property status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete property
     */
    public function deleteProperty($id) {
        if (!$this->conn) return false;
        
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete property error: " . $e->getMessage());
            return false;
        }
    }
    /**
 * Count properties by landlord
 */
public function countPropertiesByLandlord($landlord_id) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE landlord_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $landlord_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count properties by landlord error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count properties by status
 */
public function countPropertiesByStatus($status) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count properties by status error: " . $e->getMessage());
        return 0;
    }
}
}
?>