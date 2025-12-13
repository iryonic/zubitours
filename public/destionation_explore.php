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

    <title>Explore Destination - Zubi tours & Holiday</title>
   
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

    <!-- Destination Hero Section -->
    <section class="modern-hero">
      <div class="hero-background" id="destination-hero-bg"></div>
      <div class="hero-content">
        <h1 id="destination-title">Dal Lake</h1>
        <p id="destination-subtitle">The Jewel of Srinagar</p>
      </div>
    </section>

    <!-- Destination Details Container -->
    <div class="destination-details-container">
      <!-- Main Content -->
      <div class="destination-content">
        <!-- Overview Section -->
        <section class="destination-section">
          <h2 class="section-title">Destination Overview</h2>
          <p id="destination-description" class="section-para">
            Dal Lake is the jewel of Srinagar and the second largest lake in Jammu & Kashmir. 
            It is integral to tourism and recreation in Kashmir and is named the "Jewel in the crown of Kashmir" 
            or "Srinagar's Jewel". The lake is famous for its houseboats, shikaras, and floating gardens.
          </p>
          
          <div class="highlight-stats">
            <div class="stat-item">
              <i class="ri-map-pin-line"></i>
              <div>
                <h3>Location</h3>
                <p id="location-detail">Srinagar, Jammu & Kashmir</p>
              </div>
            </div>
            <div class="stat-item">
              <i class="ri-sun-line"></i>
              <div>
                <h3>Best Time to Visit</h3>
                <p id="best-time">April to October</p>
              </div>
            </div>
            <div class="stat-item">
              <i class="ri-temp-cold-line"></i>
              <div>
                <h3>Weather</h3>
                <p id="weather-info">Temperate: 5°C to 30°C</p>
              </div>
            </div>
            <div class="stat-item">
              <i class="ri-star-line"></i>
              <div>
                <h3>Rating</h3>
                <p id="rating-info">4.8/5 (2,500+ reviews)</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Gallery Preview -->
        <section class="destination-section">
          <h2 class="section-title">Photo Gallery</h2>
          <div class="gallery-preview" id="destination-gallery">
            <!-- Gallery images will be populated by JavaScript -->
          </div>
        </section>

        <!-- Highlights Section -->
        <section class="destination-section">
          <h2 class="section-title">Key Highlights</h2>
          <div class="highlights-grid">
            <div class="highlight-card">
              <i class="ri-home-5-line"></i>
              <h3>Houseboat Stay</h3>
              <p>Experience unique accommodation on traditional Kashmiri houseboats</p>
            </div>
            <div class="highlight-card">
              <i class="ri-sailboat-line"></i>
              <h3>Shikara Ride</h3>
              <p>Romantic boat rides through the floating gardens and markets</p>
            </div>
            <div class="highlight-card">
              <i class="ri-shopping-bag-line"></i>
              <h3>Floating Market</h3>
              <p>Shop at the unique floating vegetable and flower markets</p>
            </div>
            <div class="highlight-card">
              <i class="ri-camera-line"></i>
              <h3>Photography</h3>
              <p>Capture stunning sunrise and sunset views over the lake</p>
            </div>
          </div>
        </section>

        <!-- Activities Section -->
        <section class="destination-section">
          <h2 class="section-title">Activities & Experiences</h2>
          <div class="activities-list">
            <div class="activity-item">
              <div class="activity-icon">
                <i class="ri-sailboat-line"></i>
              </div>
              <div class="activity-content">
                <h3>Shikara Ride Tour</h3>
                <p>2-hour guided tour through the lake's main attractions including floating gardens and markets</p>
                <span class="activity-duration">Duration: 2 hours</span>
              </div>
              <button class="book-activity-btn">Book Now</button>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon">
                <i class="ri-restaurant-line"></i>
              </div>
              <div class="activity-content">
                <h3>Floating Restaurant Dinner</h3>
                <p>Authentic Kashmiri cuisine served on a traditional houseboat</p>
                <span class="activity-duration">Duration: 3 hours</span>
              </div>
              <button class="book-activity-btn">Book Now</button>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon">
                <i class="ri-camera-line"></i>
              </div>
              <div class="activity-content">
                <h3>Sunrise Photography Tour</h3>
                <p>Early morning tour with professional photographer for best shots</p>
                <span class="activity-duration">Duration: 4 hours</span>
              </div>
              <button class="book-activity-btn">Book Now</button>
            </div>
          </div>
        </section>

        <!-- Packages Section -->
        <section class="destination-section">
          <h2 class="section-title">Recommended Packages</h2>
          <div class="packages-grid">
            <div class="package-card">
              <div class="package-header">
                <h3>Day Tour</h3>
                <span class="package-price">₹2,500</span>
              </div>
              <ul class="package-features">
                <li><i class="ri-check-line"></i> Guided Shikara Ride</li>
                <li><i class="ri-check-line"></i> Lunch on Houseboat</li>
                <li><i class="ri-check-line"></i> Photography Session</li>
                <li><i class="ri-check-line"></i> Souvenir Shopping</li>
              </ul>
              <button class="package-book-btn">Book Package</button>
            </div>
            
            <div class="package-card highlight">
              <div class="package-badge">Most Popular</div>
              <div class="package-header">
                <h3>Weekend Getaway</h3>
                <span class="package-price">₹8,500</span>
              </div>
              <ul class="package-features">
                <li><i class="ri-check-line"></i> 2 Nights Houseboat Stay</li>
                <li><i class="ri-check-line"></i> All Meals Included</li>
                <li><i class="ri-check-line"></i> Full Day Lake Tour</li>
                <li><i class="ri-check-line"></i> Cultural Show</li>
                <li><i class="ri-check-line"></i> Airport Transfers</li>
              </ul>
              <button class="package-book-btn">Book Package</button>
            </div>
            
            <div class="package-card">
              <div class="package-header">
                <h3>Luxury Experience</h3>
                <span class="package-price">₹15,000</span>
              </div>
              <ul class="package-features">
                <li><i class="ri-check-line"></i> 3 Nights Premium Houseboat</li>
                <li><i class="ri-check-line"></i> Private Shikara with Butler</li>
                <li><i class="ri-check-line"></i> Spa & Wellness</li>
                <li><i class="ri-check-line"></i> Gourmet Dining</li>
                <li><i class="ri-check-line"></i> Personal Photographer</li>
              </ul>
              <button class="package-book-btn">Book Package</button>
            </div>
          </div>
        </section>

        <!-- Travel Tips Section -->
        <section class="destination-section">
          <h2 class="section-title">Travel Tips & Information</h2>
          <div class="travel-tips">
            <div class="tip-card">
              <i class="ri-suitcase-line"></i>
              <h3>What to Pack</h3>
              <ul>
                <li>Warm clothing (evenings can be chilly)</li>
                <li>Comfortable walking shoes</li>
                <li>Camera with extra batteries</li>
                <li>Sunscreen and sunglasses</li>
                <li>Light rain jacket</li>
              </ul>
            </div>
            
            <div class="tip-card">
              <i class="ri-information-line"></i>
              <h3>Important Info</h3>
              <ul>
                <li>Best visiting hours: 6 AM - 7 PM</li>
                <li>Entry fee: ₹50 per person</li>
                <li>Shikara rates: ₹500-1500 depending on duration</li>
                <li>Photography allowed (some areas may charge extra)</li>
                <li>Wheelchair accessible in main areas</li>
              </ul>
            </div>
            
            <div class="tip-card">
              <i class="ri-alert-line"></i>
              <h3>Safety Guidelines</h3>
              <ul>
                <li>Always wear life jackets during boat rides</li>
                <li>Follow guide instructions in floating markets</li>
                <li>Keep valuables secure</li>
                <li>Stay hydrated at high altitude</li>
                <li>Respect local customs and traditions</li>
              </ul>
            </div>
          </div>
        </section>
      </div>

      <!-- Sidebar -->
      <aside class="destination-sidebar">
        <!-- Quick Booking Widget -->
        <div class="booking-widget">
          <h3>Book Your Experience</h3>
          <form class="booking-form">
            <div class="form-group">
              <label for="visit-date"><i class="ri-calendar-line"></i> Visit Date</label>
              <input type="date" id="visit-date" required>
            </div>
            
            <div class="form-group">
              <label for="visitors"><i class="ri-user-line"></i> Number of Visitors</label>
              <select id="visitors" required>
                <option value="1">1 Person</option>
                <option value="2">2 Persons</option>
                <option value="3">3 Persons</option>
                <option value="4">4 Persons</option>
                <option value="5">5+ Persons</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="experience-type"><i class="ri-star-line"></i> Experience Type</label>
              <select id="experience-type" required>
                <option value="basic">Basic Tour</option>
                <option value="premium">Premium Experience</option>
                <option value="luxury">Luxury Package</option>
              </select>
            </div>
            
            <div class="price-summary">
              <div class="price-item">
                <span>Base Price</span>
                <span>₹2,500</span>
              </div>
              <div class="price-item">
                <span>Taxes & Fees</span>
                <span>₹300</span>
              </div>
              <div class="price-total">
                <span>Total Amount</span>
                <span>₹2,800</span>
              </div>
            </div>
            
            <button type="submit" class="book-now-btn">
              <i class="ri-wallet-line"></i> Book Now
            </button>
          </form>
        </div>

        <!-- Map Widget -->
        <div class="map-widget">
          <h3>Location Map</h3>
          <div class="map-placeholder">
            <i class="ri-map-pin-line"></i>
            <p>Interactive map showing destination location</p>
            <button class="view-map-btn">View on Google Maps</button>
          </div>
        </div>

        <!-- Contact Widget -->
        <div class="contact-widget">
          <h3>Need Help?</h3>
          <div class="contact-info">
            <p><i class="ri-phone-line"></i> +91 7006296814</p>
            <p><i class="ri-whatsapp-line"></i> WhatsApp Available</p>
            <p><i class="ri-mail-line"></i> tours@zubitours.com</p>
          </div>
          <button class="contact-btn">
            <i class="ri-chat-3-line"></i> Chat with Expert
          </button>
        </div>

        <!-- Similar Destinations -->
        <div class="similar-destinations">
          <h3>Similar Destinations</h3>
          <div class="similar-card">
            <img src="../assets/img/bg1.jpg" alt="Nigeen Lake">
            <div class="similar-content">
              <h4>Nigeen Lake</h4>
              <p>Quieter alternative to Dal Lake</p>
              <a href="#">Explore →</a>
            </div>
          </div>
          
          <div class="similar-card">
            <img src="../assets/img/bg1.jpg" alt="Wular Lake">
            <div class="similar-content">
              <h4>Wular Lake</h4>
              <p>Largest freshwater lake in India</p>
              <a href="#">Explore →</a>
            </div>
          </div>
          
          <div class="similar-card">
            <img src="../assets/img/bg1.jpg" alt="Manasbal Lake">
            <div class="similar-content">
              <h4>Manasbal Lake</h4>
              <p>Deepest lake in Kashmir Valley</p>
              <a href="#">Explore →</a>
            </div>
          </div>
        </div>
      </aside>
    </div>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
      <div class="section-header">
        <h2>Traveler Experiences</h2>
        <p>What our visitors say about their experience</p>
      </div>
      
      <div class="testimonial-cards">
        <div class="testimonial-card">
          <div class="testimonial-text">
            "The houseboat stay was magical! Waking up to the view of Dal Lake with mist rising from the water was unforgettable."
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">
              <img src="../assets/img/bg1.jpg" alt="Reviewer">
            </div>
            <div class="author-details">
              <h4>Priya Sharma</h4>
              <p>Visited in October 2023</p>
              <div class="rating">
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
              </div>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-text">
            "The shikara ride through floating gardens was like stepping into a painting. Our guide made the experience even better."
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">
              <img src="../assets/img/bg1.jpg" alt="Reviewer">
            </div>
            <div class="author-details">
              <h4>Raj Patel</h4>
              <p>Visited in May 2023</p>
              <div class="rating">
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-half-fill"></i>
              </div>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-text">
            "Perfect family destination. Kids loved the boat rides and we enjoyed the peaceful mornings with Kashmiri Kahwa."
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">
              <img src="../assets/img/bg1.jpg" alt="Reviewer">
            </div>
            <div class="author-details">
              <h4>Anita & Family</h4>
              <p>Visited in July 2023</p>
              <div class="rating">
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-content">
        <h2>Ready for Your Kashmir Adventure?</h2>
        <p>Book your dream vacation with expert guidance and customized itineraries</p>
        <div class="cta-buttons">
          <a href="tel:+917006296814" class="cta-button primary">
            <i class="ri-phone-line"></i> Call Now
          </a>
          <a href="./packages.html" class="cta-button secondary">
            <i class="ri-calendar-line"></i> View Packages
          </a>
          <a href="./contact.html" class="cta-button secondary">
            <i class="ri-chat-3-line"></i> Get Quote
          </a>
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
            <a href="#"><i class="ri-facebook-fill"></i></a>
            <a href="#"><i class="ri-instagram-line"></i></a>
            <a href="#"><i class="ri-twitter-fill"></i></a>
            <a href="#"><i class="ri-youtube-fill"></i></a>
          </div>
        </div>
        
        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="../index.html">Home</a></li>
            <li><a href="./about.html">About Us</a></li>
            <li><a href="./destinations.html">Destinations</a></li>
            <li><a href="./packages.html">Packages</a></li>
            <li><a href="./gallery.html">Gallery</a></li>
          </ul>
        </div>
        
        <div class="footer-col">
          <h4>Services</h4>
          <ul>
            <li><a href="./packages.html">Tour Packages</a></li>
            <li><a href="./car-rentals.html">Car Rentals</a></li>
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
    
    <!-- Additional CSS for Explore Page -->
    <style>
      /* Destination Details Container */
      .destination-details-container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 40px;
      }

      /* Destination Sections */
      .destination-section {
        margin-bottom: 60px;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      }

      .destination-section h2 {
        color: var(--title-color);
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--first-color);
      }

      /* Highlight Stats */
      .highlight-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
      }

      .stat-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 10px;
        transition: transform 0.3s ease;
      }

      .stat-item:hover {
        transform: translateY(-5px);
        background: var(--first-color);
        color: white;
      }

      .stat-item:hover i,
      .stat-item:hover h3,
      .stat-item:hover p {
        color: white;
      }

      .stat-item i {
        font-size: 2rem;
        color: var(--first-color);
      }

      .stat-item h3 {
        font-size: 1rem;
        margin-bottom: 5px;
        color: var(--title-color);
      }

      .stat-item p {
        font-size: 0.95rem;
        color: var(--text-color);
        margin: 0;
      }

      /* Gallery Preview */
      #destination-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 20px;
      }

      #destination-gallery img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
        transition: transform 0.3s ease;
      }

      #destination-gallery img:hover {
        transform: scale(1.05);
      }

      /* Highlights Grid */
      .highlights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
      }

      .highlight-card {
        text-align: center;
        padding: 25px;
        background: #f8fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
      }

      .highlight-card:hover {
        background: var(--first-color);
        transform: translateY(-5px);
        color: white;
      }

      .highlight-card:hover i,
      .highlight-card:hover h3,
      .highlight-card:hover p {
        color: white;
      }

      .highlight-card i {
        font-size: 2.5rem;
        color: var(--first-color);
        margin-bottom: 15px;
      }

      .highlight-card h3 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: var(--title-color);
      }

      .highlight-card p {
        font-size: 0.95rem;
        color: var(--text-color);
        line-height: 1.5;
      }

      /* Activities List */
      .activities-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .activity-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 10px;
        transition: transform 0.3s ease;
      }

      .activity-item:hover {
        transform: translateY(-3px);
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }

      .activity-icon {
        width: 60px;
        height: 60px;
        background: var(--first-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
      }

      .activity-content h3 {
        margin: 0 0 8px;
        color: var(--title-color);
      }

      .activity-content p {
        margin: 0 0 8px;
        color: var(--text-color);
        font-size: 0.95rem;
      }

      .activity-duration {
        font-size: 0.85rem;
        color: var(--first-color);
        font-weight: 500;
      }

      .book-activity-btn {
        padding: 12px 25px;
        background: var(--first-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .book-activity-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(42, 61, 232, 0.3);
      }

      /* Packages Grid */
      .packages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
      }

      .package-card {
        position: relative;
        padding: 25px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s ease;
      }

      .package-card.highlight {
        border-color: var(--first-color);
        transform: scale(1.05);
      }

      .package-badge {
        position: absolute;
        top: -10px;
        right: 20px;
        background: var(--first-color);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
      }

      .package-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
      }

      .package-header h3 {
        margin: 0;
        font-size: 1.3rem;
        color: var(--title-color);
      }

      .package-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--first-color);
      }

      .package-features {
        list-style: none;
        padding: 0;
        margin-bottom: 25px;
      }

      .package-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        color: var(--text-color);
      }

      .package-features i {
        color: #10b981;
      }

      .package-book-btn {
        width: 100%;
        padding: 12px;
        background: var(--first-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .package-book-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(42, 61, 232, 0.3);
      }

      /* Travel Tips */
      .travel-tips {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
      }

      .tip-card {
        padding: 25px;
        background: #f8fafc;
        border-radius: 10px;
        transition: transform 0.3s ease;
      }

      .tip-card:hover {
        transform: translateY(-5px);
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }

      .tip-card i {
        font-size: 2rem;
        color: var(--first-color);
        margin-bottom: 15px;
      }

      .tip-card h3 {
        margin: 0 0 15px;
        color: var(--title-color);
      }

      .tip-card ul {
        list-style: none;
        padding: 0;
      }

      .tip-card li {
        padding: 8px 0;
        color: var(--text-color);
        border-bottom: 1px solid #e2e8f0;
      }

      .tip-card li:last-child {
        border-bottom: none;
      }

      /* Sidebar Widgets */
      .destination-sidebar {
        display: flex;
        flex-direction: column;
        gap: 30px;
      }

      .booking-widget,
      .map-widget,
      .contact-widget,
      .similar-destinations {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      }

      .booking-widget h3,
      .map-widget h3,
      .contact-widget h3,
      .similar-destinations h3 {
        margin: 0 0 20px;
        color: var(--title-color);
        padding-bottom: 10px;
        border-bottom: 2px solid var(--first-color);
      }

      /* Booking Form */
      .booking-form .form-group {
        margin-bottom: 20px;
      }

      .booking-form label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--title-color);
      }

      .booking-form input,
      .booking-form select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
      }

      .booking-form input:focus,
      .booking-form select:focus {
        border-color: var(--first-color);
        outline: none;
      }

      .price-summary {
        margin: 25px 0;
        padding: 20px;
        background: #f8fafc;
        border-radius: 8px;
      }

      .price-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: var(--text-color);
      }

      .price-total {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 2px solid var(--first-color);
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--title-color);
      }

      .book-now-btn {
        width: 100%;
        padding: 15px;
        background: var(--first-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
      }

      .book-now-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(42, 61, 232, 0.3);
      }

      /* Map Widget */
      .map-placeholder {
        text-align: center;
        padding: 40px 20px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 15px;
      }

      .map-placeholder i {
        font-size: 3rem;
        color: #94a3b8;
        margin-bottom: 15px;
      }

      .view-map-btn {
        width: 100%;
        padding: 12px;
        background: white;
        color: var(--first-color);
        border: 2px solid var(--first-color);
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .view-map-btn:hover {
        background: var(--first-color);
        color: white;
      }

      /* Contact Widget */
      .contact-info p {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        color: var(--text-color);
      }

      .contact-btn {
        width: 100%;
        padding: 12px;
        background: white;
        color: var(--first-color);
        border: 2px solid var(--first-color);
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
      }

      .contact-btn:hover {
        background: var(--first-color);
        color: white;
      }

      /* Similar Destinations */
      .similar-card {
        display: flex;
        gap: 15px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8fafc;
        border-radius: 8px;
        transition: transform 0.3s ease;
      }

      .similar-card:hover {
        transform: translateX(5px);
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }

      .similar-card img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
      }

      .similar-content h4 {
        margin: 0 0 5px;
        font-size: 1rem;
        color: var(--title-color);
      }

      .similar-content p {
        margin: 0 0 8px;
        font-size: 0.9rem;
        color: var(--text-color);
      }

      .similar-content a {
        font-size: 0.9rem;
        color: var(--first-color);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
      }

      .similar-content a:hover {
        color: var(--title-color);
      }

      /* Responsive Design */
      @media (max-width: 1100px) {
        .destination-details-container {
          grid-template-columns: 1fr;
        }
        
        .destination-sidebar {
          order: -1;
        }
      }

      @media (max-width: 768px) {
        .destination-details-container {
          padding: 0 15px;
        }
        
        .destination-section {
          padding: 20px;
        }
        
        .highlight-stats,
        .highlights-grid,
        .travel-tips {
          grid-template-columns: 1fr;
        }
        
        .packages-grid {
          grid-template-columns: 1fr;
        }
        
        .package-card.highlight {
          transform: none;
        }
        
        .activity-item {
          grid-template-columns: 1fr;
          text-align: center;
        }
        
        .activity-icon {
          margin: 0 auto;
        }
      }

      @media (max-width: 480px) {
        .destination-section {
          padding: 15px;
        }
        
        .booking-widget,
        .map-widget,
        .contact-widget,
        .similar-destinations {
          padding: 20px;
        }
      }
    </style>

    <script>
      // JavaScript for Destination Details Page
      document.addEventListener('DOMContentLoaded', function() {
        // Get destination data from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const destinationId = urlParams.get('id') || 'dal-lake';
        
        // Destination data (in real app, this would come from an API)
        const destinations = {
          'dal-lake': {
            title: 'Dal Lake',
            subtitle: 'The Jewel of Srinagar',
            description: 'Dal Lake is the jewel of Srinagar and the second largest lake in Jammu & Kashmir. It is integral to tourism and recreation in Kashmir and is named the "Jewel in the crown of Kashmir" or "Srinagar\'s Jewel". The lake is famous for its houseboats, shikaras, and floating gardens.',
            location: 'Srinagar, Jammu & Kashmir',
            bestTime: 'April to October',
            weather: 'Temperate: 5°C to 30°C',
            rating: '4.8/5 (2,500+ reviews)',
            images: ['../assets/img/bg1.jpg', '../assets/img/bg1.jpg', '../assets/img/bg1.jpg']
          },
          'pangong-lake': {
            title: 'Pangong Lake',
            subtitle: 'The Color-changing Marvel',
            description: 'Pangong Lake is a breathtaking high-altitude lake situated at 14,270 ft. Famous for its changing colors from blue to green to red, it stretches from India to China across the disputed border.',
            location: 'Changthang, Ladakh',
            bestTime: 'May to September',
            weather: 'Cold: -10°C to 15°C',
            rating: '4.9/5 (1,800+ reviews)',
            images: ['../assets/img/bg1.jpg', '../assets/img/bg1.jpg', '../assets/img/bg1.jpg']
          },
          'gulmarg': {
            title: 'Gulmarg',
            subtitle: 'The Meadow of Flowers',
            description: 'Gulmarg is a world-class ski resort with the Gondola, one of the highest cable cars in the world. Perfect for skiing, snowboarding, and enjoying panoramic views of the Himalayas.',
            location: 'Baramulla, Jammu & Kashmir',
            bestTime: 'December to March (Winter), April to June (Summer)',
            weather: 'Cold: -5°C to 25°C',
            rating: '4.7/5 (3,200+ reviews)',
            images: ['../assets/img/bg1.jpg', '../assets/img/bg1.jpg', '../assets/img/bg1.jpg']
          }
        };
        
        // Set destination data
        const destination = destinations[destinationId] || destinations['dal-lake'];
        
        // Update page content
        document.getElementById('destination-title').textContent = destination.title;
        document.getElementById('destination-subtitle').textContent = destination.subtitle;
        document.getElementById('destination-description').textContent = destination.description;
        document.getElementById('location-detail').textContent = destination.location;
        document.getElementById('best-time').textContent = destination.bestTime;
        document.getElementById('weather-info').textContent = destination.weather;
        document.getElementById('rating-info').textContent = destination.rating;
        
        // Update hero background
        document.getElementById('destination-hero-bg').style.backgroundImage = 
          `linear-gradient(135deg, rgba(45, 46, 48, 0.103) 0%, rgba(29, 29, 30, 0.137) 100%), url('${destination.images[0]}')`;
        
        // Populate gallery
        const galleryContainer = document.getElementById('destination-gallery');
        galleryContainer.innerHTML = '';
        destination.images.forEach(image => {
          const img = document.createElement('img');
          img.src = image;
          img.alt = destination.title;
          img.loading = 'lazy';
          galleryContainer.appendChild(img);
        });
        
        // Update page title
        document.title = `${destination.title} - Explore Destination | Zubi Tours & Holiday`;
        
        // Booking form functionality
        const bookingForm = document.querySelector('.booking-form');
        const visitorsSelect = document.getElementById('visitors');
        const experienceSelect = document.getElementById('experience-type');
        const basePriceElement = document.querySelector('.price-item:nth-child(1) span:last-child');
        const totalPriceElement = document.querySelector('.price-total span:last-child');
        
        function updatePrice() {
          const basePrice = 2500;
          const visitors = parseInt(visitorsSelect.value);
          const experienceMultiplier = experienceSelect.value === 'premium' ? 1.5 : 
                                     experienceSelect.value === 'luxury' ? 2.5 : 1;
          
          const total = (basePrice * visitors * experienceMultiplier) + 300;
          basePriceElement.textContent = `₹${(basePrice * visitors * experienceMultiplier).toLocaleString()}`;
          totalPriceElement.textContent = `₹${total.toLocaleString()}`;
        }
        
        visitorsSelect.addEventListener('change', updatePrice);
        experienceSelect.addEventListener('change', updatePrice);
        
        bookingForm.addEventListener('submit', function(e) {
          e.preventDefault();
          alert('Booking request submitted! Our team will contact you shortly.');
        });
        
        // Book activity buttons
        document.querySelectorAll('.book-activity-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            const activity = this.closest('.activity-item').querySelector('h3').textContent;
            alert(`Booking request for "${activity}" has been added to your cart!`);
          });
        });
        
        // Package booking buttons
        document.querySelectorAll('.package-book-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            const packageName = this.closest('.package-card').querySelector('h3').textContent;
            alert(`Booking request for "${packageName}" has been added to your cart!`);
          });
        });
        
        // Contact button
        document.querySelector('.contact-btn').addEventListener('click', function() {
          window.location.href = './contact.html';
        });
        
        // View map button
        document.querySelector('.view-map-btn').addEventListener('click', function() {
          alert('Opening Google Maps with destination location...');
        });
        
        // Similar destinations links
        document.querySelectorAll('.similar-content a').forEach(link => {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            const destinationName = this.closest('.similar-card').querySelector('h4').textContent;
            alert(`Redirecting to ${destinationName} page...`);
            // In real implementation: window.location.href = `explore-destination.html?id=${destinationSlug}`;
          });
        });
        
        // Initialize price
        updatePrice();
        
        // Fade in animation for page elements
        setTimeout(() => {
          document.querySelectorAll('.destination-section, .booking-widget, .map-widget, .contact-widget, .similar-destinations').forEach((el, index) => {
            setTimeout(() => {
              el.style.opacity = '1';
              el.style.transform = 'translateY(0)';
            }, index * 100);
          });
        }, 300);
      });
    </script>
  </body>
</html>