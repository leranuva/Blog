<?php
/**
 * Generate Random Posts Script
 * Creates sample posts with random content for testing
 * 
 * Usage: php scripts/generate-posts.php [number_of_posts]
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/Category.php';

// Get number of posts to generate (default: 20)
$numberOfPosts = isset($argv[1]) ? (int)$argv[1] : 20;

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get categories
$category = new Category($db);
$categories = $category->getAll();

if (empty($categories)) {
    die("Error: No categories found. Please run the database schema first.\n");
}

// Get admin user
$stmt = $db->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$adminUser = $stmt->fetch();

if (!$adminUser) {
    die("Error: No admin user found. Please run the database schema first.\n");
}

$authorId = $adminUser['id'];

// Sample data arrays
// More diverse titles with different topics
$titles = [
    // Technology
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
    // Lifestyle
    "10 Morning Habits of Highly Productive Developers",
    "Work-Life Balance in the Tech Industry",
    "Remote Work: Tips for Staying Productive",
    "Healthy Lifestyle for Programmers",
    "Time Management for Software Developers",
    "Building a Personal Brand as a Developer",
    "Networking Tips for Tech Professionals",
    "Mental Health in Software Development",
    "Learning New Technologies: A Developer's Guide",
    "Career Growth in Tech: What to Expect",
    // Food
    "Best Coffee Shops for Remote Developers",
    "Healthy Snacks for Long Coding Sessions",
    "Meal Prep Ideas for Busy Developers",
    "Tech Meetups: Where Food Meets Code",
    "Cooking for Developers: Quick Recipes",
    // Travel
    "Best Destinations for Digital Nomads",
    "Tech Conferences Worth Traveling For",
    "Working Remotely: A Travel Guide",
    "Co-working Spaces Around the World",
    "Travel Tips for Conference Attendees",
    // Design
    "UI/UX Design Trends for 2025",
    "Color Theory in Web Design",
    "Typography Best Practices",
    "Creating Engaging User Experiences",
    "Design Systems: Building Consistency"
];

$excerpts = [
    "Discover the latest trends shaping the future of web development and how they can transform your projects.",
    "Learn essential techniques that will make you a more efficient and effective developer.",
    "A comprehensive guide to building websites that work perfectly on all devices and screen sizes.",
    "Master the powerful CSS Grid system and create complex layouts with ease.",
    "Explore security best practices to protect your applications from common vulnerabilities.",
    "Dive deep into modern development practices and tools that can improve your workflow.",
    "Understanding the fundamentals and advanced concepts that every developer should know.",
    "Practical tips and tricks that will help you write better, more maintainable code.",
    "Stay up-to-date with the latest features and improvements in modern web technologies.",
    "Learn how to build fast, secure, and scalable applications using industry best practices."
];

$contentTemplates = [
    "In today's rapidly evolving digital landscape, staying ahead of the curve is essential for developers. This comprehensive guide explores the latest trends and technologies that are shaping the future of web development.\n\nWe'll dive deep into practical examples, real-world use cases, and actionable insights that you can apply to your projects immediately. Whether you're a beginner or an experienced developer, there's something valuable here for everyone.\n\n## Key Takeaways\n\n- Understanding modern development practices\n- Implementing best practices in your projects\n- Staying updated with industry trends\n- Building scalable and maintainable applications\n\n## Conclusion\n\nBy following these guidelines and staying curious about new technologies, you'll be well-equipped to tackle any development challenge that comes your way.",
    
    "This article provides a detailed exploration of essential concepts that every developer should master. We'll cover everything from basic principles to advanced techniques.\n\n## Getting Started\n\nBefore diving in, it's important to understand the fundamentals. These concepts form the foundation of everything else you'll learn.\n\n## Advanced Topics\n\nOnce you've mastered the basics, we can move on to more complex topics that will help you build sophisticated applications.\n\n## Best Practices\n\nFollowing industry best practices ensures that your code is maintainable, scalable, and secure. We'll explore proven strategies that top developers use.\n\n## Real-World Examples\n\nNothing beats learning from real-world examples. We'll examine actual projects and see how these concepts are applied in practice.",
    
    "Modern web development requires a deep understanding of both frontend and backend technologies. This guide will walk you through everything you need to know.\n\n## The Fundamentals\n\nEvery great application starts with a solid foundation. We'll explore the core concepts that make everything else possible.\n\n## Building Your Skills\n\nDeveloping expertise takes time and practice. We'll discuss effective learning strategies and resources that can accelerate your growth.\n\n## Common Challenges\n\nEvery developer faces challenges. We'll identify common pitfalls and provide solutions to help you overcome them.\n\n## Looking Forward\n\nThe technology landscape is constantly evolving. We'll discuss what's coming next and how to prepare for it.",
    
    "Performance optimization is crucial for creating great user experiences. This article covers proven techniques for making your applications faster and more efficient.\n\n## Why Performance Matters\n\nUsers expect fast, responsive applications. Poor performance can lead to lost users and decreased engagement.\n\n## Optimization Techniques\n\nWe'll explore various techniques for improving performance, from code-level optimizations to infrastructure improvements.\n\n## Measuring Success\n\nYou can't improve what you don't measure. We'll discuss tools and metrics for tracking performance improvements.\n\n## Case Studies\n\nReal-world examples show how performance optimization can dramatically improve user experience and business metrics.",
    
    "Security should be a top priority for every developer. This comprehensive guide covers essential security practices and common vulnerabilities to avoid.\n\n## Security Fundamentals\n\nUnderstanding security basics is the first step toward building secure applications.\n\n## Common Vulnerabilities\n\nWe'll examine common security issues and learn how to prevent them in your applications.\n\n## Best Practices\n\nFollowing security best practices helps protect your applications and users from threats.\n\n## Tools and Resources\n\nWe'll explore tools and resources that can help you build and maintain secure applications."
];

$categoriesData = array_map(function($cat) {
    return $cat['id'];
}, $categories);

// Generate posts
$post = new Post($db);
$generated = 0;
$errors = 0;

echo "Generating {$numberOfPosts} random posts...\n\n";

for ($i = 0; $i < $numberOfPosts; $i++) {
    try {
        // Random title
        $title = $titles[array_rand($titles)];
        
        // Generate unique slug
        $baseSlug = generateSlug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        // Check if slug exists
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
        
        // Random excerpt
        $excerpt = $excerpts[array_rand($excerpts)];
        
        // Random content with more variation
        $content = $contentTemplates[array_rand($contentTemplates)];
        
        // Add paragraphs with more variety
        $additionalParagraphs = [
            "\n\n## Practical Examples\n\nLet's look at some real-world examples that demonstrate these concepts in action. These examples will help you understand how to apply these principles in your own projects.",
            "\n\n## Common Mistakes to Avoid\n\nMany developers fall into common traps. We'll identify these pitfalls and show you how to avoid them, saving you time and frustration.",
            "\n\n## Tools and Resources\n\nHaving the right tools makes all the difference. We'll recommend essential tools and resources that can streamline your workflow.",
            "\n\n## Next Steps\n\nNow that you understand the basics, here's how to take your skills to the next level. We'll provide a roadmap for continued learning and improvement.",
            "\n\n## Community Insights\n\nWe've gathered insights from the developer community to bring you diverse perspectives and real-world experiences.",
            "\n\n## Troubleshooting Guide\n\nWhen things don't work as expected, this troubleshooting guide will help you identify and solve common issues quickly."
        ];
        
        // Add 1-3 random additional paragraphs
        $numParagraphs = rand(1, 3);
        $selectedParagraphs = array_rand($additionalParagraphs, min($numParagraphs, count($additionalParagraphs)));
        if (!is_array($selectedParagraphs)) {
            $selectedParagraphs = [$selectedParagraphs];
        }
        foreach ($selectedParagraphs as $index) {
            $content .= $additionalParagraphs[$index];
        }
        
        // Random category
        $categoryId = $categoriesData[array_rand($categoriesData)];
        
        // Random status (70% published, 20% draft, 10% archived)
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
        
        // Random featured image (use existing images)
        $imageNumber = rand(1, 10);
        $featuredImage = null; // Can be set to actual image path if needed
        
        // Set post properties
        $post->title = $title;
        $post->slug = $slug;
        $post->excerpt = $excerpt;
        $post->content = $content;
        $post->featured_image = $featuredImage;
        $post->author_id = $authorId;
        $post->category_id = $categoryId;
        $post->status = $status;
        $post->published_at = $publishedAt;
        
        // Create post
        if ($post->create()) {
            $generated++;
            echo "✓ Created: {$title}\n";
            
            // Add random views for published posts
            if ($status === 'published') {
                $views = rand(10, 5000);
                $db->exec("UPDATE posts SET views = {$views} WHERE id = {$post->id}");
            }
        } else {
            $errors++;
            echo "✗ Failed to create: {$title}\n";
        }
        
    } catch (Exception $e) {
        $errors++;
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Posts generated: {$generated}\n";
echo "Errors: {$errors}\n";
echo "Total attempted: {$numberOfPosts}\n";

if ($generated > 0) {
    echo "\n✓ Success! You can now view the posts on your blog.\n";
}

