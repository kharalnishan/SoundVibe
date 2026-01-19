<?php
/**
 * Search Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$searchQuery = sanitize($_GET['q'] ?? '');
$results = [
    'artists' => [],
    'albums' => [],
    'playlists' => []
];

if (!empty($searchQuery) && strlen($searchQuery) >= 2) {
    $conn = getDBConnection();
    $searchTerm = "%{$searchQuery}%";
    
    // Search artists
    $stmt = $conn->prepare("SELECT id, name, genre, image_url, followers FROM artists WHERE name LIKE ? OR genre LIKE ? ORDER BY featured DESC, followers DESC LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm]);
    $results['artists'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search albums
    $stmt = $conn->prepare("SELECT a.id, a.title, a.cover_image, a.genre, a.release_year, ar.name as artist_name 
                           FROM albums a 
                           LEFT JOIN artists ar ON a.artist_id = ar.id 
                           WHERE a.title LIKE ? OR a.genre LIKE ? 
                           ORDER BY a.release_year DESC LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm]);
    $results['albums'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search playlists
    $stmt = $conn->prepare("SELECT id, name, description, cover_image FROM playlists WHERE is_public = 1 AND (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm]);
    $results['playlists'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalResults = count($results['artists']) + count($results['albums']) + count($results['playlists']);

$pageTitle = $searchQuery ? "Search: {$searchQuery}" : 'Search';
include __DIR__ . '/includes/header.php';
?>

        <!-- Search Section -->
        <section class="search-section" aria-labelledby="search-title">
            <div class="search-container">
                <h1 id="search-title">Search SoundVibe</h1>
                
                <!-- Search Form -->
                <form action="/search.php" method="GET" class="search-form" role="search">
                    <div class="search-input-wrapper">
                        <input type="search" 
                               name="q" 
                               id="searchInput"
                               value="<?php echo htmlspecialchars($searchQuery); ?>" 
                               placeholder="Search for artists, albums, playlists..."
                               aria-label="Search"
                               minlength="2"
                               required>
                        <button type="submit" class="btn btn-primary" aria-label="Search">
                            🔍 Search
                        </button>
                    </div>
                </form>

                <?php if (!empty($searchQuery)): ?>
                    <div class="search-results" aria-live="polite">
                        <p class="results-summary">
                            Found <strong><?php echo $totalResults; ?></strong> result<?php echo $totalResults !== 1 ? 's' : ''; ?> for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                        </p>

                        <?php if ($totalResults === 0): ?>
                            <div class="no-results">
                                <p>No results found. Try a different search term.</p>
                                <div class="search-suggestions">
                                    <h3>Suggestions:</h3>
                                    <ul>
                                        <li>Check your spelling</li>
                                        <li>Try more general keywords</li>
                                        <li>Try different keywords</li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            
                            <?php if (!empty($results['artists'])): ?>
                                <div class="results-section">
                                    <h2>Artists (<?php echo count($results['artists']); ?>)</h2>
                                    <div class="results-grid">
                                        <?php foreach ($results['artists'] as $artist): ?>
                                            <div class="result-card artist-card">
                                                <?php if ($artist['image_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($artist['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($artist['name']); ?>"
                                                         class="result-image">
                                                <?php else: ?>
                                                    <div class="result-image placeholder">🎤</div>
                                                <?php endif; ?>
                                                <div class="result-info">
                                                    <h3><?php echo htmlspecialchars($artist['name']); ?></h3>
                                                    <p><?php echo htmlspecialchars($artist['genre'] ?: 'Artist'); ?></p>
                                                    <span class="followers"><?php echo number_format($artist['followers']); ?> followers</span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($results['albums'])): ?>
                                <div class="results-section">
                                    <h2>Albums (<?php echo count($results['albums']); ?>)</h2>
                                    <div class="results-grid">
                                        <?php foreach ($results['albums'] as $album): ?>
                                            <div class="result-card album-card">
                                                <?php if ($album['cover_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($album['cover_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($album['title']); ?>"
                                                         class="result-image">
                                                <?php else: ?>
                                                    <div class="result-image placeholder">💿</div>
                                                <?php endif; ?>
                                                <div class="result-info">
                                                    <h3><?php echo htmlspecialchars($album['title']); ?></h3>
                                                    <p><?php echo htmlspecialchars($album['artist_name'] ?? 'Unknown Artist'); ?></p>
                                                    <?php if ($album['release_year']): ?>
                                                        <span class="year"><?php echo $album['release_year']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($results['playlists'])): ?>
                                <div class="results-section">
                                    <h2>Playlists (<?php echo count($results['playlists']); ?>)</h2>
                                    <div class="results-grid">
                                        <?php foreach ($results['playlists'] as $playlist): ?>
                                            <div class="result-card playlist-card">
                                                <?php if ($playlist['cover_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($playlist['cover_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($playlist['name']); ?>"
                                                         class="result-image">
                                                <?php else: ?>
                                                    <div class="result-image placeholder">🎵</div>
                                                <?php endif; ?>
                                                <div class="result-info">
                                                    <h3><?php echo htmlspecialchars($playlist['name']); ?></h3>
                                                    <p><?php echo htmlspecialchars(substr($playlist['description'] ?? '', 0, 60)); ?>...</p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="search-prompt">
                        <p>Enter a search term to find artists, albums, and playlists.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
