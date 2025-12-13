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
    <!-- Masonry layout CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />

    <title>Zubi tours & Holiday - Gallery</title>
 
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
    <section class="gallery-hero">
      <div class="section-header">
        <h2>Our Gallery </h2>
        <p>Showcasing the beauty of Kashmir and Ladakh</p>
      </div>
      
      <div class="gallery-filters">
        <button class="filter-btn active" data-filter="all">All Photos</button>
        <button class="filter-btn" data-filter="kashmir">Kashmir</button>
        <button class="filter-btn" data-filter="ladakh">Ladakh</button>
        <button class="filter-btn" data-filter="lakes">Lakes</button>
        <button class="filter-btn" data-filter="mountains">Mountains</button>
        <button class="filter-btn" data-filter="culture">Culture</button>
        <button class="filter-btn" data-filter="adventure">Adventure</button>
      </div>
    </section>

    <!-- Masonry Gallery -->
    <section class="masonry-gallery-section">
      <div class="masonry-grid">
        <!-- Kashmir Images -->
        <div class="masonry-item" data-category="kashmir mountains">
          
          <a href="../assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Dal Lake - Srinagar, Kashmir">
            <img loading="lazy" src="../assets/img/bg3.jpg" alt="Dal Lake">
            <div class="image-overlay">
              <h3>Dal Lake</h3>
              <p>Srinagar, Kashmir</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="kashmir adventure">
         
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Gulmarg Ski Resort - Kashmir">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Gulmarg Ski Resort">
            <div class="image-overlay">
              <h3>Gulmarg Ski Resort</h3>
              <p>Kashmir</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="kashmir culture">
          
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Traditional Houseboat - Dal Lake">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Traditional Houseboat">
            <div class="image-overlay">
              <h3>Traditional Houseboat</h3>
              <p>Dal Lake, Kashmir</p>
            </div>
          </a>
        </div>
        
        <!-- Ladakh Images -->
        <div class="masonry-item" data-category="ladakh lakes">
          
          <a href="../assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Pangong Lake - Ladakh">
            <img loading="lazy" src="../assets/img/bg3.jpg" alt="Pangong Lake">
            <div class="image-overlay">
              <h3>Pangong Lake</h3>
              <p>Ladakh</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="ladakh mountains">
          
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Nubra Valley - Ladakh">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Nubra Valley">
            <div class="image-overlay">
              <h3>Nubra Valley</h3>
              <p>Ladakh</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="ladakh culture">
        
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Hemis Monastery - Ladakh">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Hemis Monastery">
            <div class="image-overlay">
              <h3>Hemis Monastery</h3>
              <p>Ladakh</p>
            </div>
          </a>
        </div>
        
        <!-- More Kashmir Images -->
        <div class="masonry-item" data-category="kashmir lakes">
          
          <a href="../assets/img/bg2.jpg" data-fancybox="gallery" data-caption="Manasbal Lake - Kashmir">
            <img loading="lazy" src="../assets/img/bg2.jpg" alt="Manasbal Lake">
            <div class="image-overlay">
              <h3>Manasbal Lake</h3>
              <p>Kashmir</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="kashmir mountains">
          
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Tulip Garden - Srinagar">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Tulip Garden">
            <div class="image-overlay">
              <h3>Tulip Garden</h3>
              <p>Srinagar, Kashmir</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="kashmir adventure">
          
          <a href="../assets/img/bg2.jpg" data-fancybox="gallery" data-caption="Sonamarg - The Meadow of Gold">
            <img loading="lazy" src="../assets/img/bg2.jpg" alt="Sonamarg">
            <div class="image-overlay">
              <h3>Sonamarg</h3>
              <p>Kashmir</p>
            </div>
          </a>
        </div>
        
        <!-- More Ladakh Images -->
        <div class="masonry-item" data-category="ladakh adventure">
        
          <a href="../assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Magnetic Hill - Ladakh">
            <img loading="lazy" src="../assets/img/bg3.jpg" alt="Magnetic Hill">
            <div class="image-overlay">
              <h3>Magnetic Hill</h3>
              <p>Ladakh</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="ladakh culture">
          
          <a href="../assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Local Market - Leh">
            <img loading="lazy" src="../assets/img/bg3.jpg" alt="Local Market">
            <div class="image-overlay">
              <h3>Local Market</h3>
              <p>Leh, Ladakh</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="ladakh mountains">
          
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Khardung La Pass - Ladakh">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Khardung La Pass">
            <div class="image-overlay">
              <h3>Khardung La Pass</h3>
              <p>Ladakh</p>
            </div>
          </a>
        </div>
        
        <!-- Additional Images -->
        <div class="masonry-item" data-category="kashmir culture">
          
          <a href="../assets/img/bg2.jpg" data-fancybox="gallery" data-caption="Shikara Ride - Dal Lake">
            <img loading="lazy" src="../assets/img/bg2.jpg" alt="Shikara Ride">
            <div class="image-overlay">
              <h3>Shikara Ride</h3>
              <p>Dal Lake, Kashmir</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="kashmir mountains">
          
          <a href="../assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Snow-covered Peaks - Gulmarg">
            <img loading="lazy" src="../assets/img/bg3.jpg" alt="Snow-covered Peaks">
            <div class="image-overlay">
              <h3>Snow-covered Peaks</h3>
              <p>Gulmarg, Kashmir</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="ladakh adventure">
          
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Biking Expedition - Ladakh">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Biking Expedition">
            <div class="image-overlay">
              <h3>Biking Expedition</h3>
              <p>Ladakh</p>
            </div>
          </a>
        </div>
        
        <div class="masonry-item" data-category="kashmir lakes">
          
          <a href="../assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Sunset at Dal Lake">
            <img loading="lazy" src="../assets/img/bg1.jpg" alt="Sunset at Dal Lake">
            <div class="image-overlay">
              <h3>Sunset at Dal Lake</h3>
              <p>Kashmir</p>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="gallery-cta">
      <h2>Experience the Beauty Yourself</h2>
      <p>Let us help you create unforgettable memories in the paradise of Kashmir and the majestic landscapes of Ladakh</p>
      <a href="./packages.html" class="cta-button">Explore Our Packages</a>
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
    <!-- jQuery and Fancybox for gallery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    
    <script>
      // Filter functionality
      document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.masonry-item');
        
        filterButtons.forEach(button => {
          button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            galleryItems.forEach(item => {
              if (filterValue === 'all') {
                item.style.display = 'block';
              } else {
                const categories = item.getAttribute('data-category').split(' ');
                if (categories.includes(filterValue)) {
                  item.style.display = 'block';
                } else {
                  item.style.display = 'none';
                }
              }
            });
          });
        });
        
        // Initialize fancybox
        $('[data-fancybox]').fancybox({
          buttons: [
            "slideShow",
            "thumbs",
            "zoom",
            "fullScreen",
            "share",
            "close"
          ],
          loop: true,
          protect: true,
          animationEffect: "zoom",
          transitionEffect: "circular",
          transitionDuration: 800
        });
        
        // Add subtle animation to gallery items on load
        setTimeout(() => {
          galleryItems.forEach((item, index) => {
            setTimeout(() => {
              item.style.opacity = 1;
              item.style.transform = 'translateY(0)';
            }, index * 100);
          });
        }, 500);
      });
    </script>
  
  </body>
</html>