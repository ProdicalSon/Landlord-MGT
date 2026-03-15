<?php
// admin/models/UserModel.php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get recent users
     */
    public function getRecentUsers($limit = 5) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT id, username, email, user_type, is_verified, created_at 
                      FROM " . $this->table_name . " 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent users error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        if (!$this->conn) return null;
        
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all users with pagination
     */
    public function getAllUsers($limit = 20, $offset = 0) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      ORDER BY created_at DESC 
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total users
     */
    public function countUsers() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count users error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update user status
     */
    public function updateUserStatus($id, $is_verified) {
        if (!$this->conn) return false;
        
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET is_verified = :verified, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':verified', $is_verified);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update user status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        if (!$this->conn) return false;
        
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    public function getUsersByType($type, $limit = 10, $offset = 0) {
    if (!$this->conn) return [];
    
    try {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_type = :type 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get users by type error: " . $e->getMessage());
        return [];
    }
}

/**
 * Count users by type
 */
public function countUsersByType($type) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_type = :type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count users by type error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count verified users by type
 */
public function countVerifiedUsers($type) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE user_type = :type AND is_verified = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count verified users error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Count pending users by type
 */
public function countPendingUsers($type) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE user_type = :type AND is_verified = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count pending users error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Search users
 */
public function searchUsers($search, $type = null, $limit = 10, $offset = 0) {
    if (!$this->conn) return [];
    
    try {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE (username LIKE :search 
                         OR email LIKE :search 
                         OR first_name LIKE :search 
                         OR last_name LIKE :search 
                         OR phone_number LIKE :search)";
        
        if ($type) {
            $query .= " AND user_type = :type";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $searchTerm = "%$search%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Search users error: " . $e->getMessage());
        return [];
    }
}
}
?>