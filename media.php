<?php
/**
 * Media/Gallery Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$pdo = getDBConnection();

// Fetch albums for gallery
$stmt = $pdo->query("SELECT a.*, ar.name as artist_name FROM albums a JOIN artists ar ON a.artist_id = ar.id ORDER BY a.created_at DESC LIMIT 9");
$albums = $stmt->fetchAll();

$pageTitle = 'Gallery';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="media-title">
            <h1 id="media-title">Media Gallery</h1>
            <p>Explore album artwork and concert moments</p>
        </section>

        <!-- Interactive Gallery -->
        <section class="gallery" aria-labelledby="album-gallery-heading">
            <h2 id="album-gallery-heading">Album Covers Gallery</h2>
            <p class="gallery-instruction">Click on any image to view larger</p>
            
            <div class="gallery-grid" id="galleryGrid">
                <?php if (!empty($albums)): ?>
                    <?php foreach ($albums as $album): ?>
                        <article class="gallery-item">
                            <img src="<?php echo htmlspecialchars($album['cover_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($album['title']); ?> by <?php echo htmlspecialchars($album['artist_name']); ?>" 
                                 class="gallery-thumbnail"
                                 tabindex="0"
                                 role="button"
                                 aria-label="Click to enlarge <?php echo htmlspecialchars($album['title']); ?> album cover"
                                 data-description="<?php echo htmlspecialchars($album['title']); ?> by <?php echo htmlspecialchars($album['artist_name']); ?> - <?php echo htmlspecialchars($album['description']); ?>">
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback static content -->
                    <article class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=500&auto=format&fit=crop" 
                             alt="Album cover - Neon Dreams" 
                             class="gallery-thumbnail"
                             tabindex="0"
                             role="button"
                             aria-label="Click to enlarge Neon Dreams album cover"
                             data-description="Neon Dreams - A vibrant synthwave journey through retro-futuristic soundscapes.">
                    </article>
                    <article class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=500&auto=format&fit=crop" 
                             alt="Album cover - Electric Waves" 
                             class="gallery-thumbnail"
                             tabindex="0"
                             role="button"
                             aria-label="Click to enlarge Electric Waves album cover"
                             data-description="Electric Waves - Raw guitar riffs and thunderous drums.">
                    </article>
                    <article class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1486092642310-0c4ea0fa7bf4?q=80&w=500&auto=format&fit=crop" 
                             alt="Album cover - Cosmic Journey" 
                             class="gallery-thumbnail"
                             tabindex="0"
                             role="button"
                             aria-label="Click to enlarge Cosmic Journey album cover"
                             data-description="Cosmic Journey - An interstellar voyage through sound.">
                    </article>
                <?php endif; ?>
            </div>
        </section>

        <!-- Modal for enlarged images -->
        <div id="imageModal" class="modal" aria-label="Enlarged image viewer" role="dialog" aria-modal="true" hidden>
            <div class="modal-content">
                <button class="close-btn" id="closeBtn" aria-label="Close image viewer">×</button>
                <img id="modalImage" src="" alt="Enlarged album cover">
                <p id="modalDescription" class="modal-description"></p>
                <div class="modal-nav">
                    <button id="prevBtn" aria-label="Previous image">❮</button>
                    <button id="nextBtn" aria-label="Next image">❯</button>
                </div>
            </div>
        </div>

        <!-- Concert Photos Section -->
        <section class="artist-photos" aria-labelledby="concert-heading">
            <h2 id="concert-heading">Concert Highlights</h2>
            <div class="photo-grid">
                <img src="https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?q=80&w=600&auto=format&fit=crop" 
                     alt="Live concert performance with crowd" loading="lazy">
                <img src="https://images.unsplash.com/photo-1464375117522-1311d5b7d0cf?q=80&w=600&auto=format&fit=crop" 
                     alt="Stage lights at music festival" loading="lazy">
                <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=600&auto=format&fit=crop" 
                     alt="Festival crowd at sunset" loading="lazy">
                <img src="https://images.unsplash.com/photo-1518655048521-f98190eb05d2?q=80&w=600&auto=format&fit=crop" 
                     alt="DJ performing live set" loading="lazy">
                <img src="https://images.unsplash.com/photo-1506157786151-b8491531f063?q=80&w=600&auto=format&fit=crop" 
                     alt="Concert venue atmosphere" loading="lazy">
                <img src="https://images.unsplash.com/photo-1513883049090-d0b7439799ca?q=80&w=600&auto=format&fit=crop" 
                     alt="Band performing on stage" loading="lazy">
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
