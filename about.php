<?php
/**
 * About Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'About Us';
include __DIR__ . '/includes/header.php';
?>

        <!-- Hero Section -->
        <section class="page-hero" aria-labelledby="page-title">
            <h1 id="page-title">About SoundVibe</h1>
            <p>Your Gateway to Unlimited Music</p>
        </section>

        <!-- Mission Section -->
        <section class="about-section" aria-labelledby="mission-heading">
            <h2 id="mission-heading">Our Mission</h2>
            <p>At SoundVibe, we believe music has the power to transform lives. Our mission is to make world-class music streaming accessible to everyone, breaking down barriers between fans and their favorite artists. We're committed to creating a platform where music lovers can discover, share, and enjoy the songs that define their lives.</p>
        </section>

        <!-- Story Section -->
        <section class="about-section" aria-labelledby="story-heading">
            <h2 id="story-heading">Our Story</h2>
            <p>Founded in 2024, SoundVibe emerged from a simple idea: music should be universal. Our team of passionate music enthusiasts and tech innovators came together to create a platform that celebrates all genres, all artists, and all listeners. What started as a small project has grown into a vibrant community of millions of music lovers worldwide.</p>
        </section>

        <!-- Values Section -->
        <section class="about-section" aria-labelledby="values-heading">
            <h2 id="values-heading">Our Values</h2>
            <div class="values-grid">
                <article class="value-card">
                    <h3>🎵 Passion for Music</h3>
                    <p>We live and breathe music. Every decision we make is driven by our love for the art form.</p>
                </article>
                <article class="value-card">
                    <h3>🤝 Community First</h3>
                    <p>Our listeners and artists are at the heart of everything we do. We listen, learn, and improve.</p>
                </article>
                <article class="value-card">
                    <h3>🌍 Global Reach</h3>
                    <p>We celebrate music from every corner of the world, bridging cultures through sound.</p>
                </article>
                <article class="value-card">
                    <h3>🔒 Trust & Security</h3>
                    <p>Your data and privacy are sacred. We maintain the highest standards of security.</p>
                </article>
            </div>
        </section>

        <!-- Team Section -->
        <section class="about-section" aria-labelledby="team-heading">
            <h2 id="team-heading">Our Team</h2>
            <p>SoundVibe is powered by a diverse team of music lovers, software engineers, designers, and marketing experts. Together, we work tirelessly to deliver the best music streaming experience. Our team spans across continents, bringing unique perspectives and expertise to everything we create.</p>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
