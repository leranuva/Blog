<?php
/**
 * Post Model
 * Modern Blog System 2025
 */

require_once __DIR__ . '/../config/config.php';

class Post {
    private $conn;
    private $table = 'posts';

    public $id;
    public $title;
    public $slug;
    public $excerpt;
    public $content;
    public $featured_image;
    public $author_id;
    public $category_id;
    public $status;
    public $views;
    public $created_at;
    public $updated_at;
    public $published_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all posts
     */
    public function getAll($limit = POSTS_PER_PAGE, $offset = 0, $status = 'published') {
        $query = "SELECT p.*, 
                  u.username as author_name, u.full_name as author_full_name,
                  c.name as category_name, c.slug as category_slug,
                  COUNT(DISTINCT cm.id) as comment_count
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN comments cm ON p.id = cm.post_id AND cm.status = 'approved'
                  WHERE p.status = :status
                  GROUP BY p.id
                  ORDER BY p.published_at DESC, p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get post by ID
     */
    public function getById($id) {
        $query = "SELECT p.*, 
                  u.username as author_name, u.full_name as author_full_name, u.avatar as author_avatar,
                  c.name as category_name, c.slug as category_slug
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get post by slug
     */
    public function getBySlug($slug) {
        $query = "SELECT p.*, 
                  u.username as author_name, u.full_name as author_full_name, u.avatar as author_avatar,
                  c.name as category_name, c.slug as category_slug
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.slug = :slug AND p.status = 'published'
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $post = $stmt->fetch();
        
        // Increment views
        if ($post) {
            $this->incrementViews($post['id']);
        }

        return $post;
    }

    /**
     * Get posts by category
     */
    public function getByCategory($categorySlug, $limit = POSTS_PER_PAGE, $offset = 0) {
        $query = "SELECT p.*, 
                  u.username as author_name, u.full_name as author_full_name,
                  c.name as category_name, c.slug as category_slug,
                  COUNT(DISTINCT cm.id) as comment_count
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN comments cm ON p.id = cm.post_id AND cm.status = 'approved'
                  WHERE c.slug = :category_slug AND p.status = 'published'
                  GROUP BY p.id
                  ORDER BY p.published_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':category_slug', $categorySlug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search posts
     */
    public function search($searchTerm, $limit = POSTS_PER_PAGE, $offset = 0) {
        $query = "SELECT p.*, 
                  u.username as author_name, u.full_name as author_full_name,
                  c.name as category_name, c.slug as category_slug,
                  COUNT(DISTINCT cm.id) as comment_count,
                  MATCH(p.title, p.excerpt, p.content) AGAINST(:search IN NATURAL LANGUAGE MODE) as relevance
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN comments cm ON p.id = cm.post_id AND cm.status = 'approved'
                  WHERE p.status = 'published' 
                  AND (MATCH(p.title, p.excerpt, p.content) AGAINST(:search IN NATURAL LANGUAGE MODE)
                  OR p.title LIKE :search_like OR p.excerpt LIKE :search_like)
                  GROUP BY p.id
                  ORDER BY relevance DESC, p.published_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $searchLike = '%' . $searchTerm . '%';
        $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':search_like', $searchLike, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get popular posts
     */
    public function getPopular($limit = 5) {
        $query = "SELECT p.*, 
                  u.username as author_name,
                  c.name as category_name, c.slug as category_slug
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.status = 'published'
                  ORDER BY p.views DESC, p.published_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get recent posts
     */
    public function getRecent($limit = 5) {
        $query = "SELECT p.*, 
                  u.username as author_name, u.full_name as author_full_name,
                  c.name as category_name, c.slug as category_slug,
                  COUNT(DISTINCT cm.id) as comment_count
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.author_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN comments cm ON p.id = cm.post_id AND cm.status = 'approved'
                  WHERE p.status = 'published'
                  GROUP BY p.id
                  ORDER BY p.published_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get total count
     */
    public function getTotalCount($status = 'published') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Increment views
     */
    public function incrementViews($id) {
        $query = "UPDATE " . $this->table . " SET views = views + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Create post
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (title, slug, excerpt, content, featured_image, author_id, category_id, status, published_at)
                  VALUES (:title, :slug, :excerpt, :content, :featured_image, :author_id, :category_id, :status, :published_at)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':excerpt', $this->excerpt, PDO::PARAM_STR);
        $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
        $stmt->bindValue(':featured_image', $this->featured_image, PDO::PARAM_STR);
        $stmt->bindValue(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $this->status, PDO::PARAM_STR);
        $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Update post
     */
    public function update() {
        $query = "UPDATE " . $this->table . " SET
                  title = :title,
                  slug = :slug,
                  excerpt = :excerpt,
                  content = :content,
                  featured_image = :featured_image,
                  category_id = :category_id,
                  status = :status,
                  published_at = :published_at,
                  updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':excerpt', $this->excerpt, PDO::PARAM_STR);
        $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
        $stmt->bindValue(':featured_image', $this->featured_image, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $this->status, PDO::PARAM_STR);
        $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete post
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}


