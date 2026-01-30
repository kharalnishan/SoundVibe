<?php
/**
 * User Profile Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

// Require user to be logged in
requireLogin();

$user = getCurrentUser();
$errors = [];
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_profile') {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            
            // Validation
            if (empty($name)) {
                $errors[] = 'Name is required.';
            }
            
            if (empty($email)) {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            
            // Check if email is taken by another user
            if (empty($errors) && $email !== $user['email']) {
                $conn = getDBConnection();
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $user['id']]);
                if ($stmt->fetch()) {
                    $errors[] = 'This email is already registered to another account.';
                }
            }
            
            // Update profile: store a single `full_name` field in DB (preferred)
            if (empty($errors)) {
                try {
                    $conn = getDBConnection();
                    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$name, $email, $user['id']]);

                    // Update session data
                    $_SESSION['user_name'] = trim($name) ?: ($user['username'] ?? '');
                    $_SESSION['user_email'] = $email;

                    $success = 'Profile updated successfully!';

                    // Refresh user data
                    $user = getCurrentUser();
                } catch (PDOException $e) {
                    $errors[] = 'An error occurred. Please try again.';
                }
            }
        } elseif ($action === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validation
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required.';
            }
            
            if (empty($newPassword)) {
                $errors[] = 'New password is required.';
            } elseif (!validatePassword($newPassword)) {
                $errors[] = 'New password must be at least 8 characters with uppercase, lowercase, and number.';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match.';
            }
            
            // Verify current password
            if (empty($errors)) {
                $conn = getDBConnection();
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user['id']]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!password_verify($currentPassword, $userData['password'])) {
                    $errors[] = 'Current password is incorrect.';
                }
            }
            
            // Update password
            if (empty($errors)) {
                try {
                    $conn = getDBConnection();
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$hashedPassword, $user['id']]);
                    
                    $success = 'Password changed successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'An error occurred. Please try again.';
                }
            }
        } elseif ($action === 'delete_account') {
            $confirmDelete = $_POST['confirm_delete'] ?? '';
            $password = $_POST['delete_password'] ?? '';
            
            if ($confirmDelete !== 'DELETE') {
                $errors[] = 'Please type DELETE to confirm account deletion.';
            }
            
            // Verify password
            if (empty($errors)) {
                $conn = getDBConnection();
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user['id']]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!password_verify($password, $userData['password'])) {
                    $errors[] = 'Password is incorrect.';
                }
            }
            
            // Delete account
            if (empty($errors)) {
                try {
                    $conn = getDBConnection();
                    
                    // Delete user favorites first (foreign key constraint)
                    $stmt = $conn->prepare("DELETE FROM user_favorites WHERE user_id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Delete user sessions
                    $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Delete the user
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Log out
                    session_destroy();
                    header('Location: /index.php?deleted=1');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = 'An error occurred. Please try again.';
                }
            }
        }
    }
}

// Get user stats
$conn = getDBConnection();

// Count user favorites
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_favorites WHERE user_id = ?");
$stmt->execute([$user['id']]);
$favoritesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$pageTitle = 'My Profile';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="profile-title">
            <h1 id="profile-title">My Profile</h1>
            <p>Manage your account settings</p>
        </section>

        <!-- Profile Content -->
        <section class="profile-section" aria-labelledby="profile-heading">
            <div class="profile-container">
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Overview -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <div class="profile-info">
                            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $favoritesCount; ?></span>
                            <span class="stat-label">Favorites</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                            <span class="stat-label">Member Since</span>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="profile-card">
                    <h3>Edit Profile</h3>
                    <form method="POST" action="/profile.php" class="profile-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="profile-card">
                    <h3>Change Password</h3>
                    <form method="POST" action="/profile.php" class="profile-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <small>At least 8 characters with uppercase, lowercase, and number</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>

                <!-- Delete Account -->
                <div class="profile-card danger-zone">
                    <h3>Danger Zone</h3>
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    
                    <button type="button" class="btn btn-danger" onclick="toggleDeleteForm()">
                        Delete My Account
                    </button>
                    
                    <form method="POST" action="/profile.php" class="delete-form" id="deleteForm" style="display: none;">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="delete_account">
                        
                        <div class="form-group">
                            <label for="delete_password">Enter your password</label>
                            <input type="password" id="delete_password" name="delete_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_delete">Type DELETE to confirm</label>
                            <input type="text" id="confirm_delete" name="confirm_delete" 
                                   placeholder="DELETE" required>
                        </div>

                        <button type="submit" class="btn btn-danger">
                            Permanently Delete Account
                        </button>
                    </form>
                </div>

            </div>
        </section>

        <script>
        function toggleDeleteForm() {
            const form = document.getElementById('deleteForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
        </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
