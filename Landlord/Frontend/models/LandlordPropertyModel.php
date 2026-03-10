<?php
// Landlord/Frontend/models/LandlordPropertyModel.php
require_once __DIR__ . '/../config/database.php';

class LandlordPropertyModel {
    private $conn;
    private $table_name = "properties";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Add a new property to the database
     */
    public function addProperty($data, $landlord_id) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // Handle amenities - convert array to JSON
            $amenities = isset($data['amenities']) ? json_encode($data['amenities']) : null;
            
            // Handle featured status
            $featured = isset($data['featured']) ? (int)$data['featured'] : 0;
            
            // Handle year built
            $year_built = isset($data['year_built']) && !empty($data['year_built']) ? $data['year_built'] : null;
            
            // Handle zip code
            $zip = isset($data['zip']) && !empty($data['zip']) ? $data['zip'] : null;
            
            $query = "INSERT INTO " . $this->table_name . " 
                      (landlord_id, property_name, property_type, address, city, neighborhood, zip_code,
                       monthly_rent, status, bedrooms, bathrooms, sqft, year_built, featured, description, 
                       amenities, created_at, updated_at) 
                      VALUES 
                      (:landlord_id, :property_name, :property_type, :address, :city, :neighborhood, :zip_code,
                       :monthly_rent, :status, :bedrooms, :bathrooms, :sqft, :year_built, :featured, :description,
                       :amenities, NOW(), NOW())";

            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->bindParam(':property_name', $data['title']);
            $stmt->bindParam(':property_type', $data['type']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':neighborhood', $data['neighborhood']);
            $stmt->bindParam(':zip_code', $zip);
            $stmt->bindParam(':monthly_rent', $data['price']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':bedrooms', $data['bedrooms']);
            $stmt->bindParam(':bathrooms', $data['bathrooms']);
            $stmt->bindParam(':sqft', $data['area']);
            $stmt->bindParam(':year_built', $year_built);
            $stmt->bindParam(':featured', $featured);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':amenities', $amenities);

            if ($stmt->execute()) {
                $property_id = $this->conn->lastInsertId();
                
                return [
                    'success' => true,
                    'message' => 'Property added successfully!',
                    'property_id' => $property_id
                ];
            } else {
                $errorInfo = $stmt->errorInfo();
                return ['success' => false, 'message' => 'Database error: ' . $errorInfo[2]];
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in addProperty: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get all properties for a specific landlord
     */
    public function getLandlordProperties($landlord_id) {
        if (!$this->conn) {
            return [];
        }

        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE landlord_id = :landlord_id 
                      ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get landlord properties error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single property by ID
     */
    public function getPropertyById($property_id) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $property_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Get property by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing property
     */
    public function updateProperty($property_id, $data) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $amenities = isset($data['amenities']) ? json_encode($data['amenities']) : null;
            $year_built = isset($data['year_built']) && !empty($data['year_built']) ? $data['year_built'] : null;
            $zip = isset($data['zip']) && !empty($data['zip']) ? $data['zip'] : null;
            
            $query = "UPDATE " . $this->table_name . " 
                      SET property_name = :property_name,
                          property_type = :property_type,
                          address = :address,
                          city = :city,
                          neighborhood = :neighborhood,
                          zip_code = :zip_code,
                          monthly_rent = :monthly_rent,
                          status = :status,
                          bedrooms = :bedrooms,
                          bathrooms = :bathrooms,
                          sqft = :sqft,
                          year_built = :year_built,
                          description = :description,
                          amenities = :amenities,
                          updated_at = NOW()
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':property_name', $data['title']);
            $stmt->bindParam(':property_type', $data['type']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':neighborhood', $data['neighborhood']);
            $stmt->bindParam(':zip_code', $zip);
            $stmt->bindParam(':monthly_rent', $data['price']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':bedrooms', $data['bedrooms']);
            $stmt->bindParam(':bathrooms', $data['bathrooms']);
            $stmt->bindParam(':sqft', $data['area']);
            $stmt->bindParam(':year_built', $year_built);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':amenities', $amenities);
            $stmt->bindParam(':id', $property_id);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Property updated successfully!'];
            } else {
                $errorInfo = $stmt->errorInfo();
                return ['success' => false, 'message' => 'Failed to update property: ' . $errorInfo[2]];
            }
        } catch (PDOException $e) {
            error_log("Update property error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a property
     */
    public function deleteProperty($property_id) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // First delete associated images
            $this->deletePropertyImages($property_id);
            
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $property_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Property deleted successfully!'];
            } else {
                $errorInfo = $stmt->errorInfo();
                return ['success' => false, 'message' => 'Failed to delete property: ' . $errorInfo[2]];
            }
        } catch (PDOException $e) {
            error_log("Delete property error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete property images
     */
    private function deletePropertyImages($property_id) {
        try {
            // Get image paths to delete files
            $query = "SELECT image_path FROM property_images WHERE property_id = :property_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Delete files from server
            foreach ($images as $image) {
                $filePath = __DIR__ . '/../' . $image['image_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Delete from database
            $query = "DELETE FROM property_images WHERE property_id = :property_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete property images error: " . $e->getMessage());
        }
    }

    /**
     * Get property statistics for dashboard
     */
    public function getPropertyStats($landlord_id) {
        if (!$this->conn) {
            return [
                'total' => 0,
                'available' => 0,
                'occupied' => 0,
                'maintenance' => 0
            ];
        }

        try {
            $stats = [
                'total' => 0,
                'available' => 0,
                'occupied' => 0,
                'maintenance' => 0
            ];

            // Get total count
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE landlord_id = :landlord_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total'] = $result ? (int)$result['count'] : 0;

            // Get counts by status
            $statuses = ['available', 'occupied', 'maintenance'];
            foreach ($statuses as $status) {
                $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                          WHERE landlord_id = :landlord_id AND status = :status";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':landlord_id', $landlord_id);
                $stmt->bindParam(':status', $status);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stats[$status] = $result ? (int)$result['count'] : 0;
            }

            return $stats;
        } catch (PDOException $e) {
            error_log("Get property stats error: " . $e->getMessage());
            return [
                'total' => 0,
                'available' => 0,
                'occupied' => 0,
                'maintenance' => 0
            ];
        }
    }

    /**
     * Get monthly revenue for landlord
     */
    public function getMonthlyRevenue($landlord_id) {
        if (!$this->conn) {
            return 0;
        }

        try {
            $query = "SELECT SUM(monthly_rent) as total FROM " . $this->table_name . " 
                      WHERE landlord_id = :landlord_id AND status = 'occupied'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (float)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Get monthly revenue error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get properties by status
     */
    public function getPropertiesByStatus($landlord_id, $status) {
        if (!$this->conn) {
            return [];
        }

        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE landlord_id = :landlord_id AND status = :status 
                      ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get properties by status error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search properties
     */
    public function searchProperties($landlord_id, $search_term) {
        if (!$this->conn) {
            return [];
        }

        try {
            $search_term = '%' . $search_term . '%';
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE landlord_id = :landlord_id 
                      AND (property_name LIKE :search 
                           OR address LIKE :search 
                           OR city LIKE :search 
                           OR neighborhood LIKE :search
                           OR description LIKE :search)
                      ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->bindParam(':search', $search_term);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search properties error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Format property data for display
     */
    public function formatPropertyForDisplay($property) {
        if (empty($property)) {
            return $property;
        }

        // Decode amenities JSON
        if (!empty($property['amenities'])) {
            $property['amenities_list'] = json_decode($property['amenities'], true);
        } else {
            $property['amenities_list'] = [];
        }

        // Format price
        $property['formatted_price'] = 'KES ' . number_format($property['monthly_rent'], 0);

        // Format address
        $address_parts = [];
        if (!empty($property['address'])) $address_parts[] = $property['address'];
        if (!empty($property['neighborhood'])) $address_parts[] = $property['neighborhood'];
        if (!empty($property['city'])) $address_parts[] = $property['city'];
        $property['full_address'] = implode(', ', $address_parts);

        // Format dates
        $property['created_formatted'] = date('M j, Y', strtotime($property['created_at']));
        $property['updated_formatted'] = date('M j, Y', strtotime($property['updated_at']));

        return $property;
    }

    /**
     * Check if landlord owns the property
     */
    public function verifyOwnership($property_id, $landlord_id) {
        if (!$this->conn) {
            return false;
        }

        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE id = :property_id AND landlord_id = :landlord_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Verify ownership error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save property image to database
     */
    public function savePropertyImage($property_id, $image_path, $is_primary = 0) {
        if (!$this->conn) {
            return false;
        }

        try {
            // Create property_images table if it doesn't exist
            $this->createPropertyImagesTable();
            
            // If this is primary, remove primary status from other images
            if ($is_primary) {
                $updateQuery = "UPDATE property_images SET is_primary = 0 WHERE property_id = :property_id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':property_id', $property_id);
                $updateStmt->execute();
            }
            
            $query = "INSERT INTO property_images (property_id, image_path, is_primary, created_at) 
                      VALUES (:property_id, :image_path, :is_primary, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->bindParam(':image_path', $image_path);
            $stmt->bindParam(':is_primary', $is_primary);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Save property image error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get property images
     */
    public function getPropertyImages($property_id) {
        if (!$this->conn) {
            return [];
        }

        try {
            $query = "SELECT * FROM property_images WHERE property_id = :property_id ORDER BY is_primary DESC, created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get property images error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get primary property image
     */
    public function getPrimaryPropertyImage($property_id) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT image_path FROM property_images 
                      WHERE property_id = :property_id AND is_primary = 1 
                      LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['image_path'];
            }
            
            // Return first image if no primary
            $query = "SELECT image_path FROM property_images WHERE property_id = :property_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['image_path'];
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Get primary image error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create property_images table if it doesn't exist
     */
    private function createPropertyImagesTable() {
        $query = "CREATE TABLE IF NOT EXISTS property_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
        )";
        
        try {
            $this->conn->exec($query);
        } catch (PDOException $e) {
            error_log("Create property_images table error: " . $e->getMessage());
        }
    }
    /**
     * Update property status
     * @param int $property_id
     * @param string $status
     * @return array
     */
    public function updatePropertyStatus($property_id, $status) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $property_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Status updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update status'];
            }
        } catch (PDOException $e) {
            error_log("Update status error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
}
?>