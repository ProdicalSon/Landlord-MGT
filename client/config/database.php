<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $db_name = "smarthunt_db";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        try {
            $this->conn = null;
            
            // First connect to MySQL server without database
            $this->conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if database exists
            $stmt = $this->conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->db_name}'");
            $dbExists = $stmt->fetch();
            
            if (!$dbExists) {
                // Create the database
                $this->conn->exec("CREATE DATABASE IF NOT EXISTS {$this->db_name}");
                error_log("Database '{$this->db_name}' created");
            }
            
            // Now connect to the specific database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            
            error_log("Database connection successful");
            return $this->conn;
            
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            return null; // Return null on error
        }
    }
}
?>