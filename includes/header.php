<?php
/**
 * Header Include
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/auth.php';

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SoundVibe - Stream millions of songs, discover new artists, and create your perfect playlists. Your music, your vibe.">
    <meta name="keywords" content="music streaming, playlists, artists, albums, songs, SoundVibe">
    <meta name="author" content="SoundVibe Team">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0EA5E9">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - SoundVibe' : 'SoundVibe - Music Streaming'; ?>">
    <meta property="og:description" content="Stream millions of songs. Find your perfect playlist. Create your vibe.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:site_name" content="SoundVibe">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - SoundVibe' : 'SoundVibe - Music Streaming'; ?>">
    <meta name="twitter:description" content="Stream millions of songs. Find your perfect playlist. Create your vibe.">
    
    <!-- SEO -->
    <link rel="canonical" href="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "SoundVibe",
        "description": "Stream millions of songs, discover new artists, and create your perfect playlists",
        "url": "<?php echo SITE_URL; ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo SITE_URL; ?>/search.php?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    <?php if ($currentPage === 'index'): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "SoundVibe",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/images/logo.png",
        "sameAs": [
            "https://facebook.com/soundvibe",
            "https://twitter.com/soundvibe",
            "https://instagram.com/soundvibe"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer support",
            "email": "support@soundvibe.com"
        }
    }
    </script>
    <?php endif; ?>
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - SoundVibe' : 'SoundVibe - Music Streaming Web App'; ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <script>
        // Expose CSRF token for client-side requests
        window.SV_CSRF_TOKEN = '<?php echo generateCSRFToken(); ?>';
    </script>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header role="banner">
        <div class="header-content">
            <div class="logo">
                <a href="/index.php" style="text-decoration: none; color: inherit;">
                    <h1>♪ SoundVibe</h1>
                    <p class="tagline">Your Music, Your Vibe</p>
                </a>
            </div>
            <nav role="navigation" aria-label="Main Navigation">
                <button class="hamburger" id="hamburger" aria-label="Toggle navigation menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="/index.php" <?php echo $currentPage === 'index' ? 'class="active" aria-current="page"' : ''; ?>>Home</a></li>
                    <li><a href="/about.php" <?php echo $currentPage === 'about' ? 'class="active" aria-current="page"' : ''; ?>>About</a></li>
                    <li><a href="/playlist.php" <?php echo $currentPage === 'playlist' ? 'class="active" aria-current="page"' : ''; ?>>Playlists</a></li>
                    <li><a href="/artists.php" <?php echo $currentPage === 'artists' ? 'class="active" aria-current="page"' : ''; ?>>Artists</a></li>
                    <li><a href="/media.php" <?php echo $currentPage === 'media' ? 'class="active" aria-current="page"' : ''; ?>>Gallery</a></li>
                    <li><a href="/search.php" <?php echo $currentPage === 'search' ? 'class="active" aria-current="page"' : ''; ?>>🔍</a></li>
                    <li><a href="/contact.php" <?php echo $currentPage === 'contact' ? 'class="active" aria-current="page"' : ''; ?>>Contact</a></li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="/admin/dashboard.php" <?php echo strpos($currentPage, 'admin') !== false ? 'class="active"' : ''; ?>>Admin</a></li>
                        <?php endif; ?>
                        <li class="user-menu">
                            <a href="/profile.php" class="user-link">
                                <span class="user-icon">👤</span>
                                <?php echo htmlspecialchars($currentUser['first_name']); ?>
                            </a>
                        </li>
                        <li><a href="/auth/logout.php" class="logout-link">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/auth/login.php" <?php echo $currentPage === 'login' ? 'class="active"' : ''; ?>>Login</a></li>
                        <li><a href="/auth/register.php" class="cta-nav-btn" <?php echo $currentPage === 'register' ? 'class="active"' : ''; ?>>Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main role="main" id="main-content">
