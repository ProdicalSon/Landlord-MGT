<?php
// admin/models/AdminModel.php
require_once __DIR__ . '/../config/database.php';

class AdminModel {
    private $conn;
    private $table_name = "admins";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Login admin
     */
    public function login($username, $password) {
        if (!$this->conn) {
            error_log("Admin login: Database connection failed");
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            error_log("Admin login attempt for username: " . $username);
            
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE (username = :username OR email = :username) AND is_active = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("Admin found: " . $admin['username']);
                
                if (password_verify($password, $admin['password_hash'])) {
                    // Update last login
                    $this->updateLastLogin($admin['id']);
                    
                    // Remove sensitive data
                    unset($admin['password_hash']);
                    
                    error_log("Admin login successful for: " . $admin['username']);
                    
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'admin' => $admin
                    ];
                } else {
                    error_log("Admin login: Invalid password for user: " . $username);
                    return ['success' => false, 'message' => 'Invalid password'];
                }
            } else {
                error_log("Admin login: User not found: " . $username);
                return ['success' => false, 'message' => 'Admin not found'];
            }
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Get admin by ID
     */
    public function getAdminById($id) {
        if (!$this->conn) return null;

        try {
            $query = "SELECT id, username, email, first_name, last_name, role, 
                             profile_image, last_login, created_at, updated_at 
                      FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get admin error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update admin profile - SINGLE VERSION
     */
    public function updateProfile($id, $data) {
        if (!$this->conn) return false;

        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET first_name = :first_name, 
                          last_name = :last_name, 
                          email = :email, 
                          updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update profile error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Change admin password
     */
    public function changePassword($id, $current, $new) {
        if (!$this->conn) return ['success' => false, 'message' => 'Database connection failed'];

        try {
            // Verify current password
            $query = "SELECT password_hash FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                return ['success' => false, 'message' => 'Admin not found'];
            }
            
            if (!password_verify($current, $admin['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Update password
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table_name . " SET password_hash = :password, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $new_hash);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            }
            return ['success' => false, 'message' => 'Failed to change password'];
        } catch (PDOException $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Update last login
     */
    private function updateLastLogin($id) {
        try {
            $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        if (!$this->conn) return [];

        try {
            $stats = [];
            
            // Total users
            $query = "SELECT COUNT(*) as count FROM users";
            $stmt = $this->conn->query($query);
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Total landlords
            $query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'landlord'";
            $stmt = $this->conn->query($query);
            $stats['total_landlords'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Total students
            $query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'student'";
            $stmt = $this->conn->query($query);
            $stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Total properties
            $query = "SELECT COUNT(*) as count FROM properties";
            $stmt = $this->conn->query($query);
            $stats['total_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Available properties
            $query = "SELECT COUNT(*) as count FROM properties WHERE status = 'available'";
            $stmt = $this->conn->query($query);
            $stats['available_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Occupied properties
            $query = "SELECT COUNT(*) as count FROM properties WHERE status = 'occupied'";
            $stmt = $this->conn->query($query);
            $stats['occupied_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Total payments
            $query = "SELECT COUNT(*) as count, COALESCE(SUM(amount_paid), 0) as total FROM payments";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_payments'] = $result['count'];
            $stats['total_revenue'] = $result['total'];
            
            // Pending payments
            $query = "SELECT COUNT(*) as count, COALESCE(SUM(amount_due), 0) as total FROM payments WHERE status = 'pending'";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['pending_payments'] = $result['count'];
            $stats['pending_amount'] = $result['total'];
            
            // Recent users (last 7 days)
            $query = "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $stmt = $this->conn->query($query);
            $stats['new_users_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Recent properties
            $query = "SELECT COUNT(*) as count FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $stmt = $this->conn->query($query);
            $stats['new_properties_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get dashboard stats error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 10) {
        if (!$this->conn) return [];

        try {
            $activities = [];
            
            // Recent users
            $query = "SELECT 'user' as type, id, username as name, created_at 
                      FROM users ORDER BY created_at DESC LIMIT " . intval($limit/2);
            $stmt = $this->conn->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $activities[] = $row;
            }
            
            // Recent properties
            $query = "SELECT 'property' as type, id, property_name as name, created_at 
                      FROM properties ORDER BY created_at DESC LIMIT " . intval($limit/2);
            $stmt = $this->conn->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $activities[] = $row;
            }
            
            // Sort by date
            usort($activities, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return array_slice($activities, 0, $limit);
        } catch (PDOException $e) {
            error_log("Get recent activities error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all admins (for super admin)
     */
    public function getAllAdmins($limit = 20, $offset = 0) {
        if (!$this->conn) return [];

        try {
            $query = "SELECT id, username, email, first_name, last_name, role, 
                             last_login, is_active, created_at 
                      FROM " . $this->table_name . " 
                      ORDER BY created_at DESC 
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all admins error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total admins
     */
    public function countAdmins() {
        if (!$this->conn) return 0;

        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count admins error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create new admin (super admin only)
     */
    public function createAdmin($data) {
        if (!$this->conn) return ['success' => false, 'message' => 'Database connection failed'];

        try {
            // Check if username or email exists
            $check = $this->conn->prepare("SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email");
            $check->bindParam(':username', $data['username']);
            $check->bindParam(':email', $data['email']);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO " . $this->table_name . " 
                      (username, email, password_hash, first_name, last_name, role, is_active, created_at) 
                      VALUES 
                      (:username, :email, :hash, :first_name, :last_name, :role, 1, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':hash', $hash);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':role', $data['role']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Admin created successfully', 'id' => $this->conn->lastInsertId()];
            }
            return ['success' => false, 'message' => 'Failed to create admin'];
        } catch (PDOException $e) {
            error_log("Create admin error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Update admin status (activate/deactivate)
     */
    public function updateAdminStatus($id, $is_active) {
        if (!$this->conn) return false;

        try {
            $query = "UPDATE " . $this->table_name . " SET is_active = :is_active, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update admin status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete admin (super admin only)
     */
    public function deleteAdmin($id) {
        if (!$this->conn) return false;

        try {
            // Don't allow deleting yourself
            if ($id == $_SESSION['admin_id']) {
                return false;
            }
            
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete admin error: " . $e->getMessage());
            return false;
        }
    }
    /**
 * Update profile with image
 */
public function updateProfileWithImage($id, $data, $imageFile = null) {
    if (!$this->conn) return ['success' => false, 'message' => 'Database connection failed'];

    try {
        // Start building the query
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      email = :email, 
                      updated_at = NOW()";
        
        $params = [
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':id' => $id
        ];
        
        // Add profile image if provided
        if ($imageFile && isset($imageFile['name']) && !empty($imageFile['name'])) {
            // Upload image
            $uploadResult = $this->uploadProfileImage($imageFile, $id);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            
            $query .= ", profile_image = :profile_image";
            $params[':profile_image'] = $uploadResult['filename'];
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update profile'];
    } catch (PDOException $e) {
        error_log("Update profile with image error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Upload profile image
 */
/**
 * Upload profile image
 */
private function uploadProfileImage($file, $admin_id) {
    // Define upload directory - relative to the admin folder
    // This will create: C:\xampp\htdocs\Landlord-MGT\Admin\uploads\profiles\
    $uploadDir = __DIR__ . '/../uploads/profiles/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMessage = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        return ['success' => false, 'message' => 'Upload error: ' . $errorMessage];
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'];
    }
    
    // Validate file size (max 2MB)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 2MB.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'admin_' . $admin_id . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Delete old profile image if exists
        $this->deleteOldProfileImage($admin_id);
        
        // IMPORTANT: Store path relative to the admin folder
        // This will be 'uploads/profiles/filename.jpg'
        $dbPath = 'uploads/profiles/' . $filename;
        
        return [
            'success' => true,
            'filename' => $dbPath,
            'filepath' => $filepath
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to save uploaded file'];
}

/**
 * Delete old profile image
 */

private function deleteOldProfileImage($admin_id) {
    try {
        // Get current profile image
        $query = "SELECT profile_image FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $admin_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['profile_image'])) {
            // Construct the full file path relative to admin folder
            $oldFile = __DIR__ . '/../' . $result['profile_image'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    } catch (PDOException $e) {
        error_log("Delete old profile image error: " . $e->getMessage());
    }
}

/**
 * Remove profile image
 */
public function removeProfileImage($admin_id) {
    if (!$this->conn) return false;
    
    try {
        // Delete the file
        $this->deleteOldProfileImage($admin_id);
        
        // Update database
        $query = "UPDATE " . $this->table_name . " SET profile_image = NULL, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $admin_id);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Remove profile image error: " . $e->getMessage());
        return false;
    }
}
}
