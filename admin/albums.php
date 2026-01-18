<?php
/**
 * Admin Albums Management
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

// Require admin access
requireAdmin();

$user = getCurrentUser();
$errors = [];
$success = '';

$conn = getDBConnection();

// Get all artists for dropdown
$stmt = $conn->query("SELECT id, name FROM artists ORDER BY name ASC");
$allArtists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            $title = sanitize($_POST['title'] ?? '');
            $artist_id = intval($_POST['artist_id'] ?? 0);
            $release_year = intval($_POST['release_year'] ?? 0);
            $genre = sanitize($_POST['genre'] ?? '');
            $cover_image = sanitize($_POST['cover_image'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            
            if (empty($title)) {
                $errors[] = 'Album title is required.';
            }
            if ($artist_id <= 0) {
                $errors[] = 'Please select an artist.';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO albums (title, artist_id, release_year, genre, cover_image, description) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $artist_id, $release_year ?: null, $genre, $cover_image, $description]);
                    $success = 'Album added successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to add album. Please try again.';
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['album_id'] ?? 0);
            $title = sanitize($_POST['title'] ?? '');
            $artist_id = intval($_POST['artist_id'] ?? 0);
            $release_year = intval($_POST['release_year'] ?? 0);
            $genre = sanitize($_POST['genre'] ?? '');
            $cover_image = sanitize($_POST['cover_image'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            
            if (empty($title)) {
                $errors[] = 'Album title is required.';
            }
            if ($artist_id <= 0) {
                $errors[] = 'Please select an artist.';
            }
            
            if (empty($errors) && $id > 0) {
                try {
                    $stmt = $conn->prepare("UPDATE albums SET title = ?, artist_id = ?, release_year = ?, genre = ?, cover_image = ?, description = ? WHERE id = ?");
                    $stmt->execute([$title, $artist_id, $release_year ?: null, $genre, $cover_image, $description, $id]);
                    $success = 'Album updated successfully!';
                } catch (PDOException $e) {
                    $errors[] = 'Failed to update album. Please try again.';
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['album_id'] ?? 0);
            
            if ($id > 0) {
                try {
                    // Check if album has tracks
                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tracks WHERE album_id = ?");
                    $stmt->execute([$id]);
                    $trackCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    if ($trackCount > 0) {
                        $errors[] = "Cannot delete album with {$trackCount} track(s). Delete tracks first.";
                    } else {
                        $stmt = $conn->prepare("DELETE FROM albums WHERE id = ?");
                        $stmt->execute([$id]);
                        $success = 'Album deleted successfully!';
                    }
                } catch (PDOException $e) {
                    $errors[] = 'Failed to delete album. Please try again.';
                }
            }
        }
    }
}

// Get albums with search/filter
$search = sanitize($_GET['search'] ?? '');
$filterArtist = intval($_GET['artist'] ?? 0);

$sql = "SELECT a.*, ar.name as artist_name,
        (SELECT COUNT(*) FROM tracks WHERE album_id = a.id) as track_count
        FROM albums a
        LEFT JOIN artists ar ON a.artist_id = ar.id
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (a.title LIKE ? OR a.genre LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filterArtist > 0) {
    $sql .= " AND a.artist_id = ?";
    $params[] = $filterArtist;
}

$sql .= " ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get album for editing
$editAlbum = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->execute([$editId]);
    $editAlbum = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = 'Manage Albums';
include __DIR__ . '/../includes/header.php';
?>

        <section class="admin-section" aria-labelledby="albums-title">
            <div class="admin-header">
                <h1 id="albums-title">Manage Albums</h1>
                <p>Add, edit, and manage albums in the catalog.</p>
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

            <!-- Add/Edit Album Form -->
            <div class="admin-card">
                <h2><?php echo $editAlbum ? 'Edit Album' : 'Add New Album'; ?></h2>
                <form method="POST" action="/admin/albums.php" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $editAlbum ? 'edit' : 'add'; ?>">
                    <?php if ($editAlbum): ?>
                        <input type="hidden" name="album_id" value="<?php echo $editAlbum['id']; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Album Title *</label>
                            <input type="text" id="title" name="title" required
                                   value="<?php echo htmlspecialchars($editAlbum['title'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="artist_id">Artist *</label>
                            <select id="artist_id" name="artist_id" required>
                                <option value="">Select Artist</option>
                                <?php foreach ($allArtists as $artist): ?>
                                    <option value="<?php echo $artist['id']; ?>"
                                            <?php echo ($editAlbum['artist_id'] ?? 0) == $artist['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($artist['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="release_year">Release Year</label>
                            <input type="number" id="release_year" name="release_year" 
                                   min="1900" max="<?php echo date('Y'); ?>"
                                   placeholder="e.g., 2024"
                                   value="<?php echo htmlspecialchars($editAlbum['release_year'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <input type="text" id="genre" name="genre" 
                                   placeholder="e.g., Pop, Rock, Hip-Hop"
                                   value="<?php echo htmlspecialchars($editAlbum['genre'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cover_image">Cover Image URL</label>
                        <input type="url" id="cover_image" name="cover_image" 
                               placeholder="https://example.com/cover.jpg"
                               value="<?php echo htmlspecialchars($editAlbum['cover_image'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  placeholder="Album description..."><?php echo htmlspecialchars($editAlbum['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editAlbum ? 'Update Album' : 'Add Album'; ?>
                        </button>
                        <?php if ($editAlbum): ?>
                            <a href="/admin/albums.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Search/Filter -->
            <div class="admin-card">
                <form method="GET" action="/admin/albums.php" class="filter-form">
                    <div class="filter-row">
                        <input type="text" name="search" placeholder="Search albums..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <select name="artist">
                            <option value="">All Artists</option>
                            <?php foreach ($allArtists as $artist): ?>
                                <option value="<?php echo $artist['id']; ?>"
                                        <?php echo $filterArtist == $artist['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($artist['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if ($search || $filterArtist): ?>
                            <a href="/admin/albums.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Albums Table -->
            <div class="admin-card">
                <h2>All Albums (<?php echo count($albums); ?>)</h2>
                
                <?php if (empty($albums)): ?>
                    <p class="no-data">No albums found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Album</th>
                                    <th>Artist</th>
                                    <th>Year</th>
                                    <th>Genre</th>
                                    <th>Tracks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($albums as $album): ?>
                                    <tr>
                                        <td>
                                            <div class="album-cell">
                                                <?php if ($album['cover_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($album['cover_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($album['title']); ?>"
                                                         class="album-thumb">
                                                <?php else: ?>
                                                    <div class="album-thumb placeholder">💿</div>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($album['title']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($album['artist_name'] ?? 'Unknown'); ?></td>
                                        <td><?php echo $album['release_year'] ?: '-'; ?></td>
                                        <td><?php echo htmlspecialchars($album['genre'] ?: '-'); ?></td>
                                        <td><?php echo $album['track_count']; ?> tracks</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/admin/albums.php?edit=<?php echo $album['id']; ?>" 
                                                   class="btn btn-small btn-primary">Edit</a>
                                                <form method="POST" action="/admin/albums.php" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this album?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="album_id" value="<?php echo $album['id']; ?>">
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
