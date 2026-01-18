<?php
/**
 * Admin Dashboard
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

// Require admin access
requireAdmin();

$pdo = getDBConnection();

// Get statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $stmt->fetch()['count'];

// Total artists
$stmt = $pdo->query("SELECT COUNT(*) as count FROM artists");
$stats['artists'] = $stmt->fetch()['count'];

// Total playlists
$stmt = $pdo->query("SELECT COUNT(*) as count FROM playlists");
$stats['playlists'] = $stmt->fetch()['count'];

// Unread messages
$stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
$stats['messages'] = $stmt->fetch()['count'];

// Recent users
$stmt = $pdo->query("SELECT id, username, email, role, created_at, last_login FROM users ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll();

// Recent messages
$stmt = $pdo->query("SELECT id, name, email, subject, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recentMessages = $stmt->fetchAll();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
?>

        <!-- Admin Dashboard -->
        <section class="admin-section" aria-labelledby="admin-title">
            <div class="admin-header">
                <h1 id="admin-title">Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($currentUser['first_name']); ?>!</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['users']); ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎤</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['artists']); ?></h3>
                        <p>Artists</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎵</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['playlists']); ?></h3>
                        <p>Playlists</p>
                    </div>
                </div>
                <div class="stat-card <?php echo $stats['messages'] > 0 ? 'has-alert' : ''; ?>">
                    <div class="stat-icon">📩</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['messages']); ?></h3>
                        <p>Unread Messages</p>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="admin-actions">
                <a href="/admin/users.php" class="admin-action-btn">
                    <span class="action-icon">👥</span>
                    Manage Users
                </a>
                <a href="/admin/artists.php" class="admin-action-btn">
                    <span class="action-icon">🎤</span>
                    Manage Artists
                </a>
                <a href="/admin/albums.php" class="admin-action-btn">
                    <span class="action-icon">💿</span>
                    Manage Albums
                </a>
                <a href="/admin/playlists.php" class="admin-action-btn">
                    <span class="action-icon">🎵</span>
                    Manage Playlists
                </a>
                <a href="/admin/messages.php" class="admin-action-btn">
                    <span class="action-icon">📩</span>
                    View Messages
                    <?php if ($stats['messages'] > 0): ?>
                        <span class="badge"><?php echo $stats['messages']; ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="admin-grid">
                <!-- Recent Users -->
                <div class="admin-panel">
                    <h2>Recent Users</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo $user['role']; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="/admin/users.php" class="view-all-link">View All Users →</a>
                </div>

                <!-- Recent Messages -->
                <div class="admin-panel">
                    <h2>Recent Messages</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentMessages)): ?>
                                <tr>
                                    <td colspan="4" class="no-data">No messages yet</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentMessages as $message): ?>
                                    <tr class="<?php echo !$message['is_read'] ? 'unread' : ''; ?>">
                                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($message['subject'], 0, 30)) . (strlen($message['subject']) > 30 ? '...' : ''); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($message['created_at'])); ?></td>
                                        <td>
                                            <?php if ($message['is_read']): ?>
                                                <span class="status-badge status-read">Read</span>
                                            <?php else: ?>
                                                <span class="status-badge status-unread">New</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <a href="/admin/messages.php" class="view-all-link">View All Messages →</a>
                </div>
            </div>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
