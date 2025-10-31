<?php
// config/database.php - MySQL Database Connection for WAMP/PHPMyAdmin

class Database {
    private $host = "localhost";
    private $database = "ComputingQuizGame";
    private $username = "root";              // Default WAMP username
    private $password = "";                  // Default WAMP password (usually empty)
    private $conn = null;

    // Get database connection
    public function getConnection() {
        if ($this->conn === null) {
            try {
                // MySQL PDO connection
                $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
                
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
        $conn = $this->getConnection();
        
        try {
            $stmt = $conn->prepare($query);
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
        return $this->conn->lastInsertId();
    }
}

// Global database instance
$database = new Database();
?>