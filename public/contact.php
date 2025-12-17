<?php
// Start session and database connection
session_start();
require_once '../admin/includes/connection.php';




// Handle contact form submission
$form_message = '';
$form_success = false;
$form_error = '';

// Add this function at the top of your contact.php file
function isSpam($data) {
    global $conn;
    
    $spamScore = 0;
    
    // 1. Check for too many links in message (spammers often include links)
    $linkCount = preg_match_all('/https?:\/\/|www\./', $data['message']);
    if ($linkCount > 2) {
        $spamScore += 10 * $linkCount;
    }
    
    // 2. Check for suspicious keywords
    $spamKeywords = [
        'viagra', 'cialis', 'casino', 'poker', 'lottery', 'prize', 'winner',
        'investment', 'earn money', 'work from home', 'make money fast',
        'click here', 'buy now', 'discount', 'cheap', 'affordable',
        'promotion', 'limited offer', 'urgent', 'asap', '!!!', '???'
    ];
    
    foreach ($spamKeywords as $keyword) {
        if (stripos($data['message'], $keyword) !== false) {
            $spamScore += 5;
        }
    }
    
    // 3. Check email for suspicious patterns
    if (preg_match('/^\d/', $data['email']) || 
        preg_match('/@(gmail|yahoo|hotmail)\.(ru|cn|pl|tk|ml|ga|cf)$/', $data['email'])) {
        $spamScore += 15;
    }
    
    // 4. Check name for suspicious patterns (all caps, numbers, etc.)
    if (preg_match('/[A-Z]{4,}/', $data['name']) || 
        preg_match('/\d/', $data['name']) ||
        strlen($data['name']) < 2 ||
        strlen($data['name']) > 50) {
        $spamScore += 5;
    }
    
    // 5. Check message length (too short or suspiciously long)
    $messageLength = strlen($data['message']);
    if ($messageLength < 10) {
        $spamScore += 10;
    } elseif ($messageLength > 5000) {
        $spamScore += 10;
    }
    
    // 6. Check for too many special characters
    $specialCharCount = preg_match_all('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $data['message']);
    if ($specialCharCount > 20) {
        $spamScore += 10;
    }
    
    // 7. Check submission frequency from same IP (requires database)
    if (checkSubmissionFrequency($data['ip_address'])) {
        $spamScore += 20;
    }
    
    return $spamScore > 15; // Mark as spam if score > 15
}

function checkSubmissionFrequency($ip) {
    global $conn;
    
    // Check submissions in last hour
    $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM contact_messages WHERE ip_address = ? AND created_at > ?");
    $stmt->bind_param("ss", $ip, $oneHourAgo);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] >= 5; // More than 5 submissions per hour = spam
}

function logSpamAttempt($data) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO spam_logs 
        (name, email, phone, subject, message, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $spamScore = isSpam($data) ? 100 : 0;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt->bind_param(
        "sssssss",
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['subject'],
        $data['message'],
        $data['ip_address'],
        $user_agent
    );
    
    $stmt->execute();
}

// Update your contact form submission handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    try {
        // Get form data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        $honeypot = $_POST['website'] ?? ''; // Honeypot field
        $timestamp = $_POST['timestamp'] ?? '';
        
        // Basic validation
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            throw new Exception("Please fill all required fields.");
        }
        
        // Honeypot check (should be empty)
        if (!empty($honeypot)) {
            throw new Exception("Invalid submission detected.");
        }
        
        // Time check - form should take at least 3 seconds to fill
        if (!empty($timestamp) && (time() - $timestamp) < 3) {
            throw new Exception("Please take your time filling the form.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        if (strlen($message) < 10) {
            throw new Exception("Please enter a more detailed message (minimum 10 characters).");
        }
        
        // Spam check
        $formData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        if (isSpam($formData)) {
            // Log spam attempt but don't notify user
            logSpamAttempt($formData);
            throw new Exception("Thank you for your message. We'll get back to you if needed.");
        }
        
        // Get IP and user agent for security logging
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Insert into database
        $stmt = $conn->prepare("
            INSERT INTO contact_messages 
            (name, email, phone, subject, message, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("sssssss", $name, $email, $phone, $subject, $message, $ip_address, $user_agent);
        
        if ($stmt->execute()) {
            $form_message = "Thank you for your message! We'll get back to you within 24 hours.";
            $form_success = true;
            
            // Optionally send email notification to admin
            $to = "info@zubitours.com";
            $email_subject = "New Contact Form Submission: " . $subject;
            $email_body = "
                Name: $name\n
                Email: $email\n
                Phone: " . ($phone ?: "Not provided") . "\n
                Subject: $subject\n
                Message:\n$message\n\n
                IP Address: $ip_address\n
                Time: " . date('Y-m-d H:i:s') . "
            ";
            $headers = "From: $email\r\n" .
                       "Reply-To: $email\r\n" .
                       "X-Mailer: PHP/" . phpversion();
            
            // Uncomment to enable email sending
            // mail($to, $email_subject, $email_body, $headers);
            
            // Clear form data on success
            $_POST = array();
            
        } else {
            throw new Exception("Error sending message. Please try again.");
        }
        
    } catch (Exception $e) {
        $form_message = $e->getMessage();
        $form_success = false;
        $form_error = $e->getMessage();
    }
}

//clear form data after submission




// Fetch FAQs for display
$faqs = $conn->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC");

// Fetch contact settings
$settings_result = $conn->query("SELECT * FROM contact_settings");
$contact_info = [];
$social_links = [];
$faq_categories = [];

while ($row = $settings_result->fetch_assoc()) {
    if ($row['category'] === 'contact_info') {
        $contact_info[$row['setting_key']] = $row['setting_value'];
    } elseif ($row['category'] === 'social_media') {
        $social_links[$row['setting_key']] = $row['setting_value'];
    } elseif ($row['category'] === 'faq_categories') {
        $faq_categories[] = $row['setting_value'];
    }
}

// Get business hours
$business_hours_weekdays = $contact_info['business_hours_weekdays'] ?? 'Monday - Saturday: 9:00 AM - 6:00 PM';
$business_hours_weekends = $contact_info['business_hours_weekends'] ?? 'Sunday: 10:00 AM - 2:00 PM';

// Get contact emails
$contact_emails = [
    $contact_info['contact_email_1'] ?? 'info@zubitours.com',
    $contact_info['contact_email_2'] ?? 'saleszubitours@gmail.com',
    $contact_info['contact_email_3'] ?? 'b2b.zubitourskashmir@gmail.com'
];

// Get contact phones
$contact_phones = [
    $contact_info['contact_phone_1'] ?? '+91 7051073293',
    $contact_info['contact_phone_2'] ?? '+91 7006296814',
    $contact_info['contact_phone_3'] ?? '+91 6006696105',
    $contact_info['contact_phone_4'] ?? '+91 9149736660'
];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />
    
    <title>Zubi Tours & Holidays - Contact Us</title>
    
    <style>
         :root {
          
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --card-radius: 16px;
            --transition: all 0.3s ease;
          
            
        }
        
       
       
        /* Notification Styles */
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 1000;
            padding: 20px 25px;
            border-radius: 12px;

            color: white;
            font-weight: 500;
            box-shadow: var(--shadow);
           
            max-width: 400px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid;
        }
        
        .notification.success {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            border-left-color: #34d399;
        }
        
        .notification.error {
            background: linear-gradient(135deg, var(--error-color) 0%, #dc2626 100%);
            border-left-color: #f87171;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
      
    </style>
  </head>
  <body>
    <!-- Loader -->
    <!-- <div id="loader">
      <div class="travel-loader">
        <span class="path"></span>
        <i class="ri-flight-takeoff-line plane"></i>
      </div>
      <h2 class="brand-name">Zubi Tours & Holidays</h2>
    </div> -->
    
    <!-- Notification -->
    <?php if ($form_message): ?>
        <div class="notification <?php echo $form_success ? 'success' : 'error'; ?>">
            <i class="ri-<?php echo $form_success ? 'check' : 'close'; ?>-circle-fill"></i>
            <span><?php echo htmlspecialchars($form_message); ?></span>
        </div>
    <?php endif; ?>
    
    <!--==================== HEADER ====================-->
    <?php include '../admin/includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="section-header">
            <h2>Contact Us</h2>
            <p>We're here to help you plan your perfect Kashmir or Ladakh adventure</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-content">
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Reach out to us for any inquiries or assistance. Our team is ready to help you plan your dream vacation.</p>

                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Address</h3>
                            <p><?php echo htmlspecialchars($contact_info['contact_address'] ?? 'R-13 Wichka Complex Naqashpora Barbar Shah- Bab-demb Rd, Srinagar, 190001.'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="ri-phone-line"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Phone Numbers</h3>
                            <?php foreach ($contact_phones as $phone): ?>
                                <?php if (!empty($phone)): ?>
                                    <p><?php echo htmlspecialchars($phone); ?></p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="ri-mail-line"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Email Addresses</h3>
                            <?php foreach ($contact_emails as $email): ?>
                                <?php if (!empty($email)): ?>
                                    <p><?php echo htmlspecialchars($email); ?></p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Business Hours</h3>
                            <p><?php echo htmlspecialchars($business_hours_weekdays); ?></p>
                            <p><?php echo htmlspecialchars($business_hours_weekends); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="social-links-con">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <?php if (!empty($social_links['social_facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($social_links['social_facebook']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="ri-facebook-fill"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social_links['social_instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($social_links['social_instagram']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="ri-instagram-line"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social_links['social_twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($social_links['social_twitter']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="ri-twitter-fill"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social_links['social_youtube'])): ?>
                            <a href="<?php echo htmlspecialchars($social_links['social_youtube']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="ri-youtube-fill"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($social_links['social_linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($social_links['social_linkedin']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="ri-linkedin-fill"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container">
                <h2>Send Us a Message</h2>
                <form class="contact-form" id="contactForm" method="POST" novalidate>
                    <input type="hidden" name="submit_contact" value="1">
                    
                    <?php if ($form_error && !$form_success): ?>
                        <div class="form-message error">
                            <i class="ri-error-warning-line"></i>
                            <span><?php echo htmlspecialchars($form_error); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               placeholder="Enter your full name">
                        <div class="error-message">Please enter your name</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="Enter your email address">
                        <div class="error-message">Please enter a valid email address</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               placeholder="Enter your phone number (optional)">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <select id="subject" name="subject" required>
                            <option value="" disabled <?php echo !isset($_POST['subject']) ? 'selected' : ''; ?>>Select a subject</option>
                            <option value="general" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                            <option value="booking" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'booking') ? 'selected' : ''; ?>>Booking Information</option>
                            <option value="custom" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'custom') ? 'selected' : ''; ?>>Custom Package Request</option>
                            <option value="feedback" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                            <option value="complaint" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'complaint') ? 'selected' : ''; ?>>Complaint</option>
                            <option value="other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <div class="error-message">Please select a subject</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required 
                                  placeholder="Type your message here..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <div class="char-counter" id="charCounter">
                            <span id="charCount">0</span>/1000 characters
                        </div>
                        <div class="error-message">Please enter your message (minimum 10 characters)</div>
                    </div>
                    
                    <!-- Honeypot field - hidden from users but visible to bots -->
                    <div style="position: absolute; left: -9999px; opacity: 0;">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <!-- Timestamp for time-based validation -->
                    <input type="hidden" name="timestamp" value="<?php echo time(); ?>">
                    
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="ri-send-plane-line"></i>
                        <span id="submitText">Send Message</span>
                        <div class="loader" id="submitLoader" style="display: none;"></div>
                    </button>
                    
                    <p style="text-align: center; margin-top: 20px; color: var(--text-secondary); font-size: 0.9rem;">
                        <i class="ri-information-line"></i> We typically respond within 24 hours
                    </p>
                </form>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <?php if ($faqs && $faqs->num_rows > 0): ?>
    <section class="faq-section">
        <div class="section-heading">
            <h2>Frequently Asked Questions</h2>
            <p>Quick answers to common questions about our tours and services</p>
        </div>
        
        <div class="faq-container">
            <?php while ($faq = $faqs->fetch_assoc()): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                        <i class="ri-arrow-down-s-line"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                        <?php if (!empty($faq['category'])): ?>
                            <div style="margin-top: 15px; font-size: 0.9rem; color: var(--primary-color);">
                                <i class="ri-price-tag-line"></i> Category: <?php echo htmlspecialchars($faq['category']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Map Section -->
    <section class="map-section">
        <div class="section-heading">
            <h2>Our Location</h2>
            <p>Visit our office in Srinagar, Kashmir</p>
        </div>
        
        <div class="map-container">
            <!-- Google Maps Embed -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.953739230627!2d74.80288731521426!3d34.08377468060502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38e1855c5d64b5fd%3A0x1e3e3c2b2b2b2b2b!2sSrinagar%2C%20Jammu%20and%20Kashmir!5e0!3m2!1sen!2sin!4v1628671234567!5m2!1sen!2sin" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy"
                title="Zubi Tours Location Map">
            </iframe>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3>Zubi Tours & Holidays</h3>
                <p>Creating unforgettable experiences in the paradise of Kashmir and the majestic landscapes of Ladakh.</p>
                <div class="social-links">
                    <?php if (!empty($social_links['social_facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($social_links['social_facebook']); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="ri-facebook-fill"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($social_links['social_instagram'])): ?>
                        <a href="<?php echo htmlspecialchars($social_links['social_instagram']); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="ri-instagram-line"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($social_links['social_twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($social_links['social_twitter']); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="ri-twitter-fill"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($social_links['social_youtube'])): ?>
                        <a href="<?php echo htmlspecialchars($social_links['social_youtube']); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="ri-youtube-fill"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="/public/about.php">About Us</a></li>
                    <li><a href="/public/destinations.php">Destinations</a></li>
                    <li><a href="/public/packages.php">Packages</a></li>
                    <li><a href="/public/gallery.php">Gallery</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="/public/packages.php">Tour Packages</a></li>
                    <li><a href="/public/car-rentals.php">Car Rentals</a></li>
                    <li><a href="#">Hotel Booking</a></li>
                    <li><a href="#">Adventure Activities</a></li>
                    <li><a href="#">Pilgrimage Tours</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Contact Info</h4>
                <div class="contact-info">
                    <p><i class="ri-map-pin-line"></i> 
                        <?php echo htmlspecialchars($contact_info['contact_address'] ?? 'Srinagar, Jammu & Kashmir'); ?>
                    </p>
                    <p><i class="ri-phone-line"></i> 
                        <?php echo htmlspecialchars($contact_phones[0] ?? '+91 7006296814'); ?>
                    </p>
                    <p><i class="ri-mail-line"></i> 
                        <?php echo htmlspecialchars($contact_emails[0] ?? 'info@zubitours.com'); ?>
                    </p>
                    <p><i class="ri-time-line"></i> 
                        <?php echo htmlspecialchars($business_hours_weekdays); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <span id="getYear"></span> Zubi Tours & Holidays. All rights reserved.</p>
            <p> Powered By <a href="https://irfanmanzoor.in" target="_blank" rel="noopener noreferrer">EXORA</a></p>
        </div>
    </footer>

    <!-- Linking Swiper script -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    
    <script>
        // FAQ functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                const isActive = item.classList.contains('active');
                
                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(faqItem => {
                    faqItem.classList.remove('active');
                });
                
                // Open clicked item if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
        
        // Contact form handling
        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        
        // Character counter for message
        const messageTextarea = document.getElementById('message');
        const charCounter = document.getElementById('charCounter');
        const charCount = document.getElementById('charCount');
        const maxChars = 1000;
        
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Update counter color based on length
            charCounter.className = 'char-counter';
            if (length > maxChars * 0.9) {
                charCounter.classList.add('warning');
            } else if (length > maxChars) {
                charCounter.classList.add('error');
            }
            
            // Validate length
            if (length < 10) {
                setFieldError(this, 'Message must be at least 10 characters');
            } else if (length > maxChars) {
                setFieldError(this, `Message cannot exceed ${maxChars} characters`);
            } else {
                clearFieldError(this);
            }
        });
        
        // Form validation functions
        function setFieldError(field, message) {
            const formGroup = field.closest('.form-group');
            const errorElement = formGroup.querySelector('.error-message');
            
            formGroup.classList.add('error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
        
        function clearFieldError(field) {
            const formGroup = field.closest('.form-group');
            formGroup.classList.remove('error');
        }
        
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        function validatePhone(phone) {
            if (!phone) return true; // Phone is optional
            const re = /^[+]?[0-9\s\-\(\)]{10,}$/;
            return re.test(phone);
        }
        
        // Real-time validation
        const formFields = {
            name: document.getElementById('name'),
            email: document.getElementById('email'),
            phone: document.getElementById('phone'),
            subject: document.getElementById('subject'),
            message: document.getElementById('message')
        };
        
        formFields.name.addEventListener('blur', function() {
            if (!this.value.trim()) {
                setFieldError(this, 'Please enter your name');
            } else {
                clearFieldError(this);
            }
        });
        
        formFields.email.addEventListener('blur', function() {
            if (!this.value.trim()) {
                setFieldError(this, 'Please enter your email address');
            } else if (!validateEmail(this.value)) {
                setFieldError(this, 'Please enter a valid email address');
            } else {
                clearFieldError(this);
            }
        });
        
        formFields.phone.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                setFieldError(this, 'Please enter a valid phone number');
            } else {
                clearFieldError(this);
            }
        });
        
        formFields.subject.addEventListener('change', function() {
            if (!this.value) {
                setFieldError(this, 'Please select a subject');
            } else {
                clearFieldError(this);
            }
        });
        
        // Form submission
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset all errors
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });
            
            let isValid = true;
            
            // Validate name
            if (!formFields.name.value.trim()) {
                setFieldError(formFields.name, 'Please enter your name');
                isValid = false;
            }
            
            // Validate email
            if (!formFields.email.value.trim()) {
                setFieldError(formFields.email, 'Please enter your email address');
                isValid = false;
            } else if (!validateEmail(formFields.email.value)) {
                setFieldError(formFields.email, 'Please enter a valid email address');
                isValid = false;
            }
            
            // Validate phone
            if (formFields.phone.value && !validatePhone(formFields.phone.value)) {
                setFieldError(formFields.phone, 'Please enter a valid phone number');
                isValid = false;
            }
            
            // Validate subject
            if (!formFields.subject.value) {
                setFieldError(formFields.subject, 'Please select a subject');
                isValid = false;
            }
            
            // Validate message
            if (!formFields.message.value.trim()) {
                setFieldError(formFields.message, 'Please enter your message');
                isValid = false;
            } else if (formFields.message.value.trim().length < 10) {
                setFieldError(formFields.message, 'Message must be at least 10 characters');
                isValid = false;
            } else if (formFields.message.value.length > maxChars) {
                setFieldError(formFields.message, `Message cannot exceed ${maxChars} characters`);
                isValid = false;
            }
            
            if (!isValid) {
                // Scroll to first error
                const firstError = document.querySelector('.form-group.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }
            
            // Show loading state
            submitText.style.display = 'none';
            submitLoader.style.display = 'block';
            submitBtn.disabled = true;
            
            // Submit form
            this.submit();
        });
        
        // Auto-hide notification after 5 seconds
        setTimeout(() => {
            const notification = document.querySelector('.notification');
            if (notification) {
                notification.style.display = 'none';
            }
        }, 5000);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set current year in footer
            document.getElementById('getYear').textContent = new Date().getFullYear();
            
            // Initialize character counter
            const initialLength = messageTextarea.value.length;
            charCount.textContent = initialLength;
            
            // Auto-hide loader
            setTimeout(() => {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 300);
                }
            }, 1000);
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;
                    
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            // Google Maps enhancement
            const mapIframe = document.querySelector('iframe');
            if (mapIframe) {
                mapIframe.addEventListener('load', function() {
                    // Add loading indicator removal here if needed
                });
            }
        });
        
        // Add copy to clipboard for contact info
        document.querySelectorAll('.contact-text p').forEach(p => {
            p.addEventListener('click', function() {
                const text = this.textContent.trim();
                if (navigator.clipboard && text) {
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        this.style.color = '#10b981';
                        
                        setTimeout(() => {
                            this.textContent = originalText;
                            this.style.color = '';
                        }, 2000);
                    });
                }
            });
        });



      
       
    </script>




  </body>
</html>