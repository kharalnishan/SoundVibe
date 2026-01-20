<?php
/**
 * Privacy Policy Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Privacy Policy';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="privacy-title">
            <h1 id="privacy-title">Privacy Policy</h1>
            <p>Last updated: <?php echo date('F j, Y'); ?></p>
        </section>

        <!-- Privacy Content -->
        <section class="legal-content" aria-labelledby="privacy-heading">
            <div class="legal-container">
                <h2 id="privacy-heading">Your Privacy Matters</h2>
                <p class="intro-text">At SoundVibe, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, and safeguard your data.</p>
                
                <article class="legal-section">
                    <h3>1. Information We Collect</h3>
                    
                    <h4>1.1 Personal Information</h4>
                    <p>When you create an account, we collect:</p>
                    <ul>
                        <li>Full name</li>
                        <li>Email address</li>
                        <li>Password (stored securely using encryption)</li>
                        <li>Profile information you choose to provide</li>
                    </ul>

                    <h4>1.2 Usage Data</h4>
                    <p>We automatically collect information about how you use our service:</p>
                    <ul>
                        <li>Listening history and preferences</li>
                        <li>Playlists created and songs saved</li>
                        <li>Device information and IP address</li>
                        <li>Browser type and operating system</li>
                    </ul>

                    <h4>1.3 Cookies and Tracking</h4>
                    <p>We use cookies and similar technologies to:</p>
                    <ul>
                        <li>Keep you logged in</li>
                        <li>Remember your preferences</li>
                        <li>Analyze site traffic and usage patterns</li>
                        <li>Improve our services</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>2. How We Use Your Information</h3>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Provide and maintain our service</li>
                        <li>Personalize your music recommendations</li>
                        <li>Communicate with you about updates and features</li>
                        <li>Improve and develop new features</li>
                        <li>Ensure security and prevent fraud</li>
                        <li>Comply with legal obligations</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>3. Data Security</h3>
                    <p>We implement industry-standard security measures to protect your data:</p>
                    <ul>
                        <li><strong>Encryption:</strong> Passwords are hashed using bcrypt algorithm</li>
                        <li><strong>HTTPS:</strong> All data transmission is encrypted</li>
                        <li><strong>Access Control:</strong> Limited employee access to user data</li>
                        <li><strong>Regular Audits:</strong> Security assessments and updates</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>4. Data Sharing</h3>
                    <p>We do not sell your personal information. We may share data with:</p>
                    <ul>
                        <li><strong>Service Providers:</strong> Third parties that help us operate our service</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect rights</li>
                        <li><strong>Business Transfers:</strong> In connection with mergers or acquisitions</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>5. Your Rights</h3>
                    <p>You have the right to:</p>
                    <ul>
                        <li><strong>Access:</strong> Request a copy of your personal data</li>
                        <li><strong>Correction:</strong> Update inaccurate information</li>
                        <li><strong>Deletion:</strong> Request deletion of your account and data</li>
                        <li><strong>Portability:</strong> Receive your data in a portable format</li>
                        <li><strong>Opt-out:</strong> Unsubscribe from marketing communications</li>
                    </ul>
                </article>

                <article class="legal-section">
                    <h3>6. Data Retention</h3>
                    <p>We retain your personal data for as long as your account is active or as needed to provide services. If you delete your account, we will remove your personal data within 30 days, except where retention is required by law.</p>
                </article>

                <article class="legal-section">
                    <h3>7. Children's Privacy</h3>
                    <p>Our service is not intended for users under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe we have collected such information, please contact us immediately.</p>
                </article>

                <article class="legal-section">
                    <h3>8. International Data Transfers</h3>
                    <p>Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your data in accordance with applicable laws.</p>
                </article>

                <article class="legal-section">
                    <h3>9. Changes to This Policy</h3>
                    <p>We may update this Privacy Policy periodically. We will notify you of significant changes by email or through a notice on our service.</p>
                </article>

                <article class="legal-section">
                    <h3>10. Contact Us</h3>
                    <p>For questions or concerns about this Privacy Policy or your data, please contact:</p>
                    <p>
                        <strong>Data Protection Officer</strong><br>
                        Email: <a href="mailto:privacy@soundvibe.com">privacy@soundvibe.com</a><br>
                        Address: 123 Music Lane, Melbourne VIC 3000, Australia
                    </p>
                </article>

                <div class="privacy-notice">
                    <h4>Your Consent</h4>
                    <p>By using SoundVibe, you consent to the collection and use of your information as described in this Privacy Policy.</p>
                </div>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
