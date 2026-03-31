<?php
// client/models/PaymentModel.php
require_once __DIR__ . '/../config/database.php';

class PaymentModel {
    private $conn;
    private $table_name = "payments";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createPayment($data) {
        try {
            $query = "INSERT INTO payments (property_id, tenant_id, landlord_id, amount, phone_number, status, created_at) 
                      VALUES (:property_id, :tenant_id, :landlord_id, :amount, :phone_number, :status, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $data['property_id']);
            $stmt->bindParam(':tenant_id', $data['tenant_id']);
            $stmt->bindParam(':landlord_id', $data['landlord_id']);
            $stmt->bindParam(':amount', $data['amount']);
            $stmt->bindParam(':phone_number', $data['phone_number']);
            $stmt->bindParam(':status', $data['status']);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Create payment error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentStatus($payment_id, $status, $receipt_number = null) {
        try {
            $query = "UPDATE payments SET status = :status, transaction_date = NOW()";
            if ($receipt_number) {
                $query .= ", mpesa_receipt = :receipt_number";
            }
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $payment_id);
            if ($receipt_number) {
                $stmt->bindParam(':receipt_number', $receipt_number);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update payment error: " . $e->getMessage());
            return false;
        }
    }

    public function getPaymentById($id) {
        try {
            $query = "SELECT * FROM payments WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get payment error: " . $e->getMessage());
            return null;
        }
    }
}
?>