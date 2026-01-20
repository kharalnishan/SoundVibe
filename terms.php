<?php
/**
 * Terms and Conditions Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Terms & Conditions';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="terms-title">
            <h1 id="terms-title">Terms & Conditions</h1>
            <p>Last updated: <?php echo date('F j, Y'); ?></p>
        </section>

        <!-- Terms Content -->
        <section class="legal-content" aria-labelledby="terms-heading">
            <div class="legal-container">
                <h2 id="terms-heading">Terms of Service</h2>
                
                <article class="legal-section">
                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using SoundVibe ("the Service"), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our service.</p>
                </article>

                <article class="legal-section">
                    <h3>2. Description of Service</h3>
                    <p>SoundVibe is a music streaming platform that allows users to:</p>
                    <ul>
                        <li>Stream music from our catalog</li>
                        <li>Create and manage playlists</li>
                        <li>Follow artists and other users</li>
                        <li>Discover new music through recommendations</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>3. User Accounts</h3>
                    <p>To access certain features, you must create an account. You agree to:</p>
                    <ul>
                        <li>Provide accurate and complete information</li>
                        <li>Maintain the security of your account credentials</li>
                        <li>Notify us immediately of any unauthorized access</li>
                        <li>Accept responsibility for all activities under your account</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>4. Acceptable Use</h3>
                    <p>You agree not to:</p>
                    <ul>
                        <li>Violate any laws or regulations</li>
                        <li>Infringe upon intellectual property rights</li>
                        <li>Upload malicious content or malware</li>
                        <li>Attempt to gain unauthorized access to our systems</li>
                        <li>Use the service for commercial purposes without authorization</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>5. Intellectual Property</h3>
                    <p>All content on SoundVibe, including music, artwork, and software, is protected by copyright and other intellectual property laws. You may not reproduce, distribute, or create derivative works without explicit permission.</p>
                </article>

                <article class="legal-section">
                    <h3>6. Termination</h3>
                    <p>We reserve the right to suspend or terminate your account at any time for violations of these terms or for any other reason at our discretion.</p>
                </article>

                <article class="legal-section">
                    <h3>7. Limitation of Liability</h3>
                    <p>SoundVibe is provided "as is" without warranties of any kind. We are not liable for any damages arising from your use of the service.</p>
                </article>

                <article class="legal-section">
                    <h3>8. Changes to Terms</h3>
                    <p>We may update these terms from time to time. Continued use of the service after changes constitutes acceptance of the new terms.</p>
                </article>

                <article class="legal-section">
                    <h3>9. Contact Information</h3>
                    <p>For questions about these Terms, please contact us at:</p>
                    <p>Email: <a href="mailto:legal@soundvibe.com">legal@soundvibe.com</a></p>
                </article>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
