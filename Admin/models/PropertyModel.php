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
     * Get all properties with filters and pagination
     */
   /**
 * Get all properties with filters and pagination
 */
public function getFilteredProperties($filters = [], $limit = 10, $offset = 0) {
    if (!$this->conn) return [];
    
    try {
        $query = "SELECT p.*, u.username as landlord_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.landlord_id = u.id 
                  WHERE 1=1";
        $params = [];
        
        // Apply filters
        if (!empty($filters['status'])) {
            $query .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['property_type'])) {
            $query .= " AND p.property_type = :type";
            $params[':type'] = $filters['property_type'];
        }
        
        if (!empty($filters['landlord_id'])) {
            $query .= " AND p.landlord_id = :landlord_id";
            $params[':landlord_id'] = $filters['landlord_id'];
        }
        
        if (!empty($filters['featured'])) {
            $query .= " AND p.featured = :featured";
            $params[':featured'] = $filters['featured'];
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (p.property_name LIKE :search OR p.address LIKE :search OR p.city LIKE :search OR p.neighborhood LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get filtered properties error: " . $e->getMessage());
        return [];
    }
}

    /**
     * Count properties with filters
     */
    public function countProperties($filters = []) {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE 1=1";
            $params = [];
            
            if (!empty($filters['status'])) {
                $query .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['property_type'])) {
                $query .= " AND property_type = :type";
                $params[':type'] = $filters['property_type'];
            }
            
            if (!empty($filters['landlord_id'])) {
                $query .= " AND landlord_id = :landlord_id";
                $params[':landlord_id'] = $filters['landlord_id'];
            }
            
            if (!empty($filters['search'])) {
                $query .= " AND (property_name LIKE :search OR address LIKE :search OR city LIKE :search OR neighborhood LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count properties error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count total properties
     */
    public function countTotalProperties() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count total properties error: " . $e->getMessage());
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
     * Count featured properties
     */
    public function countFeaturedProperties() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE featured = 1";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count featured properties error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total monthly rent from all properties
     */
    public function getTotalMonthlyRent() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COALESCE(SUM(monthly_rent), 0) as total FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Get total monthly rent error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get property types
     */
    public function getPropertyTypes() {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT DISTINCT property_type FROM " . $this->table_name . " WHERE property_type IS NOT NULL ORDER BY property_type";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Get property types error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update property status
     */
    public function updatePropertyStatus($id, $status) {
        if (!$this->conn) return false;
        
        try {
            $query = "UPDATE " . $this->table_name . " SET status = :status, updated_at = NOW() WHERE id = :id";
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
     * Toggle featured status
     */
    public function toggleFeatured($id, $featured) {
        if (!$this->conn) return false;
        
        try {
            $query = "UPDATE " . $this->table_name . " SET featured = :featured, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':featured', $featured);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Toggle featured error: " . $e->getMessage());
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
     * Get properties by landlord
     */
    public function getPropertiesByLandlord($landlord_id, $limit = 10, $offset = 0) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE landlord_id = :landlord_id 
                      ORDER BY created_at DESC 
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get properties by landlord error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get property with landlord M-Pesa details
     */
    public function getPropertyWithPaymentDetails($property_id) {
        if (!$this->conn) return null;

        try {
            $query = "SELECT p.*, u.mpesa_number, u.mpesa_business_shortcode,
                             u.phone_number as landlord_phone, u.email as landlord_email,
                             u.first_name, u.last_name
                      FROM " . $this->table_name . " p
                      LEFT JOIN users u ON p.landlord_id = u.id
                      WHERE p.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $property_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get property with payment details error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get property image URL based on type
     */
    public function getPropertyImage($property) {
        $images = [
            'apartment' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&h=300&fit=crop',
            'house' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b058?w=400&h=300&fit=crop',
            'studio' => 'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?w=400&h=300&fit=crop',
            'townhouse' => 'https://images.unsplash.com/photo-1628744448840-5d3e8b1c8d9a?w=400&h=300&fit=crop',
            'Bed Sitter' => 'https://images.unsplash.com/photo-1460317442991-0ec209658118?w=400&h=300&fit=crop',
            'Single Room' => 'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=400&h=300&fit=crop'
        ];

        $type = strtolower($property['property_type'] ?? 'apartment');
        return $images[$type] ?? $images['apartment'];
    }

    /**
     * Format address
     */
    public function formatAddress($property) {
        $parts = [];
        if (!empty($property['address'])) $parts[] = $property['address'];
        if (!empty($property['neighborhood'])) $parts[] = $property['neighborhood'];
        if (!empty($property['city'])) $parts[] = $property['city'];
        
        return implode(', ', $parts);
    }
}
