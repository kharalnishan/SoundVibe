<?php
/**
 * Create Playlist Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

// Require login
requireLogin();

$user = getCurrentUser();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $cover = sanitize($_POST['cover_image'] ?? '');
        $is_public = isset($_POST['is_public']) ? 1 : 0;

        if (empty($name)) {
            $errors[] = 'Playlist name is required.';
        }

        if (empty($errors)) {
            try {
                $conn = getDBConnection();
                $stmt = $conn->prepare("INSERT INTO playlists (name, description, cover_image, user_id, is_public, is_featured, track_count, total_duration, created_at) VALUES (?, ?, ?, ?, ?, 0, 0, 0, CURRENT_TIMESTAMP)");
                $stmt->execute([$name, $description, $cover, $user['id'], $is_public]);
                $playlistId = $conn->lastInsertId();

                // Redirect to playlist view or list
                header('Location: /playlist.php?id=' . $playlistId . '&created=1');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Failed to create playlist. Please try again.';
            }
        }
    }
}

$pageTitle = 'Create Playlist';
include __DIR__ . '/../includes/header.php';
?>

        <section class="page-hero" aria-labelledby="create-playlist-title">
            <h1 id="create-playlist-title">Create Playlist</h1>
            <p>Create a new playlist to collect your favourite tracks.</p>
        </section>

        <section class="content-section">
            <div class="container">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <h2>Create a New Playlist</h2>
                    <form method="POST" action="/playlist/create.php" class="form">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                        <div class="form-group">
                            <label for="name">Playlist Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="cover_image">Cover Image URL</label>
                            <input type="url" id="cover_image" name="cover_image" value="<?php echo htmlspecialchars($_POST['cover_image'] ?? ''); ?>">
                        </div>

                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="is_public" value="1" <?php echo isset($_POST['is_public']) ? 'checked' : 'checked'; ?>> Public (visible to others)
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Create Playlist</button>
                            <a href="/playlist.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
