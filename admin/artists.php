<?php
/**
 * Admin Artists Management
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
            $genre = sanitize($_POST['genre'] ?? '');
            $bio = sanitize($_POST['bio'] ?? '');
            $image_url = sanitize($_POST['image_url'] ?? '');
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            if (empty($name)) {
                $errors[] = 'Artist name is required.';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO artists (name, genre, bio, image_url, featured, followers) VALUES (?, ?, ?, ?, ?, 0)");
                    $stmt->execute([$name, $genre, $bio, $image_url, $featured]);
                    $success = 'Artist added successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to add artist. Please try again.';
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['artist_id'] ?? 0);
            $name = sanitize($_POST['name'] ?? '');
            $genre = sanitize($_POST['genre'] ?? '');
            $bio = sanitize($_POST['bio'] ?? '');
            $image_url = sanitize($_POST['image_url'] ?? '');
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            if (empty($name)) {
                $errors[] = 'Artist name is required.';
            }
            
            if (empty($errors) && $id > 0) {
                try {
                    $stmt = $conn->prepare("UPDATE artists SET name = ?, genre = ?, bio = ?, image_url = ?, featured = ? WHERE id = ?");
                    $stmt->execute([$name, $genre, $bio, $image_url, $featured, $id]);
                    $success = 'Artist updated successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to update artist. Please try again.';
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['artist_id'] ?? 0);
            
            if ($id > 0) {
                try {
                    // Check if artist has albums
                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM albums WHERE artist_id = ?");
                    $stmt->execute([$id]);
                    $albumCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    if ($albumCount > 0) {
                        $errors[] = "Cannot delete artist with {$albumCount} album(s). Delete albums first.";
                    } else {
                        $stmt = $conn->prepare("DELETE FROM artists WHERE id = ?");
                        $stmt->execute([$id]);
                        $success = 'Artist deleted successfully!';
                    }
                } catch (PDOException $e) {
                    $errors[] = 'Failed to delete artist. Please try again.';
                }
            }
        }
    }
}

// Get artists with search/filter
$search = sanitize($_GET['search'] ?? '');
$filter = sanitize($_GET['filter'] ?? '');

$sql = "SELECT a.*, 
        (SELECT COUNT(*) FROM albums WHERE artist_id = a.id) as album_count
        FROM artists a WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (a.name LIKE ? OR a.genre LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter === 'featured') {
    $sql .= " AND a.featured = 1";
}

$sql .= " ORDER BY a.name ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get artist for editing
$editArtist = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM artists WHERE id = ?");
    $stmt->execute([$editId]);
    $editArtist = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = 'Manage Artists';
include __DIR__ . '/../includes/header.php';
?>

        <section class="admin-section" aria-labelledby="artists-title">
            <div class="admin-header">
                <h1 id="artists-title">Manage Artists</h1>
                <p>Add, edit, and remove artists from the platform.</p>
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

            <!-- Add/Edit Artist Form -->
            <div class="admin-card">
                <h2><?php echo $editArtist ? 'Edit Artist' : 'Add New Artist'; ?></h2>
                <form method="POST" action="/admin/artists.php" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $editArtist ? 'edit' : 'add'; ?>">
                    <?php if ($editArtist): ?>
                        <input type="hidden" name="artist_id" value="<?php echo $editArtist['id']; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Artist Name *</label>
                            <input type="text" id="name" name="name" required
                                   value="<?php echo htmlspecialchars($editArtist['name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <input type="text" id="genre" name="genre" 
                                   placeholder="e.g., Pop, Rock, Hip-Hop"
                                   value="<?php echo htmlspecialchars($editArtist['genre'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio">Biography</label>
                        <textarea id="bio" name="bio" rows="4" 
                                  placeholder="Artist biography..."><?php echo htmlspecialchars($editArtist['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <input type="url" id="image_url" name="image_url" 
                               placeholder="https://example.com/image.jpg"
                               value="<?php echo htmlspecialchars($editArtist['image_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="featured" value="1"
                                   <?php echo ($editArtist['featured'] ?? 0) ? 'checked' : ''; ?>>
                            Featured Artist
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editArtist ? 'Update Artist' : 'Add Artist'; ?>
                        </button>
                        <?php if ($editArtist): ?>
                            <a href="/admin/artists.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Search/Filter -->
            <div class="admin-card">
                <form method="GET" action="/admin/artists.php" class="filter-form">
                    <div class="filter-row">
                        <input type="text" name="search" placeholder="Search artists..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <select name="filter">
                            <option value="">All Artists</option>
                            <option value="featured" <?php echo $filter === 'featured' ? 'selected' : ''; ?>>Featured Only</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if ($search || $filter): ?>
                            <a href="/admin/artists.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Artists Table -->
            <div class="admin-card">
                <h2>All Artists (<?php echo count($artists); ?>)</h2>
                
                <?php if (empty($artists)): ?>
                    <p class="no-data">No artists found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Artist</th>
                                    <th>Genre</th>
                                    <th>Followers</th>
                                    <th>Albums</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($artists as $artist): ?>
                                    <tr>
                                        <td>
                                            <div class="artist-cell">
                                                <?php if ($artist['image_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($artist['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($artist['name']); ?>"
                                                         class="artist-thumb">
                                                <?php else: ?>
                                                    <div class="artist-thumb placeholder">
                                                        <?php echo strtoupper(substr($artist['name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($artist['name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($artist['genre'] ?: '-'); ?></td>
                                        <td><?php echo number_format($artist['followers']); ?></td>
                                        <td><?php echo $artist['album_count']; ?></td>
                                        <td>
                                            <?php if ($artist['featured']): ?>
                                                <span class="badge badge-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/admin/artists.php?edit=<?php echo $artist['id']; ?>" 
                                                   class="btn btn-small btn-primary">Edit</a>
                                                <form method="POST" action="/admin/artists.php" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this artist?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="artist_id" value="<?php echo $artist['id']; ?>">
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
