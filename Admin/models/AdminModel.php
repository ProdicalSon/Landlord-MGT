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

    public function login($username, $password) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE (username = :username OR email = :username) AND is_active = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $admin['password_hash'])) {
                    // Update last login
                    $this->updateLastLogin($admin['id']);
                    
                    // Remove sensitive data
                    unset($admin['password_hash']);
                    
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'admin' => $admin
                    ];
                } else {
                    return ['success' => false, 'message' => 'Invalid password'];
                }
            } else {
                return ['success' => false, 'message' => 'Admin not found'];
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function getAdminById($id) {
        if (!$this->conn) return null;

        try {
            $query = "SELECT id, username, email, first_name, last_name, role, 
                             profile_image, last_login, created_at 
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

    public function updateProfile($id, $data) {
        if (!$this->conn) return false;

        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET first_name = :first_name, last_name = :last_name, 
                          email = :email, updated_at = NOW() 
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

    public function changePassword($id, $current, $new) {
        if (!$this->conn) return false;

        try {
            // Verify current password
            $query = "SELECT password_hash FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($current, $admin['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Update password
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table_name . " SET password_hash = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $new_hash);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            }
            return ['success' => false, 'message' => 'Failed to change password'];
        } catch (PDOException $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

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

public function getRecentActivities($limit = 10) {
    if (!$this->conn) {
        return [];
    }

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
}
?>