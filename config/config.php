<?php
/**
 * Main Configuration File
 * Modern Blog System 2025
 */

// Error reporting (disable in production)
define('DEBUG', true);
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? 1 : 0);

// Timezone
date_default_timezone_set('America/Mexico_City');

// Base URL
define('BASE_URL', 'http://localhost:8000');
define('BASE_PATH', __DIR__ . '/..');

// Paths
define('ASSETS_PATH', BASE_PATH . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_UPLOAD_SIZE', 5242880); // 5MB

// Pagination
define('POSTS_PER_PAGE', 6);
define('COMMENTS_PER_PAGE', 10);

// Date format
define('DATE_FORMAT', 'M d, Y');
define('DATETIME_FORMAT', 'M d, Y H:i');

// Include database
require_once __DIR__ . '/database.php';


