<?php
// Database Configuration for MySQL
// config/database.php

class Database {
    private $host = "localhost";
    private $database = "ComputingQuizGame";
    private $username = "root"; // Default WAMP MySQL username
    private $password = ""; // Update with your MySQL root password
    private $conn = null;

    // Get database connection
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->database,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->exec("set names utf8");
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return $this->conn;
    }

    // Close connection
    public function closeConnection() {
        $this->conn = null;
    }

    // Execute query with parameters (prevents SQL injection)
    public function executeQuery($query, $params = array()) {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query execution failed: " . $e->getMessage());
        }
    }

    // Fetch all results
    public function fetchAll($stmt) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch single row
    public function fetchOne($stmt) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get last insert ID
    public function getLastInsertId() {
        return $this->getConnection()->lastInsertId();
    }
}

// Global database instance
$database = new Database();
?>
