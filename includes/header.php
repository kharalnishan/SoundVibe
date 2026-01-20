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
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - SoundVibe' : 'SoundVibe - Music Streaming'; ?>">
    <meta property="og:description" content="Stream millions of songs. Find your perfect playlist. Create your vibe.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    
    <!-- SEO -->
    <link rel="canonical" href="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - SoundVibe' : 'SoundVibe - Music Streaming Web App'; ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
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
