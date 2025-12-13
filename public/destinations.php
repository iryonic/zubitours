<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!--=============== REMIXICONS ===============-->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!--=============== CSS ===============-->
  <link rel="stylesheet" href="../assets/css/styles.css" />

  <title>Zubi tours & Holiday - Destinations</title>

</head>

<body>
  <!-- Loader
<div id="loader">
  <div class="travel-loader">
    <span class="path"></span>
    <i class="ri-flight-takeoff-line plane"></i>
  </div>
  <h2 class="brand-name">Zubi Tours & Holiday</h2>
</div> -->


  <!--==================== HEADER ====================-->
  <?php include '../admin/includes/navbar.php'; ?>

  <!-- Modern Hero Section -->
  <section class="modern-hero">
    <div class="hero-background"></div>
    <div class="hero-content">
      <h1>Discover Paradise</h1>
      <p>Explore the breathtaking destinations of Kashmir and Ladakh</p>
    </div>
  </section>

  <!-- Enhanced Filters -->
  <div class="enhanced-filters">
    <div class="filter-group">
      <label for="region">Region</label>
      <select id="region" class="filter-select">
        <option value="all">All Regions</option>
        <option value="kashmir">Kashmir</option>
        <option value="ladakh">Ladakh</option>
      </select>
    </div>

    <div class="filter-group">
      <label for="type">Destination Type</label>
      <select id="type" class="filter-select">
        <option value="all">All Types</option>
        <option value="lake">Lake</option>
        <option value="valley">Valley</option>
        <option value="mountain">Mountain</option>
        <option value="monastery">Monastery</option>
        <option value="hill">Hill Station</option>
      </select>
    </div>

    <div class="filter-group">
      <label for="season">Best Season</label>
      <select id="season" class="filter-select">
        <option value="all">All Seasons</option>
        <option value="spring">Spring</option>
        <option value="summer">Summer</option>
        <option value="autumn">Autumn</option>
        <option value="winter">Winter</option>
      </select>
    </div>

    <button class="filter-btn" id="applyFilters">Apply Filters</button>
  </div>
  <?php
  include '../admin/includes/connection.php';
  $sql = "SELECT * FROM destinations ORDER BY id DESC LIMIT 3";
  $query = mysqli_query($conn, $sql);

  if (mysqli_num_rows($query) > 0) {
  ?>

    <!-- Enhanced Destinations Grid -->
    <section class="enhanced-grid" id="destinations-grid">
      <!-- Destination 1 -->
      <?php while ($row = mysqli_fetch_assoc($query)) { ?>
        <div class="enhanced-card" data-region="<?php echo $row['region']; ?>" data-type="<?php echo $row['category']; ?>" data-season="summer">
          <div class="card-image">
            <img loading="lazy" src="../admin/upload/destinations/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <span class="card-badge"><?php echo $row['region']; ?></span>
          </div>
          <div class="card-content">
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo substr($row['description'], 0, 100); ?></p>
            <div class="card-meta">
              <div class="location">
                <i class="ri-map-pin-line"></i> <?php echo $row['location']; ?>
              </div>
              <div class="rating">
                <i class="ri-star-fill"></i>
                <span><?php echo $row['rating']; ?></span>
              </div>
            </div>
            <a href="#" class="card-button">Explore Destination</a>
          </div>
        </div>
      <?php } ?>
    </section>
  <?php } ?>
  <!-- View All Button -->
  <div class="view-all-container">
    <a href="./packages.html" class="view-all-btn">View All Destinations</a>
  </div>


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
    // Enhanced Filter Functionality
    document.addEventListener('DOMContentLoaded', function() {
      const regionFilter = document.getElementById('region');
      const typeFilter = document.getElementById('type');
      const seasonFilter = document.getElementById('season');
      const applyFiltersBtn = document.getElementById('applyFilters');
      const destinationCards = document.querySelectorAll('.enhanced-card');

      function filterDestinations() {
        const regionValue = regionFilter.value;
        const typeValue = typeFilter.value;
        const seasonValue = seasonFilter.value;

        destinationCards.forEach(card => {
          const cardRegion = card.getAttribute('data-region');
          const cardType = card.getAttribute('data-type');
          const cardSeason = card.getAttribute('data-season');

          const regionMatch = regionValue === 'all' || regionValue === cardRegion;
          const typeMatch = typeValue === 'all' || typeValue === cardType;
          const seasonMatch = seasonValue === 'all' || seasonValue === cardSeason;

          if (regionMatch && typeMatch && seasonMatch) {
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

      applyFiltersBtn.addEventListener('click', filterDestinations);

      // Initialize animations
      setTimeout(() => {
        destinationCards.forEach((card, index) => {
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, index * 100);
        });
      }, 500);
    });
  </script>
</body>

</html>