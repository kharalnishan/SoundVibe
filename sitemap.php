<?php
/**
 * Dynamic XML Sitemap
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = SITE_URL;

// Static pages
$staticPages = [
    ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['url' => '/about.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['url' => '/playlist.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/artists.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/media.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/contact.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['url' => '/terms.php', 'priority' => '0.5', 'changefreq' => 'yearly'],
    ['url' => '/privacy.php', 'priority' => '0.5', 'changefreq' => 'yearly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page): ?>
    <url>
        <loc><?php echo htmlspecialchars($baseUrl . $page['url']); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq><?php echo $page['changefreq']; ?></changefreq>
        <priority><?php echo $page['priority']; ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
