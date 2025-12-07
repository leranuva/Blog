<?php
/**
 * Category Page
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

// Get category slug
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    // Show all categories
    $categories = $category->getAll();
    $pageTitle = 'Categories';
    include __DIR__ . '/includes/header.php';
    ?>
    <main>
        <section class="container">
            <div class="site-content">
                <div class="post">
                    <h1>All Categories</h1>
                    <div class="categories-grid">
                        <?php foreach ($categories as $cat): ?>
                        <div class="category-card" data-aos="fade-up">
                            <a href="?slug=<?php echo e($cat['slug']); ?>">
                                <h3><?php echo e($cat['name']); ?></h3>
                                <p><?php echo e($cat['description']); ?></p>
                                <span class="post-count"><?php echo $cat['post_count']; ?> posts</span>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Get category
$categoryData = $category->getBySlug($slug);

if (!$categoryData) {
    redirect(getBaseUrl());
}

// Get pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = getPaginationOffset($page);

// Get posts
$posts = $post->getByCategory($slug, POSTS_PER_PAGE, $offset);

// Get total count (simplified - in production, add method to Post class)
$totalPosts = count($posts);
$totalPages = ceil($totalPosts / POSTS_PER_PAGE);

// Get categories for sidebar
$categories = $category->getAll();

// Get popular posts
$popularPosts = $post->getPopular(5);

// Page title
$pageTitle = $categoryData['name'];

// Include header
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="container">
        <div class="site-content">
            <div class="post">
                <div class="category-header" data-aos="fade-up">
                    <h1><?php echo e($categoryData['name']); ?></h1>
                    <?php if ($categoryData['description']): ?>
                        <p class="category-description"><?php echo e($categoryData['description']); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (empty($posts)): ?>
                    <article class="post-content empty-state" data-aos="fade-up">
                        <h2>No posts in this category</h2>
                        <p>There are no published posts in this category yet.</p>
                    </article>
                <?php else: ?>
                    <?php foreach ($posts as $index => $postItem): ?>
                    <article class="post-content" data-aos="fade-up" data-aos-delay="<?php echo ($index % 6) * 100; ?>">
                        <div class="post-image">
                            <div>
                                <img src="<?php echo $postItem['featured_image'] ? uploadUrl($postItem['featured_image']) : asset('Blog-post/blog' . (($postItem['id'] % 4) + 1) . '.png'); ?>" 
                                     alt="<?php echo e($postItem['title']); ?>" class="img" loading="lazy" />
                            </div>
                            <div class="post-info flex-row always-visible">
                                <span><i class="fas fa-user"></i><?php echo e($postItem['author_full_name'] ?: $postItem['author_name']); ?></span>
                                <span><i class="fas fa-calendar-alt"></i><?php echo formatDate($postItem['published_at']); ?></span>
                                <span><i class="fas fa-comments"></i><?php echo $postItem['comment_count']; ?> comments</span>
                            </div>
                        </div>
                        <div class="post-title">
                            <a href="<?php echo getBaseUrl(); ?>/category.php?slug=<?php echo e($postItem['category_slug']); ?>" class="post-category">
                                <?php echo e($postItem['category_name']); ?>
                            </a>
                            <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($postItem['slug']); ?>">
                                <?php echo e($postItem['title']); ?>
                            </a>
                            <p><?php echo e($postItem['excerpt'] ?: truncate(strip_tags($postItem['content']), 200)); ?></p>
                            <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($postItem['slug']); ?>" class="btn post-btn">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination flex-row">
                    <?php if ($page > 1): ?>
                        <a href="?slug=<?php echo e($slug); ?>&page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="?slug=<?php echo e($slug); ?>&page=<?php echo $i; ?>" class="pages <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?slug=<?php echo e($slug); ?>&page=<?php echo $page + 1; ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
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

