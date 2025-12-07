<?php
/**
 * Generate Random Posts - Web Interface
 * Creates sample posts with random content
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/Category.php';

// Check authentication
if (!isLoggedIn() || !isAdmin()) {
    redirect(getBaseUrl() . '/login.php');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $numberOfPosts = isset($_POST['number']) ? (int)$_POST['number'] : 20;
    $numberOfPosts = max(1, min(100, $numberOfPosts)); // Limit between 1 and 100
    
    // Initialize database
    $database = new Database();
    $db = $database->getConnection();
    
    // Get categories
    $category = new Category($db);
    $categories = $category->getAll();
    
    if (empty($categories)) {
        $error = "No categories found. Please create categories first.";
    } else {
        // Get admin user
        $stmt = $db->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
        $adminUser = $stmt->fetch();
        
        if (!$adminUser) {
            $error = "No admin user found.";
        } else {
            $authorId = $adminUser['id'];
            
            // Include the generation logic
            require_once __DIR__ . '/../scripts/generate-posts.php';
            
            // Actually, we'll inline the logic here for web interface
            $titles = [
                "The Future of Web Development: Trends to Watch in 2025",
                "10 Essential JavaScript Tips Every Developer Should Know",
                "Building Responsive Websites: A Complete Guide",
                "Understanding CSS Grid: Modern Layout Techniques",
                "PHP Best Practices for Secure Applications",
                "Introduction to React Hooks: Simplifying Component Logic",
                "Database Optimization: Improving Query Performance",
                "The Art of Clean Code: Writing Maintainable Software",
                "Exploring New Features in PHP 8.2",
                "Creating Beautiful UIs with Modern CSS",
                "API Design Principles: REST vs GraphQL",
                "Mastering Git: Advanced Version Control Techniques",
                "Docker for Developers: Containerization Made Easy",
                "Testing Strategies: Unit, Integration, and E2E Tests",
                "Performance Optimization: Making Websites Faster",
                "Security Best Practices for Web Applications",
                "The Rise of TypeScript: Why Developers Love It",
                "Microservices Architecture: Breaking Down Monoliths",
                "Cloud Computing: AWS, Azure, and Google Cloud",
                "Machine Learning Basics for Web Developers",
                "Progressive Web Apps: The Future of Mobile",
                "Design Patterns in Modern JavaScript",
                "Building Scalable Applications: Lessons Learned",
                "The Evolution of Frontend Frameworks",
                "Backend Development: Node.js vs PHP",
                "DevOps Essentials: CI/CD Pipelines",
                "Code Review Best Practices",
                "Accessibility in Web Development",
                "SEO Optimization: Technical Implementation",
                "Modern JavaScript: ES6+ Features Explained",
                "10 Morning Habits of Highly Productive Developers",
                "Work-Life Balance in the Tech Industry",
                "Remote Work: Tips for Staying Productive",
                "Healthy Lifestyle for Programmers",
                "Time Management for Software Developers"
            ];
            
            $excerpts = [
                "Discover the latest trends shaping the future of web development.",
                "Learn essential techniques that will make you a more efficient developer.",
                "A comprehensive guide to building websites that work perfectly on all devices.",
                "Master powerful techniques and create complex layouts with ease.",
                "Explore security best practices to protect your applications.",
                "Dive deep into modern development practices and tools.",
                "Understanding the fundamentals that every developer should know.",
                "Practical tips and tricks that will help you write better code.",
                "Stay up-to-date with the latest features and improvements.",
                "Learn how to build fast, secure, and scalable applications."
            ];
            
            $contentTemplates = [
                "In today's rapidly evolving digital landscape, staying ahead of the curve is essential for developers. This comprehensive guide explores the latest trends and technologies that are shaping the future of web development.\n\nWe'll dive deep into practical examples, real-world use cases, and actionable insights that you can apply to your projects immediately. Whether you're a beginner or an experienced developer, there's something valuable here for everyone.\n\n## Key Takeaways\n\n- Understanding modern development practices\n- Implementing best practices in your projects\n- Staying updated with industry trends\n- Building scalable and maintainable applications\n\n## Conclusion\n\nBy following these guidelines and staying curious about new technologies, you'll be well-equipped to tackle any development challenge that comes your way.",
                "This article provides a detailed exploration of essential concepts that every developer should master. We'll cover everything from basic principles to advanced techniques.\n\n## Getting Started\n\nBefore diving in, it's important to understand the fundamentals. These concepts form the foundation of everything else you'll learn.\n\n## Advanced Topics\n\nOnce you've mastered the basics, we can move on to more complex topics that will help you build sophisticated applications.\n\n## Best Practices\n\nFollowing industry best practices ensures that your code is maintainable, scalable, and secure. We'll explore proven strategies that top developers use."
            ];
            
            $categoriesData = array_map(function($cat) {
                return $cat['id'];
            }, $categories);
            
            $post = new Post($db);
            $generated = 0;
            $errors = 0;
            
            for ($i = 0; $i < $numberOfPosts; $i++) {
                try {
                    $title = $titles[array_rand($titles)];
                    $baseSlug = generateSlug($title);
                    $slug = $baseSlug;
                    $counter = 1;
                    
                    $checkStmt = $db->prepare("SELECT id FROM posts WHERE slug = ?");
                    while (true) {
                        $checkStmt->execute([$slug]);
                        if ($checkStmt->fetch()) {
                            $slug = $baseSlug . '-' . $counter;
                            $counter++;
                        } else {
                            break;
                        }
                    }
                    
                    $excerpt = $excerpts[array_rand($excerpts)];
                    $content = $contentTemplates[array_rand($contentTemplates)];
                    $categoryId = $categoriesData[array_rand($categoriesData)];
                    
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 70) {
                        $status = 'published';
                        $publishedAt = date('Y-m-d H:i:s', strtotime('-' . rand(0, 180) . ' days'));
                    } elseif ($statusRand <= 90) {
                        $status = 'draft';
                        $publishedAt = null;
                    } else {
                        $status = 'archived';
                        $publishedAt = date('Y-m-d H:i:s', strtotime('-' . rand(180, 365) . ' days'));
                    }
                    
                    $post->title = $title;
                    $post->slug = $slug;
                    $post->excerpt = $excerpt;
                    $post->content = $content;
                    $post->featured_image = null;
                    $post->author_id = $authorId;
                    $post->category_id = $categoryId;
                    $post->status = $status;
                    $post->published_at = $publishedAt;
                    
                    if ($post->create()) {
                        $generated++;
                        if ($status === 'published') {
                            $views = rand(10, 5000);
                            $db->exec("UPDATE posts SET views = {$views} WHERE id = {$post->id}");
                        }
                    } else {
                        $errors++;
                    }
                } catch (Exception $e) {
                    $errors++;
                }
            }
            
            if ($generated > 0) {
                $message = "Successfully generated {$generated} posts!";
                if ($errors > 0) {
                    $message .= " ({$errors} errors occurred)";
                }
            } else {
                $error = "Failed to generate posts. Please try again.";
            }
        }
    }
}

$pageTitle = 'Generate Posts';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/all.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/admin.css">
</head>
<body>
    <div class="admin-header">
        <div class="admin-container">
            <h1>Generate Random Posts</h1>
            <p><a href="<?php echo getBaseUrl(); ?>/admin">← Back to Dashboard</a></p>
        </div>
    </div>
    
    <div class="admin-nav">
        <div class="admin-container">
            <a href="<?php echo getBaseUrl(); ?>/admin">Dashboard</a>
            <a href="<?php echo getBaseUrl(); ?>/admin/posts.php">Posts</a>
            <a href="<?php echo getBaseUrl(); ?>/admin/generate-posts.php">Generate Posts</a>
        </div>
    </div>
    
    <div class="admin-container" style="max-width: 600px; margin: 2rem auto;">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo e($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>
        
        <div class="posts-table">
            <h2>Generate Sample Posts</h2>
            <p>This tool will create random posts with sample content for testing purposes.</p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="number">Number of Posts to Generate</label>
                    <input type="number" id="number" name="number" value="20" min="1" max="100" required>
                    <small>Enter a number between 1 and 100</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="generate" class="btn btn-success">
                        <i class="fas fa-magic"></i> Generate Posts
                    </button>
                    <a href="<?php echo getBaseUrl(); ?>/admin" class="btn">Cancel</a>
                </div>
            </form>
            
            <div style="margin-top: 2rem; padding: 1rem; background: #f9f9f9; border-radius: 8px;">
                <h3>What will be generated?</h3>
                <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                    <li>Random titles from a curated list</li>
                    <li>Unique slugs for each post</li>
                    <li>Random excerpts and content</li>
                    <li>Random category assignment</li>
                    <li>Random status (70% published, 20% draft, 10% archived)</li>
                    <li>Random view counts for published posts</li>
                    <li>Random publication dates (within last 180 days)</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

