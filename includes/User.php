<?php
/**
 * User Model
 * Modern Blog System 2025
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Login user
     */
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username OR email = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        $query = "SELECT id, username, email, full_name, avatar, role, created_at FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}

