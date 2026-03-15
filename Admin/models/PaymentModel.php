<?php
// admin/models/PaymentModel.php
require_once __DIR__ . '/../config/database.php';

class PaymentModel {
    private $conn;
    private $table_name = "payments";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get recent payments
     * @param int $limit Number of payments to return
     * @return array Recent payments
     */
    public function getRecentPayments($limit = 5) {
        if (!$this->conn) {
            return [];
        }

        try {
            $query = "SELECT p.*, pr.property_name 
                      FROM " . $this->table_name . " p
                      LEFT JOIN properties pr ON p.property_id = pr.id
                      ORDER BY p.created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent payments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get payment by ID
     */
    public function getPaymentById($id) {
        if (!$this->conn) return null;
        
        try {
            $query = "SELECT p.*, pr.property_name, u.username as tenant_name 
                      FROM " . $this->table_name . " p
                      LEFT JOIN properties pr ON p.property_id = pr.id
                      LEFT JOIN users u ON p.student_id = u.id
                      WHERE p.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get payment error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all payments with pagination
     */
    public function getAllPayments($limit = 20, $offset = 0) {
        if (!$this->conn) return [];
        
        try {
            $query = "SELECT p.*, pr.property_name, u.username as tenant_name 
                      FROM " . $this->table_name . " p
                      LEFT JOIN properties pr ON p.property_id = pr.id
                      LEFT JOIN users u ON p.student_id = u.id
                      ORDER BY p.created_at DESC 
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all payments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total payments
     */
    public function countPayments() {
        if (!$this->conn) return 0;
        
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Count payments error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats() {
        if (!$this->conn) {
            return [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'failed' => 0,
                'total_amount' => 0
            ];
        }

        try {
            $stats = [];
            
            // Total payments
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->query($query);
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Completed payments
            $query = "SELECT COUNT(*) as count, COALESCE(SUM(amount_paid), 0) as total 
                      FROM " . $this->table_name . " WHERE status = 'completed'";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['completed'] = $result['count'];
            $stats['total_amount'] = $result['total'];
            
            // Pending payments
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'pending'";
            $stmt = $this->conn->query($query);
            $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Failed payments
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'failed'";
            $stmt = $this->conn->query($query);
            $stats['failed'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get payment stats error: " . $e->getMessage());
            return [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'failed' => 0,
                'total_amount' => 0
            ];
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, $status) {
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
            error_log("Update payment status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete payment
     */
    public function deletePayment($id) {
        if (!$this->conn) return false;
        
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete payment error: " . $e->getMessage());
            return false;
        }
    }
    /**
 * Count payments by student
 */
public function countPaymentsByStudent($student_id) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE student_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $student_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Count payments by student error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get total paid by student
 */
public function getTotalPaidByStudent($student_id) {
    if (!$this->conn) return 0;
    
    try {
        $query = "SELECT COALESCE(SUM(amount_paid), 0) as total FROM " . $this->table_name . " 
                  WHERE student_id = :id AND status = 'completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $student_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log("Get total paid by student error: " . $e->getMessage());
        return 0;
    }
}
}
?>