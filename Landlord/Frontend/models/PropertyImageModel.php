<?php
// models/PropertyImageModel.php
require_once __DIR__ . '/../config/database.php';

class PropertyImageModel {
    private $conn;
    private $table_name = "property_images";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->createTable();
    }

    // Create property_images table if it doesn't exist
    private function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS property_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_property (property_id)
        )";
        try {
            $this->conn->exec($query);
        } catch (PDOException $e) {
            error_log("Create property_images table error: " . $e->getMessage());
        }
    }

    // Upload multiple images
    public function uploadMultipleImages($files, $property_id) {
        $uploadDir = __DIR__ . '/../uploads/properties/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directory'];
            }
        }

        $uploaded = [];
        $errors = [];

        // Process each file
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];

                $result = $this->uploadSingleImage($file, $property_id, empty($uploaded));
                
                if ($result['success']) {
                    $uploaded[] = $result['filename'];
                } else {
                    $errors[] = $result['message'];
                }
            }
        }

        if (count($uploaded) > 0) {
            return [
                'success' => true,
                'uploaded' => $uploaded,
                'count' => count($uploaded),
                'message' => count($errors) > 0 ? 'Some images failed: ' . implode(', ', $errors) : 'All images uploaded successfully'
            ];
        }

        return ['success' => false, 'message' => 'No images were uploaded'];
    }

    // Upload single image
    private function uploadSingleImage($file, $property_id, $is_primary = false) {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type for ' . $file['name']];
        }

        // Validate file size (max 5MB per image)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File too large: ' . $file['name']];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'property_' . $property_id . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = __DIR__ . '/../uploads/properties/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save to database
            $query = "INSERT INTO " . $this->table_name . " (property_id, image_path, is_primary, sort_order) 
                      VALUES (:property_id, :image_path, :is_primary, :sort_order)";
            
            $stmt = $this->conn->prepare($query);
            $image_path = 'uploads/properties/' . $filename;
            $sort_order = $is_primary ? 0 : 1;
            
            $stmt->bindParam(':property_id', $property_id);
            $stmt->bindParam(':image_path', $image_path);
            $stmt->bindParam(':is_primary', $is_primary, PDO::PARAM_BOOL);
            $stmt->bindParam(':sort_order', $sort_order);
            
            if ($stmt->execute()) {
                return ['success' => true, 'filename' => $filename];
            } else {
                return ['success' => false, 'message' => 'Database error for ' . $file['name']];
            }
        }

        return ['success' => false, 'message' => 'Failed to upload ' . $file['name']];
    }

    // Get images for a property
    public function getPropertyImages($property_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE property_id = :property_id ORDER BY is_primary DESC, sort_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $property_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>