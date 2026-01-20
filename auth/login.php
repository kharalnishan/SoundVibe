<?php
/**
 * Login Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $result = loginUser($email, $password);
            
            if ($result['success']) {
                // Redirect to intended page or home
                $redirect = $_SESSION['redirect_after_login'] ?? '/index.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = $result['error'];
            }
        }
    }
}

$pageTitle = 'Login';
include __DIR__ . '/../includes/header.php';
?>

        <!-- Login Section -->
        <section class="auth-section" aria-labelledby="login-heading">
            <div class="auth-container">
                <h2 id="login-heading">Welcome Back</h2>
                <p class="auth-subtitle">Sign in to continue to SoundVibe</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error" role="alert">
                        <span class="alert-icon">⚠️</span>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success" role="alert">
                        <span class="alert-icon">✓</span>
                        Registration successful! Please log in.
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($email); ?>"
                               required 
                               autocomplete="email"
                               aria-describedby="email-error"
                               placeholder="Enter your email">
                        <span id="email-error" class="error-message" aria-live="polite"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               aria-describedby="password-error"
                               placeholder="Enter your password">
                        <span id="password-error" class="error-message" aria-live="polite"></span>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="/auth/register.php">Create one</a></p>
                </div>
            </div>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
