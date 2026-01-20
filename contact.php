<?php
/**
 * Contact Page
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/includes/auth.php';

$success = false;
$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'category' => '',
    'message' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Sanitize inputs
        $formData['name'] = sanitize($_POST['fullName'] ?? '');
        $formData['email'] = sanitize($_POST['email'] ?? '');
        $formData['subject'] = sanitize($_POST['subject'] ?? '');
        $formData['category'] = sanitize($_POST['category'] ?? '');
        $formData['message'] = sanitize($_POST['message'] ?? '');
        
        // Validate name
        if (empty($formData['name'])) {
            $errors[] = 'Full name is required.';
        } elseif (strlen($formData['name']) < 2) {
            $errors[] = 'Name must be at least 2 characters.';
        }
        
        // Validate email
        if (empty($formData['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($formData['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        // Validate subject
        if (empty($formData['subject'])) {
            $errors[] = 'Subject is required.';
        } elseif (strlen($formData['subject']) < 5) {
            $errors[] = 'Subject must be at least 5 characters.';
        }
        
        // Validate message
        if (empty($formData['message'])) {
            $errors[] = 'Message is required.';
        } elseif (strlen($formData['message']) < 10) {
            $errors[] = 'Message must be at least 10 characters.';
        }
        
        // If no errors, save to database
        if (empty($errors)) {
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, user_id) VALUES (?, ?, ?, ?, ?)");
                
                $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
                $fullSubject = $formData['category'] ? "[{$formData['category']}] {$formData['subject']}" : $formData['subject'];
                
                $stmt->execute([
                    $formData['name'],
                    $formData['email'],
                    $fullSubject,
                    $formData['message'],
                    $userId
                ]);
                
                $success = true;
                // Clear form
                $formData = ['name' => '', 'email' => '', 'subject' => '', 'category' => '', 'message' => ''];
                
            } catch (PDOException $e) {
                error_log("Contact form error: " . $e->getMessage());
                $errors[] = 'Failed to send message. Please try again later.';
            }
        }
    }
}

// Pre-fill email if logged in
if (isLoggedIn() && empty($formData['email'])) {
    $user = getCurrentUser();
    $formData['email'] = $user['email'];
    $formData['name'] = $user['first_name'] . ' ' . $user['last_name'];
}

$pageTitle = 'Contact Us';
include __DIR__ . '/includes/header.php';
?>

        <!-- Page Header -->
        <section class="page-hero" aria-labelledby="contact-title">
            <h1 id="contact-title">Contact Us</h1>
            <p>We'd love to hear from you</p>
        </section>

        <!-- Contact Section -->
        <section class="contact-section" aria-labelledby="contact-heading">
            <h2 id="contact-heading">Get in Touch</h2>
            
            <div class="contact-container">
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h3>Send us a Message</h3>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <span class="alert-icon">✓</span>
                            <div>
                                <strong>Message sent successfully!</strong><br>
                                We'll get back to you within 24-48 hours.
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error" role="alert">
                            <span class="alert-icon">⚠️</span>
                            <ul class="error-list">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="contactForm" novalidate aria-label="Contact form">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label for="fullName">Full Name *</label>
                            <input 
                                type="text" 
                                id="fullName" 
                                name="fullName" 
                                placeholder="Your full name"
                                value="<?php echo htmlspecialchars($formData['name']); ?>"
                                required
                                aria-required="true">
                            <span class="error-message" id="fullNameError" role="alert"></span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="your@email.com"
                                value="<?php echo htmlspecialchars($formData['email']); ?>"
                                required
                                aria-required="true">
                            <span class="error-message" id="emailError" role="alert"></span>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                placeholder="What is this about?"
                                value="<?php echo htmlspecialchars($formData['subject']); ?>"
                                required
                                aria-required="true">
                            <span class="error-message" id="subjectError" role="alert"></span>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" aria-label="Inquiry category">
                                <option value="">Select a category</option>
                                <option value="support" <?php echo $formData['category'] === 'support' ? 'selected' : ''; ?>>Technical Support</option>
                                <option value="feedback" <?php echo $formData['category'] === 'feedback' ? 'selected' : ''; ?>>Feedback</option>
                                <option value="partnership" <?php echo $formData['category'] === 'partnership' ? 'selected' : ''; ?>>Partnership</option>
                                <option value="other" <?php echo $formData['category'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                placeholder="Your message here..."
                                rows="6"
                                required
                                aria-required="true"><?php echo htmlspecialchars($formData['message']); ?></textarea>
                            <span class="error-message" id="messageError" role="alert"></span>
                        </div>

                        <div class="form-group checkbox">
                            <input 
                                type="checkbox" 
                                id="subscribe" 
                                name="subscribe">
                            <label for="subscribe">Subscribe to our newsletter</label>
                        </div>

                        <button type="submit" class="submit-btn" aria-label="Submit contact form">Send Message</button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info">
                    <h3>Contact Information</h3>
                    
                    <article class="info-card">
                        <h4>📧 Email</h4>
                        <p><a href="mailto:support@soundvibe.com">support@soundvibe.com</a></p>
                        <p><a href="mailto:partnerships@soundvibe.com">partnerships@soundvibe.com</a></p>
                    </article>

                    <article class="info-card">
                        <h4>📞 Phone</h4>
                        <p><a href="tel:+1234567890">+1 (234) 567-890</a></p>
                        <p>Mon - Fri: 9:00 AM - 6:00 PM EST</p>
                    </article>

                    <article class="info-card">
                        <h4>🌐 Social Media</h4>
                        <p>
                            <a href="#" aria-label="Visit our Facebook page">Facebook</a> • 
                            <a href="#" aria-label="Visit our Instagram profile">Instagram</a> • 
                            <a href="#" aria-label="Visit our Twitter profile">Twitter</a>
                        </p>
                    </article>

                    <article class="info-card">
                        <h4>📍 Address</h4>
                        <p>SoundVibe Music Streaming<br>
                           123 Music Street<br>
                           Nashville, TN 37201<br>
                           United States</p>
                    </article>

                    <article class="info-card">
                        <h4>⏰ Business Hours</h4>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM EST<br>
                           Saturday: 10:00 AM - 4:00 PM EST<br>
                           Sunday: Closed</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq" aria-labelledby="faq-heading">
            <h2 id="faq-heading">Frequently Asked Questions</h2>
            <div class="faq-container">
                <details class="faq-item">
                    <summary>How do I create an account?</summary>
                    <p>Visit our website and click on the "Sign Up" button. Fill in your details and follow the verification steps. It takes less than 2 minutes!</p>
                </details>

                <details class="faq-item">
                    <summary>What audio quality does SoundVibe offer?</summary>
                    <p>We offer multiple quality options: Standard (96 kbps), High (192 kbps), and Premium (320 kbps). Choose based on your preference and bandwidth.</p>
                </details>

                <details class="faq-item">
                    <summary>Can I download songs for offline listening?</summary>
                    <p>Yes! Premium members can download up to 10,000 songs to listen offline. Free members can still create playlists and follow artists.</p>
                </details>

                <details class="faq-item">
                    <summary>How much does a subscription cost?</summary>
                    <p>We offer a Free plan with basic features and a Premium plan at $9.99/month. Family plans are also available at special rates.</p>
                </details>
            </div>
        </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
