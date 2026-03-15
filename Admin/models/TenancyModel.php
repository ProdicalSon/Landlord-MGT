<?php
// admin/models/TenancyModel.php
require_once __DIR__ . '/../config/database.php';

class TenancyModel {
    private $conn;
    private $table_name = "tenancies";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Create table if it doesn't exist
        $this->createTable();
    }

    /**
     * Create tenancies table if it doesn't exist
     */
    private function createTable() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS tenancies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                property_id INT NOT NULL,
                student_id INT NOT NULL,
                landlord_id INT NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                monthly_rent DECIMAL(10,2) NOT NULL,
                deposit_paid BOOLEAN DEFAULT FALSE,
                status ENUM('active', 'expired', 'terminated') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_student (student_id),
                INDEX idx_property (property_id),
                INDEX idx_landlord (landlord_id),
                INDEX idx_status (status)
            )";
            $this->conn->exec($query);
        } catch (PDOException $e) {
            error_log("Create tenancies table error: " . $e->getMessage());
        }
    }

    /**
     * Get active tenancy by student ID
     * @param int $student_id The student ID
     * @return array|null Tenancy data or null if not found
     */
    public function getActiveTenancyByStudent($student_id) {
        if (!$this->conn) return null;
        
        try {
            $query = "SELECT t.*, 
                             p.property_name, 
                             p.address, 
                             p.city, 
                             p.neighborhood,
                             u.first_name as landlord_first_name,
                             u.last_name as landlord_last_name,
                             u.phone_number as landlord_phone,
                             u.email as landlord_email
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u ON t.landlord_id = u.id
                      WHERE t.student_id = :student_id 
                        AND t.status = 'active'
                        AND t.end_date >= CURDATE()
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get active tenancy by student error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get active tenancy by property ID
     * @param int $property_id The property ID
     * @return array|null Tenancy data or null if not found
     */
    public function getActiveTenancyByProperty($property_id) {
        if (!$this->conn) return null;
        
        try {
            $query = "SELECT t.*, 
                             u.username as student_username,
                             u.first_name as student_first_name,
                             u.last_name as student_last_name,
                             u.email as student_email,
                             u.phone_number as student_phone
                      FROM " . $this->table_name . " t
                      LEFT JOIN users u ON t.student_id = u.id
                      WHERE t.property_id = :property_id 
                        AND t.status = 'active'
                        AND t.end_date >= CURDATE()
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get active tenancy by property error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all tenancies for a student
     * @param int $student_id The student ID
     * @return array List of tenancies
     */
    public function getStudentTenancies($student_id) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT t.*, 
                             p.property_name, 
                             p.address, 
                             p.city,
                             DATEDIFF(t.end_date, CURDATE()) as days_remaining
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      WHERE t.student_id = :student_id
                      ORDER BY t.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get student tenancies error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all tenancies for a landlord
     * @param int $landlord_id The landlord ID
     * @return array List of tenancies
     */
    public function getLandlordTenancies($landlord_id) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT t.*, 
                             p.property_name,
                             p.address,
                             p.city,
                             u.username as student_username,
                             u.first_name as student_first_name,
                             u.last_name as student_last_name,
                             u.email as student_email,
                             u.phone_number as student_phone
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u ON t.student_id = u.id
                      WHERE t.landlord_id = :landlord_id
                      ORDER BY t.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlord_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get landlord tenancies error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all tenancies with pagination
     * @param int $limit Number of records per page
     * @param int $offset Offset for pagination
     * @return array List of tenancies
     */
    public function getAllTenancies($limit = 20, $offset = 0) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT t.*, 
                             p.property_name,
                             p.address,
                             p.city,
                             u_student.username as student_username,
                             u_student.first_name as student_first_name,
                             u_student.last_name as student_last_name,
                             u_landlord.username as landlord_username,
                             u_landlord.first_name as landlord_first_name,
                             u_landlord.last_name as landlord_last_name
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u_student ON t.student_id = u_student.id
                      LEFT JOIN users u_landlord ON t.landlord_id = u_landlord.id
                      ORDER BY t.created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all tenancies error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total tenancies
     * @return int Total number of tenancies
     */
    public function countTenancies() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count tenancies error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count active tenancies
     * @return int Number of active tenancies
     */
    public function countActiveTenancies() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                      WHERE status = 'active' AND end_date >= CURDATE()";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count active tenancies error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count expired tenancies
     * @return int Number of expired tenancies
     */
    public function countExpiredTenancies() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                      WHERE end_date < CURDATE() OR status = 'expired'";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count expired tenancies error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get tenancy by ID
     * @param int $id Tenancy ID
     * @return array|null Tenancy data
     */
    public function getTenancyById($id) {
        if (!$this->conn) return null;
        
        try {
            $query = "SELECT t.*, 
                             p.property_name,
                             p.address,
                             p.city,
                             p.neighborhood,
                             p.monthly_rent as property_rent,
                             u_student.username as student_username,
                             u_student.first_name as student_first_name,
                             u_student.last_name as student_last_name,
                             u_student.email as student_email,
                             u_student.phone_number as student_phone,
                             u_landlord.username as landlord_username,
                             u_landlord.first_name as landlord_first_name,
                             u_landlord.last_name as landlord_last_name,
                             u_landlord.email as landlord_email,
                             u_landlord.phone_number as landlord_phone
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u_student ON t.student_id = u_student.id
                      LEFT JOIN users u_landlord ON t.landlord_id = u_landlord.id
                      WHERE t.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get tenancy by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new tenancy
     * @param array $data Tenancy data
     * @return array Result with success/error message
     */
    public function createTenancy($data) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // Check if property is already tenanted
            $existing = $this->getActiveTenancyByProperty($data['property_id']);
            if ($existing) {
                return ['success' => false, 'message' => 'This property already has an active tenancy'];
            }

            $query = "INSERT INTO " . $this->table_name . " 
                      (property_id, student_id, landlord_id, start_date, end_date, 
                       monthly_rent, deposit_paid, status, created_at, updated_at) 
                      VALUES 
                      (:property_id, :student_id, :landlord_id, :start_date, :end_date,
                       :monthly_rent, :deposit_paid, :status, NOW(), NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $data['property_id']);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':landlord_id', $data['landlord_id']);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':monthly_rent', $data['monthly_rent']);
            $stmt->bindParam(':deposit_paid', $data['deposit_paid']);
            $stmt->bindParam(':status', $data['status']);
            
            if ($stmt->execute()) {
                $tenancy_id = $this->conn->lastInsertId();
                return [
                    'success' => true,
                    'message' => 'Tenancy created successfully',
                    'tenancy_id' => $tenancy_id
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to create tenancy'];
            }
        } catch (PDOException $e) {
            error_log("Create tenancy error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Update tenancy
     * @param int $id Tenancy ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateTenancy($id, $data) {
        if (!$this->conn) return false;
        
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET end_date = :end_date,
                          monthly_rent = :monthly_rent,
                          deposit_paid = :deposit_paid,
                          status = :status,
                          updated_at = NOW()
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':monthly_rent', $data['monthly_rent']);
            $stmt->bindParam(':deposit_paid', $data['deposit_paid']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update tenancy error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Terminate tenancy
     * @param int $id Tenancy ID
     * @return bool Success status
     */
    public function terminateTenancy($id) {
        if (!$this->conn) return false;
        
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = 'terminated', updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Terminate tenancy error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete tenancy
     * @param int $id Tenancy ID
     * @return bool Success status
     */
    public function deleteTenancy($id) {
        if (!$this->conn) return false;
        
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete tenancy error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get tenancy statistics for dashboard
     * @return array Statistics
     */
    public function getTenancyStats() {
        if (!$this->conn) {
            return [
                'total' => 0,
                'active' => 0,
                'expired' => 0,
                'terminated' => 0
            ];
        }

        try {
            $stats = [];
            
            // Total tenancies
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Active tenancies
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                      WHERE status = 'active' AND end_date >= CURDATE()";
            $stmt = $this->conn->query($query);
            $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Expired tenancies
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                      WHERE end_date < CURDATE() OR status = 'expired'";
            $stmt = $this->conn->query($query);
            $stats['expired'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Terminated tenancies
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'terminated'";
            $stmt = $this->conn->query($query);
            $stats['terminated'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get tenancy stats error: " . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'expired' => 0,
                'terminated' => 0
            ];
        }
    }

    /**
     * Get upcoming expiring tenancies
     * @param int $days Number of days to look ahead
     * @return array List of tenancies expiring soon
     */
    public function getExpiringTenancies($days = 30) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT t.*, 
                             p.property_name,
                             u.username as student_username,
                             u.first_name as student_first_name,
                             u.last_name as student_last_name,
                             u.email as student_email,
                             u.phone_number as student_phone,
                             DATEDIFF(t.end_date, CURDATE()) as days_remaining
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u ON t.student_id = u.id
                      WHERE t.status = 'active' 
                        AND t.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                      ORDER BY t.end_date ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get expiring tenancies error: " . $e->getMessage());
            return [];
        }
    }
    /**
 * Count terminated tenancies
 */
public function countTerminatedTenancies() {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'terminated'";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count terminated tenancies error: " . $e->getMessage());
        return 0;
    }
}

}