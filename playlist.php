<?php
/**
 * Playlists Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$pdo = getDBConnection();

// Fetch featured playlists from database
$stmt = $pdo->query("SELECT * FROM playlists WHERE is_featured = 1 ORDER BY created_at DESC");
$playlists = $stmt->fetchAll();

// Helper function to format duration
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
}

$pageTitle = 'Playlists';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="playlist-title">
            <h1 id="playlist-title">Explore Playlists</h1>
            <p>Curated collections for every mood and moment</p>
        </section>

        <!-- Featured Playlists -->
        <section class="playlists" aria-labelledby="featured-heading">
            <h2 id="featured-heading">Featured Playlists</h2>
            <div class="playlist-grid">
                <?php if (!empty($playlists)): ?>
                    <?php foreach ($playlists as $playlist): ?>
                        <article class="playlist-card" data-genre="<?php echo htmlspecialchars(strtolower($playlist['genre'])); ?>">
                            <div class="playlist-cover">
                                <img src="<?php echo htmlspecialchars($playlist['cover_image']); ?>" alt="<?php echo htmlspecialchars($playlist['name']); ?> playlist cover">
                            </div>
                            <h3><?php echo htmlspecialchars($playlist['name']); ?></h3>
                            <p class="playlist-desc"><?php echo htmlspecialchars($playlist['description']); ?></p>
                            <p class="playlist-meta"><?php echo $playlist['track_count']; ?> tracks • <?php echo formatDuration($playlist['total_duration']); ?></p>
                            <button class="play-btn" aria-label="Play <?php echo htmlspecialchars($playlist['name']); ?> playlist" aria-pressed="false" data-audio="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3">▶</button>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback static content -->
                    <article class="playlist-card" data-genre="electronic">
                        <div class="playlist-cover">
                            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=600&auto=format&fit=crop" alt="Workout Energy playlist cover">
                        </div>
                        <h3>Workout Energy</h3>
                        <p class="playlist-desc">High-energy tracks to power your fitness routine</p>
                        <p class="playlist-meta">42 tracks • 2h 15m</p>
                        <button class="play-btn" aria-label="Play Workout Energy playlist" aria-pressed="false">▶</button>
                    </article>
                <?php endif; ?>
            </div>
        </section>

        <!-- Genre Section -->
        <section class="genres" aria-labelledby="genres-heading">
            <h2 id="genres-heading">Browse by Genre</h2>
            <div class="genre-grid">
                <button class="genre-btn active" data-target="playlist" data-genre="all">All</button>
                <button class="genre-btn" data-target="playlist" data-genre="pop">Pop</button>
                <button class="genre-btn" data-target="playlist" data-genre="hip hop">Hip Hop</button>
                <button class="genre-btn" data-target="playlist" data-genre="rock">Rock</button>
                <button class="genre-btn" data-target="playlist" data-genre="electronic">Electronic</button>
                <button class="genre-btn" data-target="playlist" data-genre="jazz">Jazz</button>
                <button class="genre-btn" data-target="playlist" data-genre="classical">Classical</button>
                <button class="genre-btn" data-target="playlist" data-genre="indie">Indie</button>
                <button class="genre-btn" data-target="playlist" data-genre="ambient">Ambient</button>
            </div>
        </section>

        <?php if (isLoggedIn()): ?>
        <!-- Create Playlist Section (Members only) -->
        <section class="create-playlist" aria-labelledby="create-heading">
            <h2 id="create-heading">Create Your Playlist</h2>
            <p>Make your own collection of favorite tracks</p>
            <a href="/playlist/create.php" class="cta-button">Create Playlist</a>
        </section>
        <?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
