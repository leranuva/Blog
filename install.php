<?php
/**
 * Installation Script
 * Modern Blog System 2025
 * 
 * Run this file once to set up the database
 * Access: http://localhost:8000/install.php
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'blog_db';

// Colors for CLI output
$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$reset = "\033[0m";

echo "=== Blog Installation Script ===\n\n";

// Step 1: Connect to MySQL (without database)
try {
    echo "Step 1: Connecting to MySQL...\n";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "{$green}✓ Connected successfully{$reset}\n\n";
} catch (PDOException $e) {
    die("{$red}✗ Connection failed: " . $e->getMessage() . "{$reset}\n");
}

// Step 2: Create database
try {
    echo "Step 2: Creating database '$db_name'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "{$green}✓ Database created successfully{$reset}\n\n";
} catch (PDOException $e) {
    die("{$red}✗ Failed to create database: " . $e->getMessage() . "{$reset}\n");
}

// Step 3: Select database
try {
    echo "Step 3: Selecting database...\n";
    $pdo->exec("USE `$db_name`");
    echo "{$green}✓ Database selected{$reset}\n\n";
} catch (PDOException $e) {
    die("{$red}✗ Failed to select database: " . $e->getMessage() . "{$reset}\n");
}

// Step 4: Read and execute schema
try {
    echo "Step 4: Reading schema file...\n";
    $schema_file = __DIR__ . '/database/schema.sql';
    
    if (!file_exists($schema_file)) {
        die("{$red}✗ Schema file not found: $schema_file{$reset}\n");
    }
    
    $schema = file_get_contents($schema_file);
    
    // Remove CREATE DATABASE and USE statements from schema
    $schema = preg_replace('/CREATE DATABASE.*?;/i', '', $schema);
    $schema = preg_replace('/USE.*?;/i', '', $schema);
    
    echo "Step 5: Executing schema...\n";
    
    // Remove comments and empty lines
    $schema = preg_replace('/--.*$/m', '', $schema);
    $schema = preg_replace('/\/\*.*?\*\//s', '', $schema);
    
    // Split by semicolon and execute each statement
    $statements = preg_split('/;\s*(?=[A-Z])/i', $schema);
    
    $executed = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || 
            stripos($statement, 'CREATE DATABASE') !== false || 
            stripos($statement, 'USE ') !== false) {
            continue;
        }
        
        // Remove trailing semicolon if present
        $statement = rtrim($statement, ';');
        
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $executed++;
            } catch (PDOException $e) {
                // Ignore "table already exists" errors
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "{$yellow}Warning: " . substr($e->getMessage(), 0, 100) . "...{$reset}\n";
                }
            }
        }
    }
    
    echo "{$green}✓ Schema executed successfully ({$executed} statements){$reset}\n\n";
    
} catch (Exception $e) {
    die("{$red}✗ Failed to execute schema: " . $e->getMessage() . "{$reset}\n");
}

// Step 6: Verify installation
try {
    echo "Step 6: Verifying installation...\n";
    
    $tables = ['users', 'categories', 'posts', 'comments', 'tags', 'post_tags', 'newsletter_subscribers'];
    $all_tables_exist = true;
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            echo "{$yellow}Warning: Table '$table' not found{$reset}\n";
            $all_tables_exist = false;
        }
    }
    
    if ($all_tables_exist) {
        echo "{$green}✓ All tables created successfully{$reset}\n\n";
    }
    
    // Check for admin user
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "{$green}✓ Admin user exists{$reset}\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n";
        echo "  {$yellow}⚠ Please change the password after first login!{$reset}\n\n";
    }
    
} catch (PDOException $e) {
    echo "{$yellow}Warning: Could not verify installation: " . $e->getMessage() . "{$reset}\n";
}

echo "\n{$green}=== Installation Complete! ==={$reset}\n\n";
echo "Next steps:\n";
echo "1. Update config/database.php if needed\n";
echo "2. Update config/config.php with your BASE_URL\n";
echo "3. Access your blog at: http://localhost:8000\n";
echo "4. Login at: http://localhost:8000/login.php\n";
echo "5. Admin panel: http://localhost:8000/admin\n\n";
echo "{$yellow}⚠ Remember to delete or protect install.php after installation!{$reset}\n";

