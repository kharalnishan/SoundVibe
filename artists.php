<?php
/**
 * Artists Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$pdo = getDBConnection();

// Fetch artists from database
$stmt = $pdo->query("SELECT * FROM artists ORDER BY is_featured DESC, followers DESC");
$artists = $stmt->fetchAll();

$pageTitle = 'Artists';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="artists-title">
            <h1 id="artists-title">Discover Artists</h1>
            <p>Explore talented musicians across all genres</p>
        </section>

        <!-- Featured Artists -->
        <section class="artists" aria-labelledby="artists-heading">
            <h2 id="artists-heading">Featured Artists</h2>
            <div class="artists-grid">
                <?php foreach ($artists as $artist): ?>
                    <article class="artist-card" data-genre="<?php echo htmlspecialchars(strtolower($artist['genre'])); ?>">
                        <div class="artist-image">
                            <img src="<?php echo htmlspecialchars($artist['image_url']); ?>" alt="<?php echo htmlspecialchars($artist['name']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($artist['name']); ?></h3>
                        <p class="artist-genre"><?php echo htmlspecialchars($artist['genre']); ?></p>
                        <p class="artist-followers"><?php echo number_format($artist['followers']); ?> followers</p>
                        <?php if (isLoggedIn()): ?>
                            <button class="follow-btn" aria-label="Follow <?php echo htmlspecialchars($artist['name']); ?>">Follow</button>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Genre Filter -->
        <section class="genres" aria-labelledby="filter-heading">
            <h2 id="filter-heading">Filter by Genre</h2>
            <div class="genre-grid">
                <button class="genre-btn active" data-target="artist" data-genre="all">All</button>
                <button class="genre-btn" data-target="artist" data-genre="electronic">Electronic</button>
                <button class="genre-btn" data-target="artist" data-genre="hip hop">Hip Hop</button>
                <button class="genre-btn" data-target="artist" data-genre="jazz">Jazz</button>
                <button class="genre-btn" data-target="artist" data-genre="rock">Rock</button>
                <button class="genre-btn" data-target="artist" data-genre="indie">Indie</button>
                <button class="genre-btn" data-target="artist" data-genre="classical">Classical</button>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
