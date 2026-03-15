<?php
// models/PropertyModel.php
require_once __DIR__ . '/../config/database.php';

class PropertyModel {
    private $conn;
    private $table_name = "properties";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all properties
    public function getAllProperties($filters = []) {
        if (!$this->conn) {
            return [];
        }
        
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
            $params = [];

            // Apply filters
            if (!empty($filters['location'])) {
                $query .= " AND (property_name LIKE :location OR address LIKE :location OR city LIKE :location OR neighborhood LIKE :location)";
                $params[':location'] = '%' . $filters['location'] . '%';
            }

            if (!empty($filters['min_price'])) {
                $query .= " AND monthly_rent >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $query .= " AND monthly_rent <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }

            if (!empty($filters['min_beds'])) {
                $query .= " AND bedrooms >= :min_beds";
                $params[':min_beds'] = $filters['min_beds'];
            }

            if (!empty($filters['property_type'])) {
                $query .= " AND property_type = :property_type";
                $params[':property_type'] = $filters['property_type'];
            }

            $query .= " ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting properties: " . $e->getMessage());
            return [];
        }
    }

    // Get property types
    public function getPropertyTypes() {
        if (!$this->conn) {
            return [];
        }
        
        try {
            $query = "SELECT DISTINCT property_type FROM " . $this->table_name . " WHERE property_type IS NOT NULL ORDER BY property_type";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get price range
    public function getPriceRange() {
        if (!$this->conn) {
            return ['min_price' => 0, 'max_price' => 10000];
        }
        
        try {
            $query = "SELECT MIN(monthly_rent) as min_price, MAX(monthly_rent) as max_price FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['min_price' => 0, 'max_price' => 10000];
        }
    }

    // Get cities
    public function getCities() {
        if (!$this->conn) {
            return [];
        }
        
        try {
            $query = "SELECT DISTINCT city FROM " . $this->table_name . " WHERE city IS NOT NULL ORDER BY city";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get single property by ID
    public function getPropertyById($id) {
        if (!$this->conn) {
            return null;
        }
        
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Get property image
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

    // Format address
    public function formatAddress($property) {
        $parts = [];
        if (!empty($property['address'])) $parts[] = $property['address'];
        if (!empty($property['neighborhood'])) $parts[] = $property['neighborhood'];
        if (!empty($property['city'])) $parts[] = $property['city'];
        
        return implode(', ', $parts);
    }
    /**
 * Get property with landlord M-Pesa details
 */
public function getPropertyWithPaymentDetails($property_id) {
    if (!$this->conn) {
        return null;
    }

    try {
        $query = "SELECT p.*, u.mpesa_number, u.mpesa_business_shortcode 
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
}
?>