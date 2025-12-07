<?php
/**
 * Login Page
 * Modern Blog System 2025
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/User.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(getBaseUrl() . '/admin');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        
        $userData = $user->login($username, $password);
        
        if ($userData) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['user_role'] = $userData['role'];
            $_SESSION['user_email'] = $userData['email'];
            
            redirect(getBaseUrl() . '/admin');
        } else {
            $error = 'Invalid username or password';
        }
    }
}

$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="container">
        <div class="login-container" style="max-width: 400px; margin: 50px auto; padding: 2rem; background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; margin-bottom: 2rem;">Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="padding: 1rem; background: #fee; color: #c33; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="padding: 1rem; background: #efe; color: #3c3; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo e($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="username" style="display: block; margin-bottom: 0.5rem;">Username</label>
                    <input type="text" id="username" name="username" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="password" style="display: block; margin-bottom: 0.5rem;">Password</label>
                    <input type="password" id="password" name="password" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                </div>
                
                <button type="submit" class="btn" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
                    Login
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: #666;">
                Default: admin / admin123
            </p>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

