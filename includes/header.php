<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get categories for navigation
$category = new Category($db);
$categories = $category->getAll();

// Get current page info
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Blog moderno con las últimas noticias y artículos sobre tecnología, lifestyle, software y más">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?>Blooger</title>

    <!--Custom Style-->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>" />
    <link rel="stylesheet" href="<?php echo asset('css/mobile-style.css'); ?>" />

    <!--Font Awesome-->
    <link rel="stylesheet" href="<?php echo asset('css/all.css'); ?>" />

    <!--Owl-Carousel-->
    <link rel="stylesheet" href="<?php echo asset('css/owl.carousel.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo asset('css/owl.theme.default.min.css'); ?>" />

    <!--AOS Library-->
    <link rel="stylesheet" href="<?php echo asset('css/aos.css'); ?>" />
    
    <!--Swiper Library-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!--Notifications-->
    <link rel="stylesheet" href="<?php echo asset('css/notifications.css'); ?>" />
    
    <!--Header Styles - Loaded last to override other styles -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/header.css?v=<?php echo time(); ?>" />
    
    <!--Hero Styles - Hero section positioning -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/hero.css?v=<?php echo time(); ?>" />
    
    <!--Carousel Styles - Featured posts carousel -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/carousel.css?v=<?php echo time(); ?>" />
    
    <!--Posts Styles - Modern professional design -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/posts.css?v=<?php echo time(); ?>" />
    
    <!--Posts Center Slider Styles - Center-mode slider for posts -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/posts-center-slider.css?v=<?php echo time(); ?>" />
    
    <!--Sidebar Styles - Modern sidebar design -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/css/sidebar.css?v=<?php echo time(); ?>" />
</head>
<body>
    <!-- --------------------------Navigation----------------------------- -->
    <nav class="nav" role="navigation" aria-label="Main navigation">
        <div class="nav-menu flex-row">
            <div class="nav-brand">
                <a href="<?php echo getBaseUrl(); ?>" aria-label="Blooger Home">
                    Blooger
                </a>
            </div>
            
            <button class="toggle-collapse" aria-label="Toggle navigation menu" aria-expanded="false">
                <div class="toggle-icons">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </div>
            </button>
            
            <ul class="nav-items" role="menubar">
                <li class="nav-link" role="none">
                    <a href="<?php echo getBaseUrl(); ?>" role="menuitem">Home</a>
                </li>
                <li class="nav-link" role="none">
                    <a href="<?php echo getBaseUrl(); ?>/category.php" role="menuitem">Category</a>
                </li>
                <li class="nav-link" role="none">
                    <a href="<?php echo getBaseUrl(); ?>/archive.php" role="menuitem">Archive</a>
                </li>
                <li class="nav-link" role="none">
                    <a href="<?php echo getBaseUrl(); ?>/pages.php" role="menuitem">Pages</a>
                </li>
                <li class="nav-link" role="none">
                    <a href="<?php echo getBaseUrl(); ?>/contact.php" role="menuitem">Contact Us</a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-link" role="none">
                        <a href="<?php echo getBaseUrl(); ?>/admin" role="menuitem">Admin</a>
                    </li>
                    <li class="nav-link" role="none">
                        <a href="<?php echo getBaseUrl(); ?>/logout.php" role="menuitem">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-link" role="none">
                        <a href="<?php echo getBaseUrl(); ?>/login.php" role="menuitem">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <div class="social" aria-label="Social media links">
                <a href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                </a>
                <a href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram" aria-hidden="true"></i>
                </a>
                <a href="#" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-twitter" aria-hidden="true"></i>
                </a>
                <a href="#" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-youtube" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </nav>
    <!-- ---------------X----------Navigation--------------X-------------- -->
    
    <!--Header JavaScript-->
    <script src="<?php echo getBaseUrl(); ?>/js/header.js?v=<?php echo time(); ?>"></script>

