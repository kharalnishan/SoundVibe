<?php
/**
 * Admin Playlists Management
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

// Require admin access
requireAdmin();

$user = getCurrentUser();
$errors = [];
$success = '';

$conn = getDBConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            $name = sanitize($_POST['name'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $cover_image = sanitize($_POST['cover_image'] ?? '');
            $is_public = isset($_POST['is_public']) ? 1 : 0;
            $created_by = $user['id'];
            
            if (empty($name)) {
                $errors[] = 'Playlist name is required.';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO playlists (name, description, cover_image, is_public, created_by) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $description, $cover_image, $is_public, $created_by]);
                    $success = 'Playlist created successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to create playlist. Please try again.';
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['playlist_id'] ?? 0);
            $name = sanitize($_POST['name'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $cover_image = sanitize($_POST['cover_image'] ?? '');
            $is_public = isset($_POST['is_public']) ? 1 : 0;
            
            if (empty($name)) {
                $errors[] = 'Playlist name is required.';
            }
            
            if (empty($errors) && $id > 0) {
                try {
                    $stmt = $conn->prepare("UPDATE playlists SET name = ?, description = ?, cover_image = ?, is_public = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $cover_image, $is_public, $id]);
                    $success = 'Playlist updated successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to update playlist. Please try again.';
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['playlist_id'] ?? 0);
            
            if ($id > 0) {
                try {
                    // Delete playlist tracks first (foreign key constraint)
                    $stmt = $conn->prepare("DELETE FROM playlist_tracks WHERE playlist_id = ?");
                    $stmt->execute([$id]);
                    
                    // Delete the playlist
                    $stmt = $conn->prepare("DELETE FROM playlists WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Playlist deleted successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to delete playlist. Please try again.';
                }
            }
        }
    }
}

// Get playlists with search/filter
$search = sanitize($_GET['search'] ?? '');
$filter = sanitize($_GET['filter'] ?? '');

$sql = "SELECT p.*, u.name as creator_name,
        (SELECT COUNT(*) FROM playlist_tracks WHERE playlist_id = p.id) as track_count
        FROM playlists p
        LEFT JOIN users u ON p.created_by = u.id
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter === 'public') {
    $sql .= " AND p.is_public = 1";
} elseif ($filter === 'private') {
    $sql .= " AND p.is_public = 0";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get playlist for editing
$editPlaylist = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM playlists WHERE id = ?");
    $stmt->execute([$editId]);
    $editPlaylist = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = 'Manage Playlists';
include __DIR__ . '/../includes/header.php';
?>

        <section class="admin-section" aria-labelledby="playlists-title">
            <div class="admin-header">
                <h1 id="playlists-title">Manage Playlists</h1>
                <p>Create, edit, and manage platform playlists.</p>
            </div>

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

            <!-- Add/Edit Playlist Form -->
            <div class="admin-card">
                <h2><?php echo $editPlaylist ? 'Edit Playlist' : 'Create New Playlist'; ?></h2>
                <form method="POST" action="/admin/playlists.php" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $editPlaylist ? 'edit' : 'add'; ?>">
                    <?php if ($editPlaylist): ?>
                        <input type="hidden" name="playlist_id" value="<?php echo $editPlaylist['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Playlist Name *</label>
                        <input type="text" id="name" name="name" required
                               value="<?php echo htmlspecialchars($editPlaylist['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  placeholder="Playlist description..."><?php echo htmlspecialchars($editPlaylist['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="cover_image">Cover Image URL</label>
                        <input type="url" id="cover_image" name="cover_image" 
                               placeholder="https://example.com/cover.jpg"
                               value="<?php echo htmlspecialchars($editPlaylist['cover_image'] ?? ''); ?>">
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="is_public" value="1"
                                   <?php echo ($editPlaylist['is_public'] ?? 1) ? 'checked' : ''; ?>>
                            Public Playlist
                        </label>
                        <small>Public playlists are visible to all users</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editPlaylist ? 'Update Playlist' : 'Create Playlist'; ?>
                        </button>
                        <?php if ($editPlaylist): ?>
                            <a href="/admin/playlists.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Search/Filter -->
            <div class="admin-card">
                <form method="GET" action="/admin/playlists.php" class="filter-form">
                    <div class="filter-row">
                        <input type="text" name="search" placeholder="Search playlists..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <select name="filter">
                            <option value="">All Playlists</option>
                            <option value="public" <?php echo $filter === 'public' ? 'selected' : ''; ?>>Public Only</option>
                            <option value="private" <?php echo $filter === 'private' ? 'selected' : ''; ?>>Private Only</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if ($search || $filter): ?>
                            <a href="/admin/playlists.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Playlists Table -->
            <div class="admin-card">
                <h2>All Playlists (<?php echo count($playlists); ?>)</h2>
                
                <?php if (empty($playlists)): ?>
                    <p class="no-data">No playlists found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Playlist</th>
                                    <th>Tracks</th>
                                    <th>Creator</th>
                                    <th>Visibility</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($playlists as $playlist): ?>
                                    <tr>
                                        <td>
                                            <div class="playlist-cell">
                                                <?php if ($playlist['cover_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($playlist['cover_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($playlist['name']); ?>"
                                                         class="playlist-thumb">
                                                <?php else: ?>
                                                    <div class="playlist-thumb placeholder">
                                                        🎵
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($playlist['name']); ?></strong>
                                                    <?php if ($playlist['description']): ?>
                                                        <small><?php echo htmlspecialchars(substr($playlist['description'], 0, 50)); ?>...</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $playlist['track_count']; ?> tracks</td>
                                        <td><?php echo htmlspecialchars($playlist['creator_name'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <?php if ($playlist['is_public']): ?>
                                                <span class="badge badge-success">Public</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Private</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($playlist['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/admin/playlists.php?edit=<?php echo $playlist['id']; ?>" 
                                                   class="btn btn-small btn-primary">Edit</a>
                                                <form method="POST" action="/admin/playlists.php" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this playlist?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="playlist_id" value="<?php echo $playlist['id']; ?>">
                                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="admin-nav">
                <a href="/admin/dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
