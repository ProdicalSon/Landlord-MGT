<?php
// Landlord/Frontend/models/LandlordUserModel.php
require_once __DIR__ . '/../config/database.php';

class LandlordUserModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Register a new landlord
     */
    public function register($username, $email, $password, $first_name = '', $last_name = '', $phone_number = '') {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        // Check if username already exists
        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username already taken'];
        }

        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Generate verification token
        $verification_token = bin2hex(random_bytes(32));
        $verification_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // User type is always 'landlord' for this registration
        $user_type = 'landlord';

        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      (username, email, password_hash, user_type, first_name, last_name, phone_number, 
                       verification_token, verification_expires, created_at, updated_at) 
                      VALUES 
                      (:username, :email, :password_hash, :user_type, :first_name, :last_name, :phone_number,
                       :verification_token, :verification_expires, NOW(), NOW())";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':user_type', $user_type);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':verification_token', $verification_token);
            $stmt->bindParam(':verification_expires', $verification_expires);

            if ($stmt->execute()) {
                $user_id = $this->conn->lastInsertId();
                
                // TODO: Send verification email
                
                return [
                    'success' => true, 
                    'message' => 'Registration successful! Please check your email to verify your account.',
                    'user_id' => $user_id
                ];
            } else {
                return ['success' => false, 'message' => 'Registration failed'];
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Login landlord
     */
    public function login($username_or_email, $password, $remember = false) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // Get client IP address
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            
            // Check if input is email or username
            $field = filter_var($username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE ($field = :credential) AND user_type = 'landlord'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':credential', $username_or_email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                $password_valid = password_verify($password, $user['password_hash']);
                
                // Record login attempt
                $this->recordLoginAttempt($user['id'], $username_or_email, $ip_address, $password_valid);
                
                // Check if account is verified
                if (!$user['is_verified']) {
                    return ['success' => false, 'message' => 'Please verify your email before logging in'];
                }
                
                if ($password_valid) {
                    // Update last login
                    $this->updateLastLogin($user['id']);
                    
                    // Remove sensitive data
                    unset($user['password_hash']);
                    unset($user['verification_token']);
                    unset($user['reset_token']);
                    
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => $user
                    ];
                } else {
                    return ['success' => false, 'message' => 'Invalid password'];
                }
            } else {
                // Record failed attempt for non-existent user
                $this->recordLoginAttempt(null, $username_or_email, $ip_address, false);
                return ['success' => false, 'message' => 'User not found or not a landlord'];
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Get landlord by ID
     */
    public function getLandlordById($user_id) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT id, username, email, user_type, first_name, last_name, phone_number, 
                             profile_image, is_verified, created_at 
                      FROM " . $this->table_name . " 
                      WHERE id = :id AND user_type = 'landlord'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Get landlord error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update landlord profile
     */
    public function updateProfile($user_id, $data) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        $allowed_fields = ['first_name', 'last_name', 'phone_number', 'profile_image'];
        $updates = [];
        $params = [':id' => $user_id];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }

        $updates[] = "updated_at = NOW()";

        if (empty($updates)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }

        try {
            $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                return ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Update failed'];
            }
        } catch (PDOException $e) {
            error_log("Update profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Change password
     */
    public function changePassword($user_id, $current_password, $new_password) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // Get current password hash
            $query = "SELECT password_hash FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($current_password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Update to new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table_name . " 
                      SET password_hash = :password_hash, updated_at = NOW() 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password_hash', $new_password_hash);
            $stmt->bindParam(':id', $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'message' => 'Password change failed'];
            }
        } catch (PDOException $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        if (!$this->conn) {
            return false;
        }

        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Username check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email exists
     */
    public function emailExists($email) {
        if (!$this->conn) {
            return false;
        }

        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail($token) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE verification_token = :token AND verification_expires > NOW() AND is_verified = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $update = "UPDATE " . $this->table_name . " 
                           SET is_verified = 1, verification_token = NULL, verification_expires = NULL, updated_at = NOW() 
                           WHERE id = :id";
                $stmt = $this->conn->prepare($update);
                $stmt->bindParam(':id', $user['id']);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Email verified successfully! You can now login.'];
                }
            }
            
            return ['success' => false, 'message' => 'Invalid or expired verification token'];
        } catch (PDOException $e) {
            error_log("Email verification error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Get full name
     */
    public function getFullName($user) {
        $parts = [];
        if (!empty($user['first_name'])) $parts[] = $user['first_name'];
        if (!empty($user['last_name'])) $parts[] = $user['last_name'];
        
        if (empty($parts)) {
            return $user['username'];
        }
        
        return implode(' ', $parts);
    }

    /**
     * Record login attempt
     */
    private function recordLoginAttempt($user_id, $username, $ip_address, $success) {
        try {
            // Create login_attempts table if it doesn't exist
            $this->createLoginAttemptsTable();
            
            $query = "INSERT INTO login_attempts (user_id, username, ip_address, success, created_at) 
                      VALUES (:user_id, :username, :ip, :success, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':ip', $ip_address);
            $stmt->bindParam(':success', $success);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Record login attempt error: " . $e->getMessage());
        }
    }

    /**
     * Create login_attempts table
     */
    private function createLoginAttemptsTable() {
        $query = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            username VARCHAR(100),
            ip_address VARCHAR(45),
            success BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip_time (ip_address, created_at),
            INDEX idx_user_time (user_id, created_at)
        )";
        
        try {
            $this->conn->exec($query);
        } catch (PDOException $e) {
            error_log("Create login_attempts table error: " . $e->getMessage());
        }
    }

    /**
     * Update last login
     */
    private function updateLastLogin($user_id) {
        try {
            $query = "UPDATE " . $this->table_name . " SET updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    /**
     * Logout
     */
    public function logout($user_id) {
        // Just destroy session - no need for database action
        return true;
    }
}
?>