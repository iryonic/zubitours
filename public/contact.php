<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!--=============== REMIXICONS ===============-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />

    <title>Zubi tours & Holiday - Contact Us</title>
  </head>
  <body>
        <!-- Loader -->
<div id="loader">
  <div class="travel-loader">
    <span class="path"></span>
    <i class="ri-flight-takeoff-line plane"></i>
  </div>
  <h2 class="brand-name">Zubi Tours & Holiday</h2>
</div>

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
          <p>Reach out to us for any inquiries or assistance</p>

          <div class="contact-details">
            <div class="contact-item">
              <div class="contact-icon">
                <i class="ri-map-pin-line"></i>
              </div>
              <div class="contact-text">
                <h3>Address</h3>
                <p>R-13 Wichka Complex Naqashpora Barbar Shah- Bab-demb Rd, Srinagar, 190001.</p>
              </div>
            </div>
            
            <div class="contact-item">
              <div class="contact-icon">
                <i class="ri-phone-line"></i>
              </div>
              <div class="contact-text">
                <h3>Phone</h3>
                <p>+91 7051073293</p>
                <p>+91 7006296814</p>
                <p>+91 6006696105</p>
                <p>+91 9149736660</p>
                
              </div>
            </div>
            
            <div class="contact-item">
              <div class="contact-icon">
                <i class="ri-mail-line"></i>
              </div>
              <div class="contact-text">
                <h3>Email</h3>
                <p>info@zubitours.com</p>
                <p>saleszubitours@gmail.com</p>
                <p>b2b.zubitourskashmir@gmail.com</p>

              </div>
            </div>
            
            <div class="contact-item">
              <div class="contact-icon">
                <i class="ri-time-line"></i>
              </div>
              <div class="contact-text">
                <h3>Business Hours</h3>
                <p>Monday - Saturday: 9s:00 AM - 6:00 PM</p>
                <p>Sunday: 10:00 AM - 2:00 PM</p>
              </div>
            </div>
          </div>
          
          <div class="social-links-con">
            <h3>Follow Us</h3>
            <div class="social-icons">
              <a href="#"><i class="ri-facebook-fill"></i></a>
              <a href="https://www.instagram.com/zubi_tours_n_holidays_kashmir?igsh=OXhwMG04MWZtdnp6"><i class="ri-instagram-line"></i></a>
              <a href="#"><i class="ri-twitter-fill"></i></a>
              <a href="#"><i class="ri-youtube-fill"></i></a>
              <a href="#"><i class="ri-linkedin-fill"></i></a>
            </div>
          </div>
        </div>
        
        <div class="contact-form-container">
          <h2>Send Us a Message</h2>
          <form class="contact-form" id="contactForm">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-group">
              <label for="subject">Subject *</label>
              <select id="subject" name="subject" required>
                <option value="">Select a subject</option>
                <option value="general">General Inquiry</option>
                <option value="booking">Booking Information</option>
                <option value="custom">Custom Package Request</option>
                <option value="feedback">Feedback</option>
                <option value="complaint">Complaint</option>
                <option value="other">Other</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="message">Message *</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Send Message</button>
          </form>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
      <div class="section-heading">
        <h2>Frequently Asked Questions</h2>
        <p>Quick answers to common questions</p>
      </div>
      
      <div class="faq-container">
        <div class="faq-item">
          <div class="faq-question">
            <h3>What is the best time to visit Kashmir?</h3>
            <i class="ri-arrow-down-s-line"></i>
          </div>
          <div class="faq-answer">
            <p>Kashmir is beautiful throughout the year, but the best time depends on your preferences. Spring (March to May) offers blooming flowers, summer (June to August) is perfect for sightseeing, autumn (September to November) has stunning foliage, and winter (December to February) is ideal for snow activities.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h3>Do I need any permits for Ladakh?</h3>
            <i class="ri-arrow-down-s-line"></i>
          </div>
          <div class="faq-answer">
            <p>Yes, certain areas in Ladakh require permits for domestic and international tourists. We handle all permit applications for our clients as part of our tour packages, making the process hassle-free for you.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h3>What is your cancellation policy?</h3>
            <i class="ri-arrow-down-s-line"></i>
          </div>
          <div class="faq-answer">
            <p>We offer a flexible cancellation policy. Cancellations made 30 days before the tour receive a full refund. Between 15-30 days, we refund 70% of the amount. For cancellations within 15 days, we offer a 50% refund or the option to reschedule.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h3>Are your tours suitable for elderly travelers?</h3>
            <i class="ri-arrow-down-s-line"></i>
          </div>
          <div class="faq-answer">
            <p>Absolutely! We offer specially curated tours with comfortable transportation, manageable itineraries, and accommodations that cater to the needs of elderly travelers. We can also arrange for medical assistance if needed.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h3>Do you offer customized packages?</h3>
            <i class="ri-arrow-down-s-line"></i>
          </div>
          <div class="faq-answer">
            <p>Yes, we specialize in creating personalized itineraries based on your preferences, budget, and time constraints. Our travel experts will work with you to design the perfect Kashmir or Ladakh experience.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
      <div class="section-heading">
        <h2>Our Location</h2>
        <p>Visit our office in Srinagar</p>
      </div>
      
      <div class="map-container">
        <div class="map-placeholder">
          <i class="ri-map-pin-line"></i>
          <h3>Interactive Map</h3>
          <p>Google Maps integration would appear here</p>
          <p>123 Boulevard Road, Srinagar, Jammu & Kashmir</p>
        </div>
      </div>
    </section>


       <!-- FOOTER -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3>Zubi Tours & Holidays</h3>
      <p>Creating unforgettable experiences in the paradise of Kashmir and the majestic landscapes of Ladakh.</p>
      <div class="social-links">
        <a href="./package-details.html"><i class="ri-facebook-fill"></i></a>
        <a href="./package-details.html"><i class="ri-instagram-line"></i></a>
        <a href="./package-details.html"><i class="ri-twitter-fill"></i></a>
        <a href="./package-details.html"><i class="ri-youtube-fill"></i></a>
      </div>
    </div>
    
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="../index.html">Home</a></li>
        <li><a href="/public/about.html">About Us</a></li>
        <li><a href="/public/destinations.html">Destinations</a></li>
        <li><a href="/public/packages.html">Packages</a></li>
        <li><a href="/public/gallery.html">Gallery</a></li>
      </ul>
    </div>
    
    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="/public/packages.html">Tour Packages</a></li>
        <li><a href="/public/car-rentals.html">Car Rentals</a></li>
        <li><a href="#">Hotel Booking</a></li>
        <li><a href="#">Adventure Activities</a></li>
        <li><a href="#">Pilgrimage Tours</a></li>
      </ul>
    </div>
    
    <div class="footer-col">
      <h4>Contact Info</h4>
      <div class="contact-info">
        <p><i class="ri-map-pin-line"></i> Srinagar, Jammu & Kashmir</p>
        <p><i class="ri-phone-line"></i> +91 7006296814</p>
        <p><i class="ri-mail-line"></i> info@zubitours.com</p>
        <p><i class="ri-time-line"></i> Mon-Sat: 9AM - 6PM</p>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <p>&copy; <span id="getYear"></span> Zubi Tours & Holidays. All rights reserved.</p>
    <p> Powered By <a href="https://irfanmanzoor.in">EXORA</a></p>
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
          item.classList.toggle('active');
        });
      });
      
      // Contact form handling
      document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Form validation
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        
        if (!name || !email || !subject || !message) {
          alert('Please fill all required fields');
          return;
        }
        
        // Here you would typically send the form data to your server
        alert('Thank you for your message! We will get back to you within 24 hours.');
        this.reset();
      });
    </script>
  </body>
</html>