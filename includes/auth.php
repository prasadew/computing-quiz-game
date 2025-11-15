<?php
// Authentication Functions
// includes/auth.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/jwt.php';

class Auth {
    private $db;

    public function __construct() {
        global $database;
        $this->db = $database;
    }

    // Register new user
    public function register($name, $email, $password) {
        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }

        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->executeQuery($query, [$email]);
        
        if ($this->db->fetchOne($stmt)) {
            return [
                'success' => false,
                'message' => 'Email already registered'
            ];
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
    $query = "INSERT INTO users (name, email, password_hash, total_score, created_at) 
          VALUES (?, ?, ?, 0, NOW())";
        
        try {
            $stmt = $this->db->executeQuery($query, [$name, $email, $password_hash]);
            $user_id = $this->db->getLastInsertId();

            // Generate JWT token
            $token = JWT::generate($user_id, $email);

            return [
                'success' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'user' => [
                    'id' => $user_id,
                    'name' => $name,
                    'email' => $email
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    // Login user
    public function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }

        // Get user by email
        $query = "SELECT id, name, email, password_hash, total_score FROM users WHERE email = ?";
        $stmt = $this->db->executeQuery($query, [$email]);
        $user = $this->db->fetchOne($stmt);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Generate JWT token
        $token = JWT::generate($user['id'], $user['email']);

        return [
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'total_score' => $user['total_score']
            ]
        ];
    }

    // Get current user from token
    public function getCurrentUser($token = null) {
        if (!$token) {
            $token = JWT::getBearerToken();
            if (!$token && isset($_COOKIE['auth_token'])) {
                $token = $_COOKIE['auth_token'];
            }
        }

        if (!$token) {
            return null;
        }

        $payload = JWT::verify($token);
        
        if (!$payload) {
            return null;
        }

        // Get user details from database
        $query = "SELECT id, name, email, total_score FROM users WHERE id = ?";
        $stmt = $this->db->executeQuery($query, [$payload['user_id']]);
        $user = $this->db->fetchOne($stmt);

        return $user;
    }

    // Check if user is authenticated
    public function isAuthenticated() {
        $token = JWT::getBearerToken();
        if (!$token && isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];
        }

        if (!$token) {
            return false;
        }

        $payload = JWT::verify($token);
        return $payload !== false;
    }

    // Require authentication (redirect if not authenticated)
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }
    }

    // Get user ID from token
    public function getUserId() {
        $token = JWT::getBearerToken();
        if (!$token && isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];
        }

        if (!$token) {
            return null;
        }

        $payload = JWT::verify($token);
        return $payload ? $payload['user_id'] : null;
    }

    // Logout user
    public function logout() {
        // Clear the auth token cookie if it exists
        if (isset($_COOKIE['auth_token'])) {
            setcookie('auth_token', '', time() - 3600, '/');
            unset($_COOKIE['auth_token']);
        }

        // Clear session variables
        $_SESSION = array();
        
        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
}
?>