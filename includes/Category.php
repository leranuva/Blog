<?php
/**
 * Category Model
 * Modern Blog System 2025
 */

require_once __DIR__ . '/../config/config.php';

class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $name;
    public $slug;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all categories
     */
    public function getAll() {
        $query = "SELECT c.*, COUNT(p.id) as post_count
                  FROM " . $this->table . " c
                  LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                  GROUP BY c.id
                  ORDER BY c.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get category by slug
     */
    public function getBySlug($slug) {
        $query = "SELECT * FROM " . $this->table . " WHERE slug = :slug LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get category by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}

