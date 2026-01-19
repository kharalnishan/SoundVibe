<?php
/**
 * 404 Error Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

http_response_code(404);

$pageTitle = 'Page Not Found';
include __DIR__ . '/includes/header.php';
?>

        <!-- 404 Error Section -->
        <section class="error-section" aria-labelledby="error-title">
            <div class="error-container">
                <div class="error-icon">🎵</div>
                <h1 id="error-title">404</h1>
                <h2>Oops! This track doesn't exist</h2>
                <p>The page you're looking for seems to have skipped to another playlist. Let's get you back on track!</p>
                
                <div class="error-actions">
                    <a href="/index.php" class="btn btn-primary">Go to Homepage</a>
                    <a href="/playlist.php" class="btn btn-secondary">Browse Playlists</a>
                </div>

                <div class="error-suggestions">
                    <h3>You might be looking for:</h3>
                    <ul>
                        <li><a href="/artists.php">Discover Artists</a></li>
                        <li><a href="/media.php">Browse Gallery</a></li>
                        <li><a href="/contact.php">Contact Support</a></li>
                    </ul>
                </div>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
