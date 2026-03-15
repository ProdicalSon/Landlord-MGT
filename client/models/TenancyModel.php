<?php
// models/TenancyModel.php
require_once __DIR__ . '/../config/database.php';

class TenancyModel {
    private $conn;
    private $table_name = "tenancies";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create tenancies table if it doesn't exist
     */
    public function createTable() {
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
                FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
                FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (landlord_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            $this->conn->exec($query);
            return true;
        } catch (PDOException $e) {
            error_log("Create tenancies table error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get active tenancy for a student in a property
     */
    public function getActiveTenancy($studentId, $propertyId) {
        if (!$this->conn) {
            return null;
        }

        try {
            // Ensure table exists
            $this->createTable();
            
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE student_id = :student_id 
                        AND property_id = :property_id 
                        AND status = 'active'
                        AND end_date >= CURDATE()
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':property_id', $propertyId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get active tenancy error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all tenancies for a student
     */
    public function getStudentTenancies($studentId) {
        if (!$this->conn) {
            return [];
        }

        try {
            $this->createTable();
            
            $query = "SELECT t.*, p.property_name, p.address, p.city, p.neighborhood,
                             DATEDIFF(t.end_date, CURDATE()) as days_remaining
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      WHERE t.student_id = :student_id
                      ORDER BY t.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get student tenancies error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all tenancies for a landlord
     */
    public function getLandlordTenancies($landlordId) {
        if (!$this->conn) {
            return [];
        }

        try {
            $this->createTable();
            
            $query = "SELECT t.*, p.property_name, p.address, 
                             u.username, u.first_name, u.last_name, u.email, u.phone_number
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u ON t.student_id = u.id
                      WHERE t.landlord_id = :landlord_id
                      ORDER BY t.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlordId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get landlord tenancies error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get tenancy by ID
     */
    public function getTenancyById($tenancyId) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT t.*, p.property_name, p.address, p.city, p.neighborhood,
                             u.username, u.first_name, u.last_name, u.email, u.phone_number
                      FROM " . $this->table_name . " t
                      LEFT JOIN properties p ON t.property_id = p.id
                      LEFT JOIN users u ON t.student_id = u.id
                      WHERE t.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $tenancyId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get tenancy by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new tenancy
     */
    public function createTenancy($data) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $this->createTable();
            
            $query = "INSERT INTO " . $this->table_name . " 
                      (property_id, student_id, landlord_id, start_date, end_date, monthly_rent, deposit_paid, status, created_at, updated_at) 
                      VALUES 
                      (:property_id, :student_id, :landlord_id, :start_date, :end_date, :monthly_rent, :deposit_paid, :status, NOW(), NOW())";
            
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
     * Update tenancy status
     */
    public function updateTenancyStatus($tenancyId, $status) {
        if (!$this->conn) {
            return false;
        }

        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $tenancyId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update tenancy status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if property is currently tenanted
     */
    public function isPropertyTenanted($propertyId) {
        if (!$this->conn) {
            return false;
        }

        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE property_id = :property_id 
                        AND status = 'active'
                        AND end_date >= CURDATE()
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $propertyId);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Check property tenanted error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get tenant for a property
     */
    public function getPropertyTenant($propertyId) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT u.*, t.start_date, t.end_date, t.monthly_rent
                      FROM " . $this->table_name . " t
                      LEFT JOIN users u ON t.student_id = u.id
                      WHERE t.property_id = :property_id 
                        AND t.status = 'active'
                        AND t.end_date >= CURDATE()
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $propertyId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get property tenant error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get tenancy statistics for dashboard
     */
    public function getTenancyStats($userId, $userType = 'landlord') {
        if (!$this->conn) {
            return [];
        }

        try {
            $field = $userType == 'landlord' ? 'landlord_id' : 'student_id';
            
            $query = "SELECT 
                        COUNT(*) as total_tenancies,
                        COUNT(CASE WHEN status = 'active' THEN 1 END) as active_tenancies,
                        COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_tenancies,
                        COUNT(CASE WHEN status = 'terminated' THEN 1 END) as terminated_tenancies,
                        COUNT(CASE WHEN end_date < CURDATE() AND status = 'active' THEN 1 END) as overdue_tenancies
                      FROM " . $this->table_name . " 
                      WHERE $field = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get tenancy stats error: " . $e->getMessage());
            return [
                'total_tenancies' => 0,
                'active_tenancies' => 0,
                'expired_tenancies' => 0,
                'terminated_tenancies' => 0,
                'overdue_tenancies' => 0
            ];
        }
    }
}
?>