<?php
/**
 * Home Page - Dynamic Blog
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

// Get pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = getPaginationOffset($page);

// Get posts
$posts = $post->getAll(POSTS_PER_PAGE, $offset);
$totalPosts = $post->getTotalCount();
$totalPages = ceil($totalPosts / POSTS_PER_PAGE);

// Get featured posts for carousel (latest 6)
$featuredPosts = $post->getRecent(6);

// Get popular posts for sidebar
$popularPosts = $post->getPopular(5);

// Get categories
$categories = $category->getAll();

// Get recent posts for sidebar
$recentPosts = $post->getRecent(5);

// Page title
$pageTitle = 'Home';

// Include header
include __DIR__ . '/includes/header.php';
?>

<!-- --------------------------Main Site Section----------------------------- -->
<main>
    <!-- --------------------------Hero Section----------------------------- -->
    <section class="site-title heroEffects">
        <div class="bg"></div>
        <div class="shade"></div>
        
        <!-- Main Content -->
        <div class="title centerV">
            <div>
                <div class="text site-backgroud">
                    <h3>Tours & Travels</h3>
                    <h1>Amazing Place on Earth</h1>
                    <p>Discover incredible destinations, share your travel experiences, and connect with a community of passionate explorers. Your next adventure starts here.</p>
                    <div class="hero-buttons">
                        <a href="#posts" class="btn btn-primary">
                            <span>Explore Posts</span>
                        </a>
                        <a href="#posts" class="btn btn-secondary">
                            <span>Start Reading</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="arrow bouncy" aria-label="Scroll to content">
            <svg height="25" width="50" viewBox="0 0 50 25">
                <polygon points="0,0 25,15 50,0 25,25" fill="rgba(255,255,255,0.9)" stroke-width="0"/>
            </svg>
        </div>
    </section>
    <!-- -------------X------------Hero Section--------------X-------------- -->

    <!-- -------------------------Featured Posts Carousel---------------------------- -->
    <?php if (!empty($featuredPosts)): ?>
    <section class="featured-carousel-section">
        <div class="blog">
            <div class="container">
                <div class="carousel-header" data-aos="fade-up">
                    <h2>Featured Stories</h2>
                    <p>Discover our most popular and inspiring travel stories from around the world</p>
                </div>
                
                <div class="owl-carousel owl-theme blog-post">
                    <?php foreach ($featuredPosts as $index => $featured): ?>
                    <article class="blog-content" data-aos="fade-up" data-aos-delay="<?php echo ($index % 6) * 100; ?>">
                        <div class="blog-image-wrapper">
                            <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($featured['slug']); ?>">
                                <img src="<?php echo $featured['featured_image'] ? uploadUrl($featured['featured_image']) : asset('Blog-post/post-' . (($featured['id'] % 10) + 1) . '.jpg'); ?>" 
                                     alt="<?php echo e($featured['title']); ?>" loading="lazy" />
                            </a>
                        </div>
                        <div class="blog-title">
                            <a href="<?php echo getBaseUrl(); ?>/category.php?slug=<?php echo e($featured['category_slug']); ?>" class="btn btn-blog" onclick="event.stopPropagation();">
                                <?php echo e($featured['category_name']); ?>
                            </a>
                            <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($featured['slug']); ?>">
                                <h3><?php echo e($featured['title']); ?></h3>
                            </a>
                            <span><?php echo formatDate($featured['published_at'], 'M d, Y'); ?></span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- -------------X------------Featured Carousel-----------X--------------- -->

    <!----------------------------- Site Content --------------------------- -->
    <section class="container" id="posts">
        <div class="site-content">
            <div class="post">
                <?php if (empty($posts)): ?>
                    <article class="post-content empty-state" data-aos="fade-up">
                        <h2>No posts found</h2>
                        <p>There are no published posts yet. Check back soon!</p>
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
                        <a href="?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?>" class="pages <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>"><i class="fas fa-chevron-right"></i></a>
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

                <!-- Newsletter -->
                <div class="newsletter" data-aos="fade-up" data-aos-delay="300">
                    <h2>Newsletter</h2>
                    <div class="form-element">
                        <form id="newsletter-form-sidebar" method="POST" action="<?php echo getBaseUrl(); ?>/api/newsletter.php">
                            <input type="email" name="email" class="input-element" placeholder="Email" required />
                            <button type="submit" class="btn form-btn">Subscribe</button>
                        </form>
                    </div>
                </div>

                <!-- Popular Tags -->
                <div class="popular-tags">
                    <h2>Popular Tags</h2>
                    <div class="tags flex-row">
                        <?php
                        $tags = ['Software', 'Technology', 'Travel', 'Illustration', 'Design', 'Lifestyle', 'Food', 'Love', 'Project'];
                        foreach ($tags as $index => $tag):
                        ?>
                        <span class="tag" data-aos="flip-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                            <?php echo e($tag); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </section>
    <!----------------------------- Site Content ------------------------------>

</main>
<!-- --------------X-----------Main Site Section--------------X-------------- -->

<?php include __DIR__ . '/includes/footer.php'; ?>

