<?php
/**
 * Newsletter API
 * Modern Blog System 2025
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    try {
        // Check if email already exists
        $checkQuery = "SELECT id FROM newsletter_subscribers WHERE email = :email";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $checkStmt->execute();
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already subscribed']);
            exit;
        }

        // Insert new subscriber
        $query = "INSERT INTO newsletter_subscribers (email, status) VALUES (:email, 'active')";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Successfully subscribed to newsletter!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to subscribe']);
        }
    } catch (PDOException $e) {
        error_log("Newsletter Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}


