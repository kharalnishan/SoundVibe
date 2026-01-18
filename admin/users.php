<?php
/**
 * Admin - User Management
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$pdo = getDBConnection();
$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);
    
    if ($userId > 0 && $userId !== $_SESSION['user_id']) {
        switch ($action) {
            case 'change_role':
                $newRole = sanitize($_POST['new_role'] ?? '');
                if (in_array($newRole, ['admin', 'member', 'normal'])) {
                    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->execute([$newRole, $userId]);
                    $message = 'User role updated successfully.';
                    $messageType = 'success';
                }
                break;
                
            case 'toggle_active':
                $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$userId]);
                $message = 'User status updated.';
                $messageType = 'success';
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $message = 'User deleted successfully.';
                $messageType = 'success';
                break;
        }
    }
}

// Get all users
$search = sanitize($_GET['search'] ?? '');
$roleFilter = sanitize($_GET['role'] ?? '');

$sql = "SELECT id, username, email, first_name, last_name, role, is_active, created_at, last_login FROM users WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
}

if ($roleFilter) {
    $sql .= " AND role = ?";
    $params[] = $roleFilter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = 'Manage Users';
include __DIR__ . '/../includes/header.php';
?>

        <section class="admin-section">
            <div class="admin-header">
                <h1>Manage Users</h1>
                <a href="/admin/dashboard.php" class="back-link">← Back to Dashboard</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <span class="alert-icon"><?php echo $messageType === 'success' ? '✓' : '⚠️'; ?></span>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="admin-filters">
                <form method="GET" class="filter-form">
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="role">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="member" <?php echo $roleFilter === 'member' ? 'selected' : ''; ?>>Member</option>
                        <option value="normal" <?php echo $roleFilter === 'normal' ? 'selected' : ''; ?>>Normal</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($search || $roleFilter): ?>
                        <a href="/admin/users.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Users Table -->
            <div class="admin-panel">
                <table class="admin-table full-width">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="<?php echo !$user['is_active'] ? 'inactive-row' : ''; ?>">
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="new_role" onchange="this.form.submit()" <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            <option value="member" <?php echo $user['role'] === 'member' ? 'selected' : ''; ?>>Member</option>
                                            <option value="normal" <?php echo $user['role'] === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                                <td>
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_active">
                                            <button type="submit" class="btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>">
                                                <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                        </form>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-sm btn-danger">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">(You)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
