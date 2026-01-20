<?php
/**
 * Registration Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$errors = [];
$formData = [
    'first_name' => '',
    'last_name' => '',
    'username' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Sanitize inputs
        $formData['first_name'] = sanitize($_POST['first_name'] ?? '');
        $formData['last_name'] = sanitize($_POST['last_name'] ?? '');
        $formData['username'] = sanitize($_POST['username'] ?? '');
        $formData['email'] = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate first name
        if (empty($formData['first_name'])) {
            $errors[] = 'First name is required.';
        } elseif (strlen($formData['first_name']) < 2) {
            $errors[] = 'First name must be at least 2 characters.';
        }
        
        // Validate last name
        if (empty($formData['last_name'])) {
            $errors[] = 'Last name is required.';
        } elseif (strlen($formData['last_name']) < 2) {
            $errors[] = 'Last name must be at least 2 characters.';
        }
        
        // Validate username
        if (empty($formData['username'])) {
            $errors[] = 'Username is required.';
        } elseif (strlen($formData['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $formData['username'])) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        }
        
        // Validate email
        if (empty($formData['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($formData['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        // Validate password
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } else {
            $passwordErrors = validatePassword($password);
            $errors = array_merge($errors, $passwordErrors);
        }
        
        // Confirm password match
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        // Terms acceptance
        if (!isset($_POST['terms'])) {
            $errors[] = 'You must accept the Terms and Conditions.';
        }
        
        // If no errors, register user
        if (empty($errors)) {
            $result = registerUser(
                $formData['username'],
                $formData['email'],
                $password,
                $formData['first_name'],
                $formData['last_name']
            );
            
            if ($result['success']) {
                header('Location: /auth/login.php?registered=1');
                exit;
            } else {
                $errors[] = $result['error'];
            }
        }
    }
}

$pageTitle = 'Create Account';
include __DIR__ . '/../includes/header.php';
?>

        <!-- Registration Section -->
        <section class="auth-section" aria-labelledby="register-heading">
            <div class="auth-container">
                <h2 id="register-heading">Create Your Account</h2>
                <p class="auth-subtitle">Join millions of music lovers on SoundVibe</p>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" role="alert">
                        <span class="alert-icon">⚠️</span>
                        <ul class="error-list">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="<?php echo htmlspecialchars($formData['first_name']); ?>"
                                   required 
                                   autocomplete="given-name"
                                   placeholder="John">
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="<?php echo htmlspecialchars($formData['last_name']); ?>"
                                   required 
                                   autocomplete="family-name"
                                   placeholder="Doe">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($formData['username']); ?>"
                               required 
                               autocomplete="username"
                               pattern="[a-zA-Z0-9_]+"
                               placeholder="johndoe123">
                        <small class="form-hint">Letters, numbers, and underscores only</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($formData['email']); ?>"
                               required 
                               autocomplete="email"
                               placeholder="john@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               placeholder="Create a strong password">
                        <small class="form-hint">Min 8 characters, include uppercase, lowercase, and number</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               required 
                               autocomplete="new-password"
                               placeholder="Confirm your password">
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="/terms.php" target="_blank">Terms & Conditions</a> and <a href="/privacy.php" target="_blank">Privacy Policy</a> *</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="/auth/login.php">Sign in</a></p>
                </div>
            </div>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
