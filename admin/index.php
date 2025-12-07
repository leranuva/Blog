<?php
/**
 * Admin Dashboard
 * Modern Blog System 2025
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/Category.php';

// Check authentication
if (!isLoggedIn()) {
    redirect(getBaseUrl() . '/login.php');
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize models
$post = new Post($db);
$category = new Category($db);

// Get stats
$totalPosts = $post->getTotalCount('published');
$draftPosts = $post->getTotalCount('draft');
$totalCategories = count($category->getAll());

// Get recent posts
$recentPosts = $post->getAll(5, 0, 'published');

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/all.css'); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .admin-header { background: #fff; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .admin-header h1 { color: #333; }
        .admin-nav { background: #fff; padding: 1rem 2rem; margin-bottom: 2rem; }
        .admin-nav a { margin-right: 1.5rem; color: #666; text-decoration: none; }
        .admin-nav a:hover { color: #333; }
        .admin-container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-card h3 { color: #666; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .stat-card .number { font-size: 2rem; font-weight: bold; color: #333; }
        .posts-table { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; font-weight: 600; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-container">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo e($_SESSION['username']); ?>!</p>
        </div>
    </div>
    
    <div class="admin-nav">
        <div class="admin-container">
            <a href="<?php echo getBaseUrl(); ?>/admin">Dashboard</a>
            <a href="<?php echo getBaseUrl(); ?>/admin/posts.php">Posts</a>
            <a href="<?php echo getBaseUrl(); ?>/admin/categories.php">Categories</a>
            <a href="<?php echo getBaseUrl(); ?>/admin/generate-posts.php">Generate Posts</a>
            <a href="<?php echo getBaseUrl(); ?>">View Site</a>
            <a href="<?php echo getBaseUrl(); ?>/logout.php">Logout</a>
        </div>
    </div>
    
    <div class="admin-container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Published Posts</h3>
                <div class="number"><?php echo $totalPosts; ?></div>
            </div>
            <div class="stat-card">
                <h3>Draft Posts</h3>
                <div class="number"><?php echo $draftPosts; ?></div>
            </div>
            <div class="stat-card">
                <h3>Categories</h3>
                <div class="number"><?php echo $totalCategories; ?></div>
            </div>
        </div>
        
        <div class="posts-table">
            <h2 style="margin-bottom: 1rem;">Recent Posts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPosts)): ?>
                        <tr>
                            <td colspan="5">No posts found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentPosts as $postItem): ?>
                        <tr>
                            <td><?php echo e($postItem['title']); ?></td>
                            <td><?php echo e($postItem['category_name']); ?></td>
                            <td><?php echo e($postItem['author_name']); ?></td>
                            <td><?php echo formatDate($postItem['published_at']); ?></td>
                            <td>
                                <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($postItem['slug']); ?>" class="btn" target="_blank">View</a>
                                <a href="<?php echo getBaseUrl(); ?>/admin/posts.php?edit=<?php echo $postItem['id']; ?>" class="btn btn-success">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div style="margin-top: 1rem;">
                <a href="<?php echo getBaseUrl(); ?>/admin/posts.php?action=create" class="btn btn-success">Create New Post</a>
            </div>
        </div>
    </div>
</body>
</html>

