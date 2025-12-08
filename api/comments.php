<?php
/**
 * Comments API
 * Modern Blog System 2025
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create comment
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $author_name = isset($_POST['author_name']) ? sanitize($_POST['author_name']) : '';
    $author_email = isset($_POST['author_email']) ? sanitize($_POST['author_email']) : '';
    $content = isset($_POST['content']) ? sanitize($_POST['content']) : '';
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Validate CSRF token
    if (!verifyCSRFToken($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }

    // Validate input
    if (empty($post_id) || empty($author_name) || empty($author_email) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Validate email
    if (!filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Get user ID if logged in
    $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;

    try {
        $query = "INSERT INTO comments (post_id, user_id, author_name, author_email, content, status) 
                  VALUES (:post_id, :user_id, :author_name, :author_email, :content, 'pending')";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':author_name', $author_name, PDO::PARAM_STR);
        $stmt->bindValue(':author_email', $author_email, PDO::PARAM_STR);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Comment submitted successfully. It will be reviewed before publishing.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit comment']);
        }
    } catch (PDOException $e) {
        error_log("Comment Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get comments for a post
    $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
    
    if (empty($post_id)) {
        echo json_encode(['success' => false, 'message' => 'Post ID required']);
        exit;
    }

    try {
        $query = "SELECT c.*, u.username, u.avatar 
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id AND c.status = 'approved'
                  ORDER BY c.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $comments = $stmt->fetchAll();
        
        // Format dates
        foreach ($comments as &$comment) {
            $comment['created_at_formatted'] = formatDate($comment['created_at']);
        }
        
        echo json_encode(['success' => true, 'comments' => $comments]);
    } catch (PDOException $e) {
        error_log("Comment Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
    }
}


