<?php
/**
 * Helper Functions
 * Modern Blog System 2025
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate URL-friendly slug
 */
function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Format date
 */
function formatDate($date, $format = DATE_FORMAT) {
    if (empty($date)) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Get current URL
 */
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Get base URL
 */
function getBaseUrl() {
    return BASE_URL;
}

/**
 * Get asset URL
 * Handles both assets folder and root-level files (css, js)
 */
function asset($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    
    // If path already starts with assets/, use it as is
    if (strpos($path, 'assets/') === 0) {
        return BASE_URL . '/' . $path;
    }
    
    // For CSS, JS files in root, use directly
    if (strpos($path, 'css/') === 0 || strpos($path, 'js/') === 0) {
        return BASE_URL . '/' . $path;
    }
    
    // For images and other assets, add assets/ prefix
    // Check if it's an image file extension
    $isImage = preg_match('/\.(jpg|jpeg|png|gif|svg|webp|ico)$/i', $path);
    
    // Check if it's a known asset folder path
    $assetFolders = ['Blog-post/', 'popular-post/', 'instagram/', 'Background-image', 'Abract'];
    $isAssetFolder = false;
    foreach ($assetFolders as $folder) {
        if (strpos($path, $folder) === 0) {
            $isAssetFolder = true;
            break;
        }
    }
    
    // If it's an image or asset folder, add assets/ prefix
    if ($isImage || $isAssetFolder) {
        return BASE_URL . '/assets/' . $path;
    }
    
    // Default: use directly (for backward compatibility)
    return BASE_URL . '/' . $path;
}

/**
 * Get upload URL
 */
function uploadUrl($filename) {
    return UPLOADS_URL . '/' . ltrim($filename, '/');
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get pagination offset
 */
function getPaginationOffset($page, $perPage = POSTS_PER_PAGE) {
    return ($page - 1) * $perPage;
}

/**
 * Format number (views, comments, etc.)
 */
function formatNumber($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

/**
 * Escape output for HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

