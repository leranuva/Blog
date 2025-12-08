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

// Get hero slider posts (latest 4 for hero slider)
$heroSliderPosts = $post->getRecent(4);

// Get popular posts for sidebar
$popularPosts = $post->getPopular(5);

// Get categories
$categories = $category->getAll();

// Get recent posts for sidebar
$recentPosts = $post->getRecent(5);

// Get popular tags (tags most used in posts)
$popularTagsQuery = "SELECT t.id, t.name, t.slug, COUNT(pt.post_id) as post_count
                      FROM tags t
                      LEFT JOIN post_tags pt ON t.id = pt.tag_id
                      LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                      GROUP BY t.id, t.name, t.slug
                      HAVING post_count > 0
                      ORDER BY post_count DESC, t.name ASC
                      LIMIT 12";
$popularTagsStmt = $db->prepare($popularTagsQuery);
$popularTagsStmt->execute();
$popularTags = $popularTagsStmt->fetchAll(PDO::FETCH_ASSOC);

// Page title
$pageTitle = 'Home';

// Include header
include __DIR__ . '/includes/header.php';
?>

<!-- --------------------------Main Site Section----------------------------- -->
<main>
    <!-- --------------------------Hero Section----------------------------- -->
    <?php if (!empty($heroSliderPosts)): ?>
    <div class="slider-container">
        <div class="slider-control left inactive"></div>
        <div class="slider-control right"></div>
        <ul class="slider-pagi"></ul>
        
        <div class="slider">
            <?php 
            $overlayColors = [
                'rgb(233, 156, 126)',
                'rgb(225, 204, 174)',
                'rgb(173, 197, 205)',
                'rgb(203, 198, 195)',
                'rgb(180, 200, 180)',
                'rgb(200, 180, 200)'
            ];
            foreach ($heroSliderPosts as $index => $heroPost): 
                $imageIds = [
                    'photo-1469854523086-cc02fe5d8800',
                    'photo-1488646953014-85cb44e25828',
                    'photo-1506905925346-21bda4d32df4',
                    'photo-1469474968028-56623f02e42e',
                    'photo-1507525428034-b723cf961d3e',
                    'photo-1519904981063-e0fcf4b7c936'
                ];
                $imageId = $imageIds[$index % count($imageIds)];
                $imageUrl = $heroPost['featured_image'] ? uploadUrl($heroPost['featured_image']) : "https://images.unsplash.com/{$imageId}?w=1920&h=1080&fit=crop&auto=format&q=80";
                $excerpt = $heroPost['excerpt'] ?: truncate(strip_tags($heroPost['content']), 200);
                $overlayColor = $overlayColors[$index % count($overlayColors)];
            ?>
            <div class="slide slide-<?php echo $index; ?> <?php echo $index === 0 ? 'active' : ''; ?>" data-slide-index="<?php echo $index; ?>">
                <div class="slide__bg" style="background-image: url('<?php echo $imageUrl; ?>');"></div>
                <div class="slide__content">
                    <svg class="slide__overlay" viewBox="0 0 720 405" preserveAspectRatio="xMaxYMax slice">
                        <path class="slide__overlay-path" d="M0,0 150,0 500,405 0,405" fill="<?php echo $overlayColor; ?>" />
                    </svg>
                    <div class="slide__text">
                        <h2 class="slide__text-heading"><?php echo e($heroPost['title']); ?></h2>
                        <p class="slide__text-desc"><?php echo e($excerpt); ?></p>
                        <a class="slide__text-link" href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($heroPost['slug']); ?>">Read More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
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
                
                <div class="carousel-container-3d">
                    <button class="nav-arrow-3d left" aria-label="Previous card">‹</button>
                    <div class="carousel-track-3d">
                        <?php foreach ($featuredPosts as $index => $featured): 
                        $publishedDate = $featured['published_at'] ? strtotime($featured['published_at']) : time();
                        $day = date('d', $publishedDate);
                        $month = date('M', $publishedDate);
                        $excerpt = $featured['excerpt'] ?: truncate(strip_tags($featured['content']), 120);
                        $readTime = max(1, ceil(str_word_count(strip_tags($featured['content'])) / 200)); // Estimate reading time
                        
                        // Use real images from Unsplash with travel/tourism themes
                        $imageIds = [
                            'photo-1469854523086-cc02fe5d8800', // Travel
                            'photo-1488646953014-85cb44e25828', // Mountains
                            'photo-1506905925346-21bda4d32df4', // Beach
                            'photo-1469474968028-56623f02e42e', // Nature
                            'photo-1507525428034-b723cf961d3e', // Tropical
                            'photo-1519904981063-e0fcf4b7c936', // City
                            'photo-1501594907352-04cda38ebc29', // Landscape
                            'photo-1476514525534-6b61f6a0d5c5', // Adventure
                            'photo-1504280390367-361c6d9f38f4', // Camping
                            'photo-1519681393784-d120267933ba', // Sunset
                            'photo-1506905925346-21bda4d32df4', // Ocean
                            'photo-1501594907352-04cda38ebc29'  // Forest
                        ];
                        $imageId = $imageIds[$featured['id'] % count($imageIds)];
                        
                        // Fallback images - handle different extensions
                        $postNum = (($featured['id'] % 10) + 1);
                        $fallbackImages = [
                            1 => 'Blog-post/post-1.jpg',
                            2 => 'Blog-post/post-2.jpg',
                            3 => 'Blog-post/post-3.jpg',
                            4 => 'Blog-post/post-4.png',
                            5 => 'Blog-post/post-5.png',
                            6 => 'Blog-post/post-6.png',
                            7 => 'Blog-post/post-7.jpg',
                            8 => 'Blog-post/post-8.jpg',
                            9 => 'Blog-post/post-9.jpg',
                            10 => 'Blog-post/post-10.jpg'
                        ];
                        $fallbackImage = asset($fallbackImages[$postNum] ?? 'Blog-post/post-1.jpg');
                        $imageUrl = $featured['featured_image'] ? uploadUrl($featured['featured_image']) : "https://images.unsplash.com/{$imageId}?w=800&h=600&fit=crop&auto=format&q=80";
                    ?>
                    <article class="card card-3d" data-index="<?php echo $index; ?>">
                        <header class="card__thumb">
                            <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($featured['slug']); ?>">
                                <div class="card__image-overlay"></div>
                                <img src="<?php echo $imageUrl; ?>" 
                                     alt="<?php echo e($featured['title']); ?>" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='<?php echo $fallbackImage; ?>';" />
                            </a>
                        </header>
                        <div class="card__date">
                            <span class="card__date__day"><?php echo $day; ?></span>
                            <span class="card__date__month"><?php echo $month; ?></span>
                        </div>
                        <div class="card__body">
                            <div class="card__category">
                                <a href="<?php echo getBaseUrl(); ?>/category.php?slug=<?php echo e($featured['category_slug']); ?>">
                                    <?php echo e($featured['category_name']); ?>
                                </a>
                            </div>
                            <div class="card__title">
                                <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($featured['slug']); ?>">
                                    <?php echo e($featured['title']); ?>
                                </a>
                            </div>
                            <div class="card__subtitle">By <?php echo e($featured['author_full_name'] ?: $featured['author_name']); ?></div>
                            <p class="card__description"><?php echo e($excerpt); ?></p>
                        </div>
                        <footer class="card__footer">
                            <span class="icon icon--time"><?php echo $readTime; ?> min</span>
                            <span class="icon icon--comment">
                                <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($featured['slug']); ?>#comments">
                                    <?php echo isset($featured['comment_count']) ? (int)$featured['comment_count'] : 0; ?> comments
                                </a>
                            </span>
                        </footer>
                    </article>
                        <?php endforeach; ?>
                    </div>
                    <button class="nav-arrow-3d right" aria-label="Next card">›</button>
                </div>
                
                <?php if (!empty($featuredPosts)): ?>
                <div class="post-info-3d">
                    <h2 class="post-title-3d"><?php echo e($featuredPosts[0]['title']); ?></h2>
                    <p class="post-category-3d"><?php echo e($featuredPosts[0]['category_name']); ?></p>
                </div>
                
                <div class="dots-3d">
                    <?php foreach ($featuredPosts as $index => $featured): ?>
                    <div class="dot-3d <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- -------------X------------Featured Carousel-----------X--------------- -->

    <!----------------------------- Posts Section --------------------------- -->
    <section class="posts-section" id="posts">
        <?php if (empty($posts)): ?>
            <article class="post-content empty-state" data-aos="fade-up">
                <h2>No posts found</h2>
                <p>There are no published posts yet. Check back soon!</p>
            </article>
        <?php else: ?>
            <?php 
            $postsToShow = array_slice($posts, 0, 8);
            ?>
            <div class="head">
                <h2>Latest Articles</h2>
                <div class="controls">
                    <button id="prev" class="nav-btn" aria-label="Prev">‹</button>
                    <button id="next" class="nav-btn" aria-label="Next">›</button>
                </div>
            </div>
            <div class="slider">
                <div class="track" id="track">
                    <?php foreach ($postsToShow as $index => $postItem): 
                        $imageUrl = $postItem['featured_image'] ? uploadUrl($postItem['featured_image']) : asset('Blog-post/blog' . (($postItem['id'] % 4) + 1) . '.png');
                        $authorImage = 'https://picsum.photos/id/' . (100 + $postItem['id']) . '/133/269';
                        $excerpt = $postItem['excerpt'] ?: truncate(strip_tags($postItem['content']), 100);
                        $isActive = $index === 0 ? 'active' : '';
                    ?>
                    <article class="project-card" <?php echo $isActive ? 'active' : ''; ?>>
                        <img class="project-card__bg" src="<?php echo $imageUrl; ?>" alt="<?php echo e($postItem['title']); ?>">
                        <div class="project-card__content">
                            <img class="project-card__thumb" src="<?php echo $authorImage; ?>" alt="<?php echo e($postItem['author_full_name'] ?: $postItem['author_name']); ?>">
                            <div>
                                <h3 class="project-card__title">
                                    <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($postItem['slug']); ?>">
                                        <?php echo e($postItem['title']); ?>
                                    </a>
                                </h3>
                                <p class="project-card__desc"><?php echo e($excerpt); ?></p>
                                <a href="<?php echo getBaseUrl(); ?>/post.php?slug=<?php echo e($postItem['slug']); ?>" class="project-card__btn">Read More</a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="dots" id="dots"></div>
        <?php endif; ?>
    </section>
    <!----------------------------- Posts Section --------------------------- -->

    <!----------------------------- Sidebar Section --------------------------- -->
    <section class="sidebar-section">
        <div class="posts-grid-layout">
            <aside class="posts-sidebar-grid">
                <!-- Categories -->
                <div class="sidebar-widget category">
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
                <div class="sidebar-widget popular-post">
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
                <div class="sidebar-widget newsletter" data-aos="fade-up" data-aos-delay="300">
                    <h2>Newsletter</h2>
                    <div class="form-element">
                        <form id="newsletter-form-sidebar" method="POST" action="<?php echo getBaseUrl(); ?>/api/newsletter.php">
                            <input type="email" name="email" class="input-element" placeholder="Email" required />
                            <button type="submit" class="btn form-btn">Subscribe</button>
                        </form>
                    </div>
                </div>

                <!-- Popular Tags -->
                <?php if (!empty($popularTags)): ?>
                <div class="sidebar-widget popular-tags">
                    <h2>Popular Tags</h2>
                    <div class="tags flex-row">
                        <?php foreach ($popularTags as $index => $tag): ?>
                        <a href="<?php echo getBaseUrl(); ?>/tag.php?slug=<?php echo e($tag['slug']); ?>" 
                           class="tag" 
                           data-aos="flip-up" 
                           data-aos-delay="<?php echo ($index + 1) * 100; ?>"
                           title="<?php echo e($tag['name']); ?> (<?php echo $tag['post_count']; ?> posts)">
                            <?php echo e($tag['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </section>
    <!----------------------------- Sidebar Section ------------------------------>

</main>
<!-- --------------X-----------Main Site Section--------------X-------------- -->

<?php include __DIR__ . '/includes/footer.php'; ?>

