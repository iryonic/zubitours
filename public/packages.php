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

    <title>Zubi tours & Holiday - Packages</title>
    
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

    <!-- Modern Hero Section -->
    <section class="modern-hero">
      <div class="hero-background"></div>
      <div class="hero-content">
        <h1>Curated Experiences</h1>
        <p>Discover our handcrafted tour packages for unforgettable journeys through Kashmir and Ladakh</p>
      </div>
    </section>

    <!-- Enhanced Filters -->
    <div class="enhanced-filters">
      <div class="filter-group">
        <label for="duration">Package Duration</label>
        <select id="duration" class="filter-select">
          <option value="all">Any Duration</option>
          <option value="3-5">3-5 Days</option>
          <option value="6-8">6-8 Days</option>
          <option value="9-12">9-12 Days</option>
          <option value="12+">12+ Days</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label for="type">Package Type</label>
        <select id="type" class="filter-select">
          <option value="all">All Types</option>
          <option value="cultural">Cultural</option>
          <option value="adventure">Adventure</option>
          <option value="luxury">Luxury</option>
          <option value="honeymoon">Honeymoon</option>
          <option value="family">Family</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label for="price">Price Range</label>
        <select id="price" class="filter-select">
          <option value="all">Any Price</option>
          <option value="budget">Budget (Under ₹20,000)</option>
          <option value="mid-range">Mid-Range (₹20,000-₹40,000)</option>
          <option value="premium">Premium (₹40,000+)</option>
        </select>
      </div>
      
      <button class="filter-btn" id="applyFilters">
        <i class="ri-filter-line"></i> Apply Filters
      </button>
    </div>

    <!-- Enhanced Packages Grid -->
    <section class="enhanced-grid" id="packages-grid">
      <!-- Package 1 -->
      <div class="enhanced-card" data-duration="6-8" data-type="cultural" data-price="mid-range">
        <div class="card-image">
          <img loading="lazy" src="../assets/img/bg1.jpg" alt="Kashmir Valley Explorer">
          <span class="card-badge">Bestseller</span>
        </div>
        <div class="card-content">
          <h3>Kashmir Valley Explorer</h3>
          <p>Complete Kashmir experience including Dal Lake, Gulmarg, Pahalgam, and Sonamarg with cultural immersion.</p>
          
          <div class="package-details">
            <div class="detail-item">
              <i class="ri-calendar-event-line"></i> 7 Days
            </div>
            <div class="detail-item">
              <i class="ri-user-line"></i> Max 6 People
            </div>
            <div class="detail-item">
              <i class="ri-hotel-bed-line"></i> 4 Star Hotels
            </div>
          </div>
          
          <div class="package-price">₹25,999 / person</div>
          <div class="price-note">All inclusive package with meals, accommodation and transport</div>
          
          <div class="card-meta">
            <div class="location">
              <i class="ri-map-pin-line"></i> Srinagar · Gulmarg · Pahalgam
            </div>
            <div class="rating">
              <i class="ri-star-fill"></i>
              <span>4.9</span>
            </div>
          </div>
          <div class="card-buttons">
            <a href="./package-details.html" class="card-button">Book Now</a>
            <a href="./package-details.html" class="card-button outline">View Details</a>
          </div>
        </div>
      </div>

      <!-- Package 2 -->
      <div class="enhanced-card" data-duration="9-12" data-type="adventure" data-price="premium">
        <div class="card-image">
          <img loading="lazy" src="../assets/img/bg1.jpg" alt="Ladakh Adventure">
          <span class="card-badge">Adventure</span>
        </div>
        <div class="card-content">
          <h3>Ladakh Adventure Expedition</h3>
          <p>Explore the majestic landscapes of Ladakh including Pangong Lake, Nubra Valley, and ancient monasteries.</p>
          
          <div class="package-details">
            <div class="detail-item">
              <i class="ri-calendar-event-line"></i> 10 Days
            </div>
            <div class="detail-item">
              <i class="ri-user-line"></i> Max 8 People
            </div>
            <div class="detail-item">
              <i class="ri-hotel-bed-line"></i> 3 Star Hotels
            </div>
          </div>
          
          <div class="package-price">₹32,499 / person</div>
          <div class="price-note">Includes permits, accommodation, and experienced guide</div>
          
          <div class="card-meta">
            <div class="location">
              <i class="ri-map-pin-line"></i> Leh · Nubra · Pangong
            </div>
            <div class="rating">
              <i class="ri-star-fill"></i>
              <span>4.8</span>
            </div>
          </div>
          <div class="card-buttons">
            <a href="./package-details.html" class="card-button">Book Now</a>
            <a href="./package-details.html" class="card-button outline">View Details</a>
          </div>
        </div>
      </div>

      <!-- Package 3 -->
      <div class="enhanced-card" data-duration="3-5" data-type="honeymoon" data-price="premium">
        <div class="card-image">
          <img loading="lazy" src="../assets/img/bg1.jpg" alt="Kashmir Honeymoon">
          <span class="card-badge">Romantic</span>
        </div>
        <div class="card-content">
          <h3>Kashmir Honeymoon Special</h3>
          <p>Romantic getaway for couples with luxury houseboat stay, shikara ride, and private sightseeing.</p>
          
          <div class="package-details">
            <div class="detail-item">
              <i class="ri-calendar-event-line"></i> 5 Days
            </div>
            <div class="detail-item">
              <i class="ri-user-line"></i> Couples Only
            </div>
            <div class="detail-item">
              <i class="ri-hotel-bed-line"></i> 5 Star Hotels
            </div>
          </div>
          
          <div class="package-price">₹38,999 / couple</div>
          <div class="price-note">Special romantic experiences included</div>
          
          <div class="card-meta">
            <div class="location">
              <i class="ri-map-pin-line"></i> Srinagar · Gulmarg
            </div>
            <div class="rating">
              <i class="ri-star-fill"></i>
              <span>4.9</span>
            </div>
          </div>
          <div class="card-buttons">
            <a href="./package-details.html" class="card-button">Book Now</a>
            <a href="./package-details.html" class="card-button outline">View Details</a>
          </div>
        </div>
      </div>

      <!-- Package 4 -->
      <div class="enhanced-card" data-duration="6-8" data-type="family" data-price="mid-range">
        <div class="card-image">
          <img loading="lazy" src="../assets/img/bg1.jpg" alt="Family Kashmir Tour">
          <span class="card-badge">Family Friendly</span>
        </div>
        <div class="card-content">
          <h3>Family Kashmir Escape</h3>
          <p>Perfect family vacation with child-friendly activities, comfortable stays, and hassle-free itinerary.</p>
          
          <div class="package-details">
            <div class="detail-item">
              <i class="ri-calendar-event-line"></i> 6 Days
            </div>
            <div class="detail-item">
              <i class="ri-user-line"></i> Family Size
            </div>
            <div class="detail-item">
              <i class="ri-hotel-bed-line"></i> 4 Star Hotels
            </div>
          </div>
          
          <div class="package-price">₹22,499 / person</div>
          <div class="price-note">Children under 12 get 30% discount</div>
          
          <div class="card-meta">
            <div class="location">
              <i class="ri-map-pin-line"></i> Srinagar · Pahalgam
            </div>
            <div class="rating">
              <i class="ri-star-fill"></i>
              <span>4.7</span>
            </div>
          </div>
          <div class="card-buttons">
            <a href="./package-details.html" class="card-button">Book Now</a>
            <a href="./package-details.html" class="card-button outline">View Details</a>
          </div>
        </div>
      </div>

      <!-- Package 5 -->
      <div class="enhanced-card" data-duration="9-12" data-type="adventure" data-price="mid-range">
        <div class="card-image">
          <img loading="lazy" src="../assets/img/bg1.jpg" alt="Complete Ladakh">
          <span class="card-badge">Comprehensive</span>
        </div>
        <div class="card-content">
          <h3>Complete Ladakh Experience</h3>
          <p>Comprehensive Ladakh tour covering all major attractions with comfortable pacing for high altitude.</p>
          
          <div class="package-details">
            <div class="detail-item">
              <i class="ri-calendar-event-line"></i> 11 Days
            </div>
            <div class="detail-item">
              <i class="ri-user-line"></i> Max 10 People
            </div>
            <div class="detail-item">
              <i class="ri-hotel-bed-line"></i> 3 Star Hotels
            </div>
          </div>
          
          <div class="package-price">₹35,999 / person</div>
          <div class="price-note">Includes oxygen cylinders and medical support</div>
          
          <div class="card-meta">
            <div class="location">
              <i class="ri-map-pin-line"></i> Leh · Nubra · Pangong · Tso Moriri
            </div>
            <div class="rating">
              <i class="ri-star-fill"></i>
              <span>4.9</span>
            </div>
          </div>
          <div class="card-buttons">
            <a href="./package-details.html" class="card-button">Book Now</a>
            <a href="./package-details.html" class="card-button outline">View Details</a>
          </div>
        </div>
      </div>

      <!-- Package 6 -->
      <div class="enhanced-card" data-duration="3-5" data-type="luxury" data-price="premium">
        <div class="card-image">
          <img loading="lazy" src="../assets/img/bg1.jpg" alt="Luxury Houseboat Stay">
          <span class="card-badge">Luxury</span>
        </div>
        <div class="card-content">
          <h3>Premium Houseboat Experience</h3>
          <p>Luxury houseboat stay with personal chef, private shikara, and exclusive cultural experiences.</p>
          
          <div class="package-details">
            <div class="detail-item">
              <i class="ri-calendar-event-line"></i> 4 Days
            </div>
            <div class="detail-item">
              <i class="ri-user-line"></i> Private Tour
            </div>
            <div class="detail-item">
              <i class="ri-hotel-bed-line"></i> 5 Star Houseboat
            </div>
          </div>
          
          <div class="package-price">₹45,999 / couple</div>
          <div class="price-note">All meals included with personal chef</div>
          
          <div class="card-meta">
            <div class="location">
              <i class="ri-map-pin-line"></i> Dal Lake, Srinagar
            </div>
            <div class="rating">
              <i class="ri-star-fill"></i>
              <span>5.0</span>
            </div>
          </div>
          <div class="card-buttons">
            <a href="./package-details.html" class="card-button">Book Now</a>
            <a href="./package-details.html" class="card-button outline">View Details</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
      <div class="section-header">
        <h2>Guest Experiences</h2>
        <p>What our travelers say about our packages</p>
      </div>
      
      <div class="testimonial-cards">
        <div class="testimonial-card">
          <div class="testimonial-text">
            "The Kashmir Valley Explorer package exceeded our expectations. Every detail was perfectly arranged, and our guide made the experience truly special."
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">
              <img loading="lazy" src="../assets/img/bg1.jpg" alt="Rajesh Kumar">
            </div>
            <div class="author-details">
              <h4>Rajesh Kumar</h4>
              <p>Kashmir Valley Explorer</p>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-text">
            "Our Ladakh adventure was the trip of a lifetime! The itinerary was perfectly paced, and the team took care of everything from permits to accommodations."
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">
              <img loading="lazy" src="../assets/img/bg1.jpg" alt="Priya Singh">
            </div>
            <div class="author-details">
              <h4>Priya Singh</h4>
              <p>Ladakh Adventure Expedition</p>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-text">
            "The honeymoon package was absolutely romantic. The houseboat stay was magical, and the private shikara ride at sunset was unforgettable."
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">
              <img loading="lazy" src="../assets/img/bg1.jpg" alt>Amit &amp; Sunita</p>
            </div>
            <div class="author-details">
              <h4>Amit &amp; Sunita</h4>
              <p>Kashmir Honeymoon Special</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-content">
        <h2>Ready for Your Adventure?</h2>
        <p>Contact us to customize a package that perfectly matches your travel dreams</p>
        <div class="cta-buttons">
          <a href="./contact.html" class="cta-button primary">Get In Touch</a>
          <a href="./destinations.html" class="cta-button secondary">Explore Destinations</a>
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


      // Enhanced Filter Functionality for Packages
      document.addEventListener('DOMContentLoaded', function() {
        const durationFilter = document.getElementById('duration');
        const typeFilter = document.getElementById('type');
        const priceFilter = document.getElementById('price');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const packageCards = document.querySelectorAll('.enhanced-card');
        
        function filterPackages() {
          const durationValue = durationFilter.value;
          const typeValue = typeFilter.value;
          const priceValue = priceFilter.value;
          
          packageCards.forEach(card => {
            const cardDuration = card.getAttribute('data-duration');
            const cardType = card.getAttribute('data-type');
            const cardPrice = card.getAttribute('data-price');
            
            const durationMatch = durationValue === 'all' || durationValue === cardDuration;
            const typeMatch = typeValue === 'all' || typeValue === cardType;
            const priceMatch = priceValue === 'all' || priceValue === cardPrice;
            
            if (durationMatch && typeMatch && priceMatch) {
              card.style.display = 'block';
              setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
              }, 50);
            } else {
              card.style.opacity = '0';
              card.style.transform = 'translateY(20px)';
              setTimeout(() => {
                card.style.display = 'none';
              }, 300);
            }
          });
        }
        
        applyFiltersBtn.addEventListener('click', filterPackages);
        
        // Initialize animations
        setTimeout(() => {
          packageCards.forEach((card, index) => {
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, index * 100);
          });
        }, 500);
      });

      //no results found
      const noResultsMessage = document.createElement('div');
      noResultsMessage.classList.add('no-results');
      noResultsMessage.textContent = 'No results found';
      document.querySelector('.packages-container').appendChild(noResultsMessage);
      

    </script>
  </body>
</html>