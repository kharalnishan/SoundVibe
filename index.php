<?php
/**
 * Home Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

// Fetch trending albums from database
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT a.*, ar.name as artist_name FROM albums a JOIN artists ar ON a.artist_id = ar.id ORDER BY a.created_at DESC LIMIT 4");
$trendingAlbums = $stmt->fetchAll();

// Fetch featured playlists
$stmt = $pdo->query("SELECT * FROM playlists WHERE is_featured = 1 LIMIT 4");
$featuredPlaylists = $stmt->fetchAll();

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

        <!-- Hero Section -->
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-content">
                <h2 id="hero-title">Discover Your Sound</h2>
                <p>Stream millions of songs. Find your perfect playlist. Create your vibe.</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="/auth/register.php" class="cta-button" aria-label="Start exploring music">Get Started Free</a>
                <?php else: ?>
                    <a href="/playlist.php" class="cta-button" aria-label="Browse playlists">Explore Playlists</a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" aria-labelledby="features-heading">
            <h2 id="features-heading">Why SoundVibe?</h2>
            <div class="features-grid">
                <article class="feature-card">
                    <div class="feature-icon">♪</div>
                    <h3>Millions of Tracks</h3>
                    <p>Access an endless library of music from every genre imaginable.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🎧</div>
                    <h3>Personalized Playlists</h3>
                    <p>Let our algorithms craft the perfect playlist for your mood.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🌐</div>
                    <h3>Listen Anywhere</h3>
                    <p>Stream on any device, anytime, anywhere in the world.</p>
                </article>
            </div>
        </section>

        <!-- Trending Section -->
        <section class="trending" aria-labelledby="trending-heading">
            <h2 id="trending-heading">Trending Now</h2>
            <div class="trending-grid">
                <?php if (count($trendingAlbums) > 0): ?>
                    <?php foreach ($trendingAlbums as $album): ?>
                        <div class="trending-card">
                            <img src="<?php echo htmlspecialchars($album['cover_image']); ?>" alt="<?php echo htmlspecialchars($album['title']); ?> album cover">
                            <h3><?php echo htmlspecialchars($album['title']); ?></h3>
                            <p><?php echo htmlspecialchars($album['artist_name']); ?></p>
                            <button class="play-btn" aria-label="Play <?php echo htmlspecialchars($album['title']); ?>" aria-pressed="false" data-audio="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3">▶</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback static content -->
                    <div class="trending-card">
                        <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=400&auto=format&fit=crop" alt="Neon Nights album cover">
                        <h3>Neon Nights</h3>
                        <p>Synthwave Collective</p>
                        <button class="play-btn" aria-label="Play Neon Nights" aria-pressed="false" data-audio="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3">▶</button>
                    </div>
                    <div class="trending-card">
                        <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=400&auto=format&fit=crop" alt="Urban Pulse album cover">
                        <h3>Urban Pulse</h3>
                        <p>Hip Hop Masters</p>
                        <button class="play-btn" aria-label="Play Urban Pulse" aria-pressed="false" data-audio="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3">▶</button>
                    </div>
                    <div class="trending-card">
                        <img src="https://images.unsplash.com/photo-1503481766315-7a586b20f66d?q=80&w=400&auto=format&fit=crop" alt="Chilled Vibes album cover">
                        <h3>Chilled Vibes</h3>
                        <p>Lofi Dreams</p>
                        <button class="play-btn" aria-label="Play Chilled Vibes" aria-pressed="false" data-audio="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3">▶</button>
                    </div>
                    <div class="trending-card">
                        <img src="https://images.unsplash.com/photo-1498038432885-c6f3f1b912ee?q=80&w=400&auto=format&fit=crop" alt="Rock Legends album cover">
                        <h3>Rock Legends</h3>
                        <p>Classic Rock Band</p>
                        <button class="play-btn" aria-label="Play Rock Legends" aria-pressed="false" data-audio="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3">▶</button>
                    </div>
                <?php endif; ?>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
