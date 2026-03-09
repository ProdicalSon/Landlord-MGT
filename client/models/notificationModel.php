<?php
// models/NotificationModel.php
require_once __DIR__ . '/../config/database.php';

class NotificationModel {
    private $conn;
    private $table_name = "notifications";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get user notifications
    public function getUserNotifications($user_id) {
        $query = "SELECT n.*, p.property_name, p.id as property_id 
                  FROM " . $this->table_name . " n
                  LEFT JOIN properties p ON n.property_id = p.id
                  WHERE n.user_id = :user_id 
                  ORDER BY n.created_at DESC 
                  LIMIT 50";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unread count
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }

    // Mark as read
    public function markAsRead($notification_id, $user_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1, read_at = NOW() 
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    // Mark all as read
    public function markAllAsRead($user_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1, read_at = NOW() 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    // Add reply to notification
    public function addReply($notification_id, $user_id, $message) {
        // First, check if notification_replies table exists
        try {
            $query = "INSERT INTO notification_replies (notification_id, user_id, message, created_at) 
                      VALUES (:notification_id, :user_id, :message, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':notification_id', $notification_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':message', $message);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // If table doesn't exist, create it
            $this->createRepliesTable();
            
            // Try again
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':notification_id', $notification_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':message', $message);
            
            return $stmt->execute();
        }
    }

    // Get replies for notification
    public function getReplies($notification_id) {
        try {
            $query = "SELECT nr.*, 'You' as user_name 
                      FROM notification_replies nr
                      WHERE nr.notification_id = :notification_id 
                      ORDER BY nr.created_at ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':notification_id', $notification_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Create notification
    public function create($user_id, $type, $message, $property_id = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, type, message, property_id, created_at) 
                  VALUES (:user_id, :type, :message, :property_id, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':property_id', $property_id);
        
        return $stmt->execute();
    }

    // Create replies table if it doesn't exist
    private function createRepliesTable() {
        $query = "CREATE TABLE IF NOT EXISTS notification_replies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            notification_id INT NOT NULL,
            user_id INT NOT NULL,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
        )";
        
        $this->conn->exec($query);
    }
}
?>