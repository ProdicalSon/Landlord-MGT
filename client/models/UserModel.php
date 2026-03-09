<?php
// models/UserModel.php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new user
    public function register($username, $email, $password, $user_type, $first_name = '', $last_name = '', $phone_number = '') {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        // Check if username or email already exists
        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username already taken'];
        }

        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Generate verification token
        $verification_token = bin2hex(random_bytes(32));
        $verification_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

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

    // Login user
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
                  WHERE ($field = :credential)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':credential', $username_or_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            $password_valid = password_verify($password, $user['password_hash']);
            
            // =============================================
            // RECORD LOGIN ATTEMPT - MOVED BEFORE VERIFICATION CHECK
            // =============================================
            try {
                $attemptQuery = "INSERT INTO login_attempts 
                                (user_id, username, ip_address, success, created_at) 
                                VALUES (:user_id, :username, :ip, :success, NOW())";
                $attemptStmt = $this->conn->prepare($attemptQuery);
                $attemptStmt->execute([
                    ':user_id' => $user['id'],
                    ':username' => $username_or_email,
                    ':ip' => $ip_address,
                    ':success' => $password_valid ? 1 : 0
                ]);
            } catch (PDOException $e) {
                // Log but don't stop login if logging fails
                error_log("Failed to record login attempt: " . $e->getMessage());
            }
            
            // Check if account is verified
            if (!$user['is_verified']) {
                return ['success' => false, 'message' => 'Please verify your email before logging in'];
            }
            
            // Check password
            if ($password_valid) {
                // Update last login
                $this->updateLastLogin($user['id']);
                
                // Create session token for remember me
                $session_token = null;
                if ($remember) {
                    $session_token = $this->createRememberMeToken($user['id']);
                }
                
                // Remove sensitive data
                unset($user['password_hash']);
                unset($user['verification_token']);
                unset($user['reset_token']);
                
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user,
                    'session_token' => $session_token
                ];
            } else {
                return ['success' => false, 'message' => 'Invalid password'];
            }
        } else {
            // User not found - record attempt with null user_id
            try {
                $attemptQuery = "INSERT INTO login_attempts 
                                (user_id, username, ip_address, success, created_at) 
                                VALUES (:user_id, :username, :ip, :success, NOW())";
                $attemptStmt = $this->conn->prepare($attemptQuery);
                $attemptStmt->execute([
                    ':user_id' => null,
                    ':username' => $username_or_email,
                    ':ip' => $ip_address,
                    ':success' => 0
                ]);
            } catch (PDOException $e) {
                error_log("Failed to record login attempt: " . $e->getMessage());
            }
            
            return ['success' => false, 'message' => 'User not found'];
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}
    // Get user by ID
    public function getUserById($user_id) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT id, username, email, user_type, first_name, last_name, phone_number, 
                             profile_image, is_verified, created_at, updated_at 
                      FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }

    // Get user by email
    public function getUserByEmail($email) {
        if (!$this->conn) {
            return null;
        }

        try {
            $query = "SELECT id, username, email, user_type, first_name, last_name, is_verified 
                      FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Get user by email error: " . $e->getMessage());
            return null;
        }
    }

    // Update user profile
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

        // Always update the updated_at timestamp
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

    // Change password
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

    // Check if username exists
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

    // Check if email exists
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

    // Verify email with token
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

    // Request password reset
    public function requestPasswordReset($email) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $user = $this->getUserByEmail($email);
            
            if (!$user) {
                // Don't reveal that email doesn't exist for security
                return ['success' => true, 'message' => 'If the email exists, a reset link will be sent'];
            }
            
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $query = "UPDATE " . $this->table_name . " 
                      SET reset_token = :token, reset_expires = :expires, updated_at = NOW() 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $reset_token);
            $stmt->bindParam(':expires', $reset_expires);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
            
            // TODO: Send password reset email
            
            return ['success' => true, 'message' => 'If the email exists, a reset link will be sent'];
        } catch (PDOException $e) {
            error_log("Password reset request error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    // Reset password with token
    public function resetPassword($token, $new_password) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE reset_token = :token AND reset_expires > NOW()";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                $update = "UPDATE " . $this->table_name . " 
                           SET password_hash = :password_hash, reset_token = NULL, reset_expires = NULL, updated_at = NOW() 
                           WHERE id = :id";
                $stmt = $this->conn->prepare($update);
                $stmt->bindParam(':password_hash', $password_hash);
                $stmt->bindParam(':id', $user['id']);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Password reset successful! You can now login with your new password.'];
                }
            }
            
            return ['success' => false, 'message' => 'Invalid or expired reset token'];
        } catch (PDOException $e) {
            error_log("Password reset error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    // Update last login
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

    // Create remember me token
    private function createRememberMeToken($user_id) {
        try {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Create user_sessions table if you want remember me functionality
            // For now, we'll just return the token and handle it via cookie
            return $token;
        } catch (Exception $e) {
            error_log("Create remember token error: " . $e->getMessage());
            return null;
        }
    }

    // Get user statistics
    public function getUserStats($user_id) {
        if (!$this->conn) {
            return [];
        }

        $stats = [];
        
        try {
            // Count saved properties (if you have saved_properties table)
            $query = "SELECT COUNT(*) as count FROM saved_properties WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $stats['saved_properties'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Count unread notifications (if you have notifications table)
            $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $stats['unread_notifications'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Get account age
            $query = "SELECT created_at FROM " . $this->table_name . " WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['member_since'] = $result['created_at'] ?? date('Y-m-d H:i:s');
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get user stats error: " . $e->getMessage());
            return [
                'saved_properties' => 0,
                'unread_notifications' => 0,
                'member_since' => date('Y-m-d H:i:s')
            ];
        }
    }

    // Get full name
    public function getFullName($user) {
        $parts = [];
        if (!empty($user['first_name'])) $parts[] = $user['first_name'];
        if (!empty($user['last_name'])) $parts[] = $user['last_name'];
        
        if (empty($parts)) {
            return $user['username'];
        }
        
        return implode(' ', $parts);
    }

    public function logout($user_id) {
    if (!$this->conn) {
        return false;
    }

    try {
        // If you have a user_sessions table, clear sessions
        $query = "DELETE FROM user_sessions WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
        return false;
    }
}
}
?>