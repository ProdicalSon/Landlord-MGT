-- Create Database
CREATE DATABASE IF NOT EXISTS smarthunt_db;
USE smarthunt_db;

-- Users Table (for both landlords and students)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    user_type ENUM('landlord', 'student') NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone_number VARCHAR(20),
    profile_image VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100),
    verification_expires DATETIME,
    reset_token VARCHAR(100),
    reset_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_user_type (user_type)
);

-- Landlords Table (extends users table)
CREATE TABLE landlords (
    user_id INT PRIMARY KEY,
    company_name VARCHAR(100),
    tax_id VARCHAR(50),
    bank_account VARCHAR(100),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_properties INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Students Table (extends users table)
CREATE TABLE students (
    user_id INT PRIMARY KEY,
    university VARCHAR(100),
    student_id VARCHAR(50),
    year_of_study INT,
    emergency_contact VARCHAR(20),
    preferred_location VARCHAR(100),
    budget_min DECIMAL(10,2),
    budget_max DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Login Attempts Table (for security)
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    success BOOLEAN,
    user_agent TEXT,
    INDEX idx_user_id (user_id),
    INDEX idx_attempt_time (attempt_time),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sessions Table (for managing user sessions)
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session_token (session_token),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Password Reset Table (alternative approach)
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(100) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

-- Sample Data (for testing)
INSERT INTO users (username, email, password_hash, user_type, first_name, last_name, is_verified) 
VALUES 
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'landlord', 'John', 'Doe', TRUE),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Jane', 'Smith', TRUE);

INSERT INTO landlords (user_id, company_name, tax_id, bank_account) 
VALUES (1, 'Doe Properties', 'TAX-12345', 'BANK-67890');

INSERT INTO students (user_id, university, student_id, year_of_study, budget_min, budget_max) 
VALUES (2, 'University of Nairobi', 'STU-12345', 2, 5000.00, 15000.00);