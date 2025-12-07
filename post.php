<?php
/**
 * Single Post Page
 * Modern Blog System 2025
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/Post.php';
require_once __DIR__ . '/includes/Category.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize models
$post = new Post($db);
$category = new Category($db);

// Get post slug
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    redirect(getBaseUrl());
}

// Get post
$postData = $post->getBySlug($slug);

if (!$postData) {
    header("HTTP/1.0 404 Not Found");
    $pageTitle = 'Post Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<main><div class="container"><div class="post-content"><h2>Post not found</h2><p>The post you are looking for does not exist.</p></div></div></main>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Get related posts (same category)
$relatedPosts = $post->getByCategory($postData['category_slug'], 3, 0);

// Get categories
$categories = $category->getAll();

// Get popular posts
$popularPosts = $post->getPopular(5);

// Page title
$pageTitle = $postData['title'];

// Include header
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="container">
        <div class="site-content">
            <div class="post">
                <div class="post-content" data-aos="zoom-in-up" data-aos-delay="200">
                    <div class="post-image">
                        <div>
                            <img src="<?php echo $postData['featured_image'] ? uploadUrl($postData['featured_image']) : asset('Blog-post/blog1.png'); ?>" 
                                 alt="<?php echo e($postData['title']); ?>" class="img" />
                        </div>
                        <div class="post-info flex-row">
                            <span><i class="fas fa-user text-gray"></i>&nbsp;&nbsp;<?php echo e($postData['author_full_name'] ?: $postData['author_name']); ?></span>
                            <span><i class="fas fa-calendar-alt text-gray"></i>&nbsp;&nbsp;<?php echo formatDate($postData['published_at']); ?></span>
                            <span><i class="fas fa-tag text-gray"></i>&nbsp;&nbsp;<a href="<?php echo getBaseUrl(); ?>/category.php?slug=<?php echo e($postData['category_slug']); ?>"><?php echo e($postData['category_name']); ?></a></span>
                            <span><i class="fas fa-eye text-gray"></i>&nbsp;&nbsp;<?php echo formatNumber($postData['views']); ?> views</span>
                        </div>
                    </div>
                    <div class="post-title">
                        <h1><?php echo e($postData['title']); ?></h1>
                        <div class="post-content-text">
                            <?php echo nl2br(e($postData['content'])); ?>
                        </div>
                    </div>
                </div>
                <hr />

                <!-- Comments Section -->
                <div class="comments-section" data-aos="fade-up" data-aos-delay="300">
                    <h2>Comments</h2>
                    <div id="comments-container" data-post-id="<?php echo $postData['id']; ?>">
                        <!-- Comments will be loaded here via JavaScript -->
                    </div>
                    <div class="comment-form">
                        <h3>Leave a Comment</h3>
                        <form id="comment-form" method="POST" action="<?php echo getBaseUrl(); ?>/api/comments.php">
                            <input type="hidden" name="post_id" value="<?php echo $postData['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <div class="form-group">
                                <input type="text" name="author_name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="author_email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group">
                                <textarea name="content" rows="5" placeholder="Your Comment" required></textarea>
                            </div>
                            <button type="submit" class="btn">Submit Comment</button>
                        </form>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="related-posts" data-aos="fade-up" data-aos-delay="400">
                    <h2>Related Posts</h2>
                    <div class="related-posts-grid">
                        <?php foreach ($relatedPosts as $related): ?>
                            <?php if ($related['id'] != $postData['id']): ?>
                            <div class="related-post-item">
                                <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($related['slug']); ?>">
                                    <img src="<?php echo $related['featured_image'] ? uploadUrl($related['featured_image']) : asset('Blog-post/blog' . (($related['id'] % 4) + 1) . '.png'); ?>" 
                                         alt="<?php echo e($related['title']); ?>">
                                    <h4><?php echo e($related['title']); ?></h4>
                                </a>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <aside class="sidebar">
                <!-- Categories -->
                <div class="category">
                    <h2>Category</h2>
                    <ul class="category-list">
                        <?php foreach ($categories as $cat): ?>
                        <li class="list-items" data-aos="fade-left" data-aos-delay="100">
                            <a href="<?php echo getBaseUrl(); ?>/category.php?slug=<?php echo e($cat['slug']); ?>">
                                <?php echo e($cat['name']); ?>
                            </a>
                            <span>(<?php echo $cat['post_count']; ?>)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular Posts -->
                <?php if (!empty($popularPosts)): ?>
                <div class="popular-post">
                    <h2>Popular Post</h2>
                    <?php foreach ($popularPosts as $popular): ?>
                    <div class="post-content" data-aos="flip-up" data-aos-delay="200">
                        <div class="post-image">
                            <div>
                                <img src="<?php echo $popular['featured_image'] ? uploadUrl($popular['featured_image']) : asset('popular-post/m-blog-' . (($popular['id'] % 5) + 1) . '.jpg'); ?>" 
                                     alt="<?php echo e($popular['title']); ?>" class="img" />
                            </div>
                            <div class="post-info flex-row">
                                <span><i class="fas fa-user text-gray"></i>&nbsp;&nbsp;<?php echo e($popular['author_name']); ?></span>
                                <span><i class="fas fa-calendar-alt text-gray"></i>&nbsp;&nbsp;<?php echo formatDate($popular['published_at']); ?></span>
                            </div>
                        </div>
                        <div class="post-title">
                            <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($popular['slug']); ?>">
                                <?php echo e($popular['title']); ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

