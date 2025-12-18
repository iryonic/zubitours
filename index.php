<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="googlebot" content="index, follow">

<meta name="language" content="English">
<meta name="geo.region" content="IN-JK">
<meta name="geo.placename" content="Kashmir, Srinagar">
<meta name="distribution" content="global">
<meta name="rating" content="general">
<meta name="revisit-after" content="7 days">

<meta name="author" content="Zubi Tours & Holidays">
<meta name="copyright" content="Zubi Tours & Holidays">

<meta property="og:site_name" content="Zubi Tours & Holidays">
<meta property="og:locale" content="en_IN">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@zubitours">

<meta name="description" content="Zubi Tours & Holidays is a leading Kashmir travel agency offering affordable Kashmir tour packages, honeymoon trips, family tours, hotel bookings & cab services.">



<!-- Meta tags for SEO optimization -->
 <meta name="keywords" content="
Kashmir tour packages,
Kashmir travel agency,
best Kashmir tour operator,
Srinagar travel agency,
Kashmir holiday packages,
Kashmir honeymoon tour,
Kashmir family tour,
Kashmir trip planner,
Jammu Kashmir tourism,
Kashmir sightseeing tour,
Kashmir cab service,
Kashmir tour company
">

  <!--=============== FAVICON ===============-->
  <link rel="icon" href="./assets/img/zubilogo.jpg" type="image/jpg" />


  <!--=============== REMIXICONS ===============-->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!--=============== CSS ===============-->
  <link rel="stylesheet" href="./assets/css/styles.css" />

 <title>Kashmir Tour Packages | Best Travel Agency in Srinagar – Zubi Tours</title>

  <style>
    /* Improved Hero Section */
    .modern-hero {
      position: relative;
      height: 100vh;
      min-height: 700px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
      overflow: hidden;
    }

    .hero-img-background {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: contain;
      z-index: -2;
      overflow: hidden;
    }

    .hero-img-background img{
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg,
          rgba(201, 47, 47, 0.06) 0%,
          rgba(73, 73, 188, 0.16) 100%);
      z-index: -1;
    }

    .hero-content {
      max-width: 900px;
      padding: 0 20px;
      z-index: 1;
      animation: fadeInUp 1s ease-out;
    }

    .hero-subtitle {
      font-size: 1.3rem;
      font-weight: 500;
      margin-bottom: 15px;
      color: rgba(255, 255, 255, 0.9);
      display: block;
      text-transform: uppercase;
      letter-spacing: 3px;
    }

    .modern-hero h1 {
      font-size: 4rem;
      margin-bottom: 20px;
      font-weight: 800;
      line-height: 1.2;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
    }

    .modern-hero p {
      font-size: 1.4rem;
      margin-bottom: 40px;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
    }

    /* Enhanced Search Box */
    .search-box-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      max-width: 1000px;
      margin: 0 auto;
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: fadeIn 1.5s ease-out;
    }

    .search-box {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      background: transparent;
      box-shadow: none;
      padding: 0;
      width: 100%;
    }

    .search-box div {
      display: flex;
      flex-direction: column;
    }

    .search-box label {
      color: white;
      font-weight: 600;
      margin-bottom: 10px;
      text-align: left;
      font-size: 1.1rem;
    }

    .search-box select,
    .search-box input[type="date"] {
      padding: 16px 20px;
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }

    .search-box select:focus,
    .search-box input[type="date"]:focus {
      border-color: white;
      background: rgba(255, 255, 255, 0.15);
      outline: none;
      box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
    }

    .search-box option {
      background: rgb(250, 250, 250);
      color: rgb(30, 53, 146);
    }

    .srch-btn {
      background: var(--first-color);
      color: white;
      padding: 16px 30px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.1rem;
      margin-top: 25px;
      transition: all 0.3s ease;
      grid-column: 1 / -1;
      justify-self: center;
      width: 200px;
    }

    .srch-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .scroll-indicator {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      color: white;
      font-size: 1.2rem;
      animation: bounce 2s infinite;
    }

    .scroll-indicator i {
      margin-top: 10px;
      display: block;
      font-size: 1.8rem;
    }

    /* Improved Destinations Section */
    .destinations-section {
      padding: 100px 20px;
      background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);
      position: relative;
    }

    .section-header {
      text-align: center;
      margin-bottom: 70px;
    }

    .section-subtitle {
      display: block;
      font-size: 1.2rem;
      color: var(--first-color);
      font-weight: 600;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .section-title {
      font-size: 3rem;
      color: var(--title-color);
      margin-bottom: 20px;
      position: relative;
      display: inline-block;
    }

    .section-title:after {
      content: "";
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 5px;
      background: var(--first-color);
      border-radius: 3px;
    }

    .section-description {
      font-size: 1.2rem;
      color: var(--text-color);
      max-width: 700px;
      margin: 30px auto 0;
      line-height: 1.6;
    }

    .destinations-container {
      max-width: 1400px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 30px;
    }

    .destination-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s ease;
      position: relative;
    }

    .destination-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .card-image {
      height: 280px;
      overflow: hidden;
      position: relative;
    }

    .card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .destination-card:hover .card-image img {
      transform: scale(1.1);
    }

    .card-badge {
      position: absolute;
      top: 20px;
      right: 20px;
      background: var(--first-color);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      z-index: 2;
    }

    .card-content {
      padding: 25px;
    }

    .card-content h3 {
      font-size: 1.6rem;
      margin-bottom: 15px;
      color: var(--title-color);
    }

    .card-content p {
      color: var(--text-color);
      margin-bottom: 20px;
      line-height: 1.6;
    }

    .card-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      padding-top: 20px;
      border-top: 1px solid #f1f1f1;
    }

    .location {
      display: flex;
      align-items: center;
      color: var(--first-color);
      font-weight: 600;
      font-size: 1.1rem;
    }

    .location i {
      margin-right: 8px;
    }

    .rating {
      display: flex;
      align-items: center;
      color: #f59e0b;
      font-weight: 600;
    }

    .rating i {
      margin-right: 5px;
    }

    .card-button {
      display: block;
      width: 100%;
      padding: 15px;
      background: var(--first-color);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
      text-decoration: none;
      font-size: 1.1rem;
    }

    .card-button:hover {
      transform: translateY(-3px);
    }

    .view-all-container {
      text-align: center;
      margin-top: 60px;
    }

    .view-all-btn {
      display: inline-block;
      padding: 18px 40px;
      background: var(--first-color);
      color: white;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(42, 61, 232, 0.3);
      font-size: 1.1rem;
    }

    .view-all-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(42, 61, 232, 0.4);
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes bounce {
      0%,
      20%,
      50%,
      80%,
      100% {
        transform: translateY(0) translateX(-50%);
      }

      40% {
        transform: translateY(-20px) translateX(-50%);
      }

      60% {
        transform: translateY(-10px) translateX(-50%);
      }
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
      .modern-hero h1 {
        font-size: 3.5rem;
      }

      .destinations-container {
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      }
    }

    @media (max-width: 992px) {
      .modern-hero h1 {
        font-size: 3rem;
      }

      .modern-hero p {
        font-size: 1.2rem;
      }

      .section-title {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 768px) {
      .modern-hero {
        height: auto;
        min-height: 100vh;
        padding: 120px 0 80px;
      }

      .modern-hero h1 {
        font-size: 2.5rem;
      }

      .hero-subtitle {
        font-size: 1.1rem;
      }

      .search-box {
        grid-template-columns: 1fr;
      }

      .destinations-container {
        grid-template-columns: 1fr;
      }

      .section-title {
        font-size: 2.2rem;
      }

      .card-image {
        height: 250px;
      }
    }

    @media (max-width: 576px) {
      .modern-hero h1 {
        font-size: 2.2rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .search-box-container {
        padding: 20px;
      }

      .card-content {
        padding: 20px;
      }

      .destination-card {
        border-radius: 15px;
      }
    }
  </style>
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
  <header class="header" id="header">
    <nav class="nav nav-container">
      <a href="#" class="nav__logo"><img src="./assets/img/zubilogo.jpg" alt="zubilogo" id="logo"></a>

      <div class="nav__menu" id="nav-menu">
        <ul class="nav__list">
          <li class="nav__item">
            <a href="index.php" class="nav__link active">Home</a>
          </li>

          <li class="nav__item">
            <a href="./public/about.php" class="nav__link">About Us</a>
          </li>

          <li class="nav__item">
            <a href="./public/destinations.php" class="nav__link">Destinations</a>
          </li>

          <li class="nav__item">
            <a href="./public/packages.php" class="nav__link">Packages</a>
          </li>

          <li class="nav__item">
            <a href="./public/gallery.php" class="nav__link">Gallery</a>
          </li>

          <li class="nav__item">
            <a href="./public/car-rentals.php" class="nav__link">Car Rentals</a>
          </li>

          <li class="nav__item">
            <a href="./public/contact.php" class="nav__link">Contact Us</a>
          </li>
        </ul>

        <!-- Close button -->
        <div class="nav__close" id="nav-close">
          <i class="ri-close-line"></i>
        </div>
      </div>

      <div class="nav__actions">
        <!-- Toggle button -->
        <div class="nav__toggle" id="nav-toggle">
          <i class="ri-menu-line"></i>
        </div>
      </div>
    </nav>
  </header>

  <!--==================== MAIN ====================-->
  <main class="main">
    <?php
    // Include database connection
    include './admin/includes/connection.php';
    
    // Fetch hero section data
    $hero_query = mysqli_query($conn, "SELECT * FROM homepage_sections WHERE section_name = 'hero'");
    $hero = mysqli_fetch_assoc($hero_query);
    
    // Default values if not set
    $hero_subtitle = $hero['subtitle'] ?? 'Welcome to Paradise';
    $hero_title = $hero['title'] ?? 'Discover Kashmir & Ladakh';
    $hero_description = $hero['description'] ?? 'Experience the breathtaking landscapes, rich culture, and adventure activities in these stunning regions of India.';
    $hero_background = !empty($hero['background_image']) ? './admin/upload/' . $hero['background_image'] : './assets/img/dalbg.jpg';
    ?>
    
    <!-- Improved Hero Section -->
    <section class="modern-hero">
      <div class="hero-img-background">
        <img src="<?php echo $hero_background; ?>" alt="Hero Background" id="banner"/>
      </div>
      <div class="hero-overlay"></div>

      <div class="hero-content">
        <span class="hero-subtitle"><?php echo htmlspecialchars($hero_subtitle); ?></span>
        <h1><?php echo htmlspecialchars($hero_title); ?></h1>
        <p><?php echo htmlspecialchars($hero_description); ?></p>

        <div class="search-box-container">
          <form class="search-box" id="tourForm" action="public/packages.php" method="get">
            <div>
              <label for="destination">Destination</label>
              <input id="destination" name="search" type="search" placeholder="Type destination or select..." list="dest-list" style="padding:16px 20px; border-radius:12px; border:2px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.05); color: white; font-size:1.05rem;" />
              <datalist id="dest-list"></datalist>
            </div>

            <div>
              <label for="checkin">Check In Date</label>
              <input type="date" id="checkin" name="checkin" />
            </div>

            <div>
              <label for="duration">Duration</label>
              <select id="duration" name="duration">
                <option value="all">Any Duration</option>
                <option value="3-5">3-5 Days</option>
                <option value="6-8">6-8 Days</option>
                <option value="9-12">9-12 Days</option>
                <option value="12+">12+ Days</option>
              </select>
            </div>

            <div>
              <label for="travelers">Travelers</label>
              <select id="travelers" name="travelers">
                <option value="1">1 Traveler</option>
                <option value="2" selected>2 Travelers</option>
                <option value="3">3 Travelers</option>
                <option value="4">4 Travelers</option>
                <option value="5+">5+ Travelers</option>
              </select>
            </div>

            <button type="submit" class="srch-btn">Search Tours</button>
          </form>
        </div>
      </div>

      <div class="scroll-indicator">
        Scroll to explore
        <i class="ri-arrow-down-line"></i>
      </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const params = new URLSearchParams(window.location.search);
        const form = document.getElementById('tourForm');
        if (!form) return;
        const search = params.get('search');
        const duration = params.get('duration');
        const checkin = params.get('checkin');
        const travelers = params.get('travelers');

        if (search) {
            const sel = document.getElementById('destination');
            if (sel) {
                const opt = Array.from(sel.options).find(o => o.value.toLowerCase() === search.toLowerCase());
                if (opt) sel.value = opt.value;
                else {
                    const newOpt = document.createElement('option');
                    newOpt.value = search;
                    newOpt.text = search;
                    sel.appendChild(newOpt);
                    sel.value = search;
                }
            }
        }
        if (duration && document.getElementById('duration')) document.getElementById('duration').value = duration;
        if (checkin && document.getElementById('checkin')) document.getElementById('checkin').value = checkin;
        if (travelers && document.getElementById('travelers')) document.getElementById('travelers').value = travelers;

        // Suggestion autocomplete (debounced)
        const destInput = document.getElementById('destination');
        const datalist = document.getElementById('dest-list');
        function debounce(fn, wait=200){let t; return (...a)=>{clearTimeout(t); t=setTimeout(()=>fn.apply(this,a), wait);}};
        async function fetchSuggestions(q){
            if (!q || q.trim().length < 2) {
                datalist.innerHTML = '';
                return;
            }
            try{
                const resp = await fetch('/admin/logic/search_destinations.php?q=' + encodeURIComponent(q));
                if (!resp.ok) return;
                const items = await resp.json();
                datalist.innerHTML = items.map(it => `<option value="${it.destination_name} - ${it.location}"></option>`).join('');
            } catch(e){ console.error(e); }
        }
        if (destInput) destInput.addEventListener('input', debounce(e => fetchSuggestions(e.target.value), 200));
    });
    </script>
    
    <?php
    // Fetch destinations section data
    $destinations_section_query = mysqli_query($conn, "SELECT * FROM homepage_sections WHERE section_name = 'destinations'");
    $destinations_section = mysqli_fetch_assoc($destinations_section_query);
    
    $destinations_subtitle = $destinations_section['subtitle'] ?? 'Explore';
    $destinations_title = $destinations_section['title'] ?? 'Popular Destinations';
    $destinations_description = $destinations_section['description'] ?? 'Discover the most breathtaking locations in Kashmir and Ladakh that will leave you with unforgettable memories.';
    
    // Fetch destinations from database (include primary image if present)
    $destinations_query = mysqli_query($conn, "SELECT d.*, di.image_path FROM destinations d LEFT JOIN destination_images di ON d.id = di.destination_id AND di.is_primary = 1 ORDER BY d.created_at DESC LIMIT 3");
    ?>
    
    <!-- Improved Destinations Section -->
    <section class="destinations-section">
      <div class="section-header">
        <span class="section-subtitle"><?php echo htmlspecialchars($destinations_subtitle); ?></span>
        <h2 class="section-title"><?php echo htmlspecialchars($destinations_title); ?></h2>
        <p class="section-description">
          <?php echo htmlspecialchars($destinations_description); ?>
        </p>
      </div>

      <?php if (mysqli_num_rows($destinations_query) > 0): ?>
        <div class="destinations-container">
          <?php while ($destination = mysqli_fetch_assoc($destinations_query)): 
              // Defensive fields and fallbacks
              $image_path = $destination['image_path'] ?? '';
              $image_src = $image_path ? './admin/upload/' . htmlspecialchars($image_path) : './assets/img/bg1.jpg';
              $name = htmlspecialchars($destination['destination_name'] ?? $destination['name'] ?? 'Unknown Destination');
              $desc = '';
              if (!empty($destination['short_description'])) {
                  $desc = substr(htmlspecialchars($destination['short_description']), 0, 100) . '...';
              } elseif (!empty($destination['description'])) {
                  $desc = substr(htmlspecialchars($destination['description']), 0, 100) . '...';
              }
          ?>
            <!-- Destination Card -->
            <div class="destination-card">
              <div class="card-image">
                <img src="<?php echo $image_src; ?>" alt="<?php echo $name; ?>" />
                <?php if (!empty($destination['region'])): ?>
                  <span class="card-badge"><?php echo htmlspecialchars($destination['region']); ?></span>
                <?php endif; ?>
              </div>
              <div class="card-content">
                <h3><?php echo $name; ?></h3>
                <p><?php echo $desc; ?></p>

                <div class="card-meta">
                  <div class="location">
                    <i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($destination['category'] ?? 'Destination'); ?>
                  </div>
                  <?php if (!empty($destination['rating'])): ?>
                    <div class="rating"><i class="ri-star-fill"></i> <?php echo htmlspecialchars($destination['rating']); ?></div>
                  <?php endif; ?>
                </div>
                <a href="/public/destinations.php?id=<?php echo $destination['id']; ?>" class="card-button">Explore</a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 50px; color: var(--text-color);">
          <p>No destinations found. Please add destinations from the admin panel.</p>
        </div>
      <?php endif; ?>

      <div class="view-all-container">
        <a href="/public/destinations.php" class="view-all-btn">View All Destinations</a>
      </div>
    </section>
  </main>

  <?php
  // Fetch packages section data
  $packages_section_query = mysqli_query($conn, "SELECT * FROM homepage_sections WHERE section_name = 'packages'");
  $packages_section = mysqli_fetch_assoc($packages_section_query);
  
  $packages_title = $packages_section['title'] ?? 'Popular Packages';
  $packages_description = $packages_section['description'] ?? 'Carefully crafted itineraries for unforgettable experiences in Kashmir and Ladakh';
  
  // Fetch featured packages
  $packages_query = mysqli_query($conn, "
    SELECT p.*, pi.image_path 
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    WHERE p.is_featured = 1 AND p.is_active = 1 
    ORDER BY p.created_at DESC 
    LIMIT 3
  ");
  ?>
  
  <!-- Featured Packages Section (for homepage) -->
  <section class="featured-section fade-in">
    <div class="section-header">
      <h2><?php echo htmlspecialchars($packages_title); ?></h2>
      <p><?php echo htmlspecialchars($packages_description); ?></p>
    </div>

    <?php if (mysqli_num_rows($packages_query) > 0): ?>
      <div class="enhanced-grid">
        <?php while ($package = mysqli_fetch_assoc($packages_query)): 
          $package_highlights = json_decode($package['highlights'] ?? '[]', true);
          $first_highlight = !empty($package_highlights) ? $package_highlights[0]['description'] : substr($package['description'] ?? '', 0, 100) . '...';
          $package_inclusions = json_decode($package['inclusions'] ?? '[]', true);
          $accommodation = !empty($package_inclusions) ? 
            (strpos(implode(' ', $package_inclusions), 'Star') !== false ? 
              preg_match('/\d Star/', implode(' ', $package_inclusions), $matches) ? $matches[0] : 'Hotel' 
              : 'Hotel') 
            : 'Hotel';
        ?>
          <!-- Package Card -->
          <div class="enhanced-card fade-up">
            <div class="card-image">
              <img src="./admin/upload/<?php echo htmlspecialchars($package['image_path'] ?? 'packages/default.jpg'); ?>" 
                   alt="<?php echo htmlspecialchars($package['package_name']); ?>"
                   onerror="this.src='./assets/img/bg1.jpg'" />
              <?php if (!empty($package['badge'])): ?>
                <span class="card-badge"><?php echo htmlspecialchars($package['badge']); ?></span>
              <?php endif; ?>
            </div>
            <div class="card-content">
              <h3><?php echo htmlspecialchars($package['package_name']); ?></h3>
              <p><?php echo htmlspecialchars($first_highlight); ?></p>

              <div class="package-details">
                <div class="detail-item">
                  <i class="ri-calendar-event-line"></i> <?php echo htmlspecialchars($package['duration_days'] ?? '7'); ?> Days
                </div>
                <div class="detail-item">
                  <i class="ri-user-line"></i> Max <?php echo htmlspecialchars($package['max_people'] ?? '6'); ?> People
                </div>
                <div class="detail-item">
                  <i class="ri-hotel-bed-line"></i> <?php echo htmlspecialchars($accommodation); ?>
                </div>
              </div>

              <div class="package-price">
                ₹<?php echo number_format($package['price_per_person'] ?? 25999, 2); ?> / person
              </div>

              <div class="card-meta">
                <div class="location">
                  <i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($package['package_type'] ?? 'Package'); ?>
                </div>
                <?php if (!empty($package['rating'])): ?>
                  <div class="rating">
                    <i class="ri-star-fill"></i>
                    <span><?php echo number_format($package['rating'], 1); ?></span>
                  </div>
                <?php endif; ?>
              </div>
              <a href="/public/packages.php?id=<?php echo $package['id']; ?>" class="card-button">View Details</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <!-- Fallback packages if no featured packages are set -->
      <div class="enhanced-grid">
        <!-- Package 1 -->
        <div class="enhanced-card fade-up">
          <div class="card-image">
            <img src="./assets/img/bg1.jpg" alt="Kashmir Valley Explorer" />
            <span class="card-badge">Bestseller</span>
          </div>
          <div class="card-content">
            <h3>Kashmir Valley Explorer</h3>
            <p>Complete Kashmir experience including Dal Lake, Gulmarg, Pahalgam, and Sonamarg with cultural immersion.</p>
            <div class="package-details">
              <div class="detail-item"><i class="ri-calendar-event-line"></i> 7 Days</div>
              <div class="detail-item"><i class="ri-user-line"></i> 6 People</div>
              <div class="detail-item"><i class="ri-hotel-bed-line"></i> 4 Star</div>
            </div>
            <div class="package-price">₹25,999 / person</div>
            <div class="card-meta">
              <div class="location"><i class="ri-map-pin-line"></i> Srinagar · Gulmarg · Pahalgam</div>
              <div class="rating"><i class="ri-star-fill"></i><span>4.9</span></div>
            </div>
            <a href="/public/packages.php" class="card-button">View Details</a>
          </div>
        </div>

        <!-- Package 2 -->
        <div class="enhanced-card fade-up">
          <div class="card-image">
            <img src="./assets/img/dalbg.jpg" alt="Ladakh Adventure" />
            <span class="card-badge">Adventure</span>
          </div>
          <div class="card-content">
            <h3>Ladakh Adventure</h3>
            <p>Explore the majestic landscapes of Ladakh including Pangong Lake, Nubra Valley, and ancient monasteries.</p>
            <div class="package-details">
              <div class="detail-item"><i class="ri-calendar-event-line"></i> 9 Days</div>
              <div class="detail-item"><i class="ri-user-line"></i> 8 People</div>
              <div class="detail-item"><i class="ri-hotel-bed-line"></i> 3 Star</div>
            </div>
            <div class="package-price">₹32,499 / person</div>
            <div class="card-meta">
              <div class="location"><i class="ri-map-pin-line"></i> Leh · Nubra · Pangong</div>
              <div class="rating"><i class="ri-star-fill"></i><span>4.8</span></div>
            </div>
            <a href="/public/packages.php" class="card-button">View Details</a>
          </div>
        </div>

        <!-- Package 3 -->
        <div class="enhanced-card fade-up">
          <div class="card-image">
            <img src="./assets/img/bg3.jpg" alt="Honeymoon Special" />
            <span class="card-badge">Romantic</span>
          </div>
          <div class="card-content">
            <h3>Kashmir Honeymoon Special</h3>
            <p>Romantic getaway for couples with luxury houseboat stay, shikara ride, and private sightseeing.</p>
            <div class="package-details">
              <div class="detail-item"><i class="ri-calendar-event-line"></i> 6 Days</div>
              <div class="detail-item"><i class="ri-user-line"></i> 2 People</div>
              <div class="detail-item"><i class="ri-hotel-bed-line"></i> 5 Star</div>
            </div>
            <div class="package-price">₹38,999 / couple</div>
            <div class="card-meta">
              <div class="location"><i class="ri-map-pin-line"></i> Srinagar · Gulmarg · Pahalgam</div>
              <div class="rating"><i class="ri-star-fill"></i><span>4.9</span></div>
            </div>
            <a href="/public/packages.php" class="card-button">View Details</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="view-all-container">
      <a href="/public/packages.php" class="view-all-btn">View All Packages</a>
    </div>
  </section>

  <?php
  // Fetch gallery section data
  $gallery_section_query = mysqli_query($conn, "SELECT * FROM homepage_sections WHERE section_name = 'gallery'");
  $gallery_section = mysqli_fetch_assoc($gallery_section_query);
  
  $gallery_title = $gallery_section['title'] ?? 'Photo Gallery';
  $gallery_description = $gallery_section['description'] ?? 'Carefully crafted itineraries for unforgettable experiences in Kashmir and Ladakh';
  
  // Fetch gallery images from database
  $gallery_query = mysqli_query($conn, "SELECT * FROM homepage_gallery ORDER BY RAND() LIMIT 6");
  ?>
  
  <!-- GALLERY PREVIEW -->
  <div class="sec-wrapper">
    <div class="section-header">
      <h2><?php echo htmlspecialchars($gallery_title); ?></h2>
      <p><?php echo htmlspecialchars($gallery_description); ?></p>
    </div>
    
    <section class="masonry-gallery-section">
      <?php if (mysqli_num_rows($gallery_query) > 0): ?>
        <div class="masonry-grid">
          <?php while ($gallery_image = mysqli_fetch_assoc($gallery_query)): ?>
            <!-- Gallery Image -->
            <div class="masonry-item fade-up" data-category="<?php echo htmlspecialchars($gallery_image['category']); ?>">
              <a href="./admin/upload/<?php echo htmlspecialchars($gallery_image['image_path']); ?>"
                 data-fancybox="gallery"
                 data-caption="<?php echo htmlspecialchars($gallery_image['title'] . ' - ' . $gallery_image['location']); ?>">
                <img src="./admin/upload/<?php echo htmlspecialchars($gallery_image['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($gallery_image['title']); ?>"
                     onerror="this.src='./assets/img/bg1.jpg'" />
                <div class="image-overlay">
                  <h3><?php echo htmlspecialchars($gallery_image['title']); ?></h3>
                  <p><?php echo htmlspecialchars($gallery_image['location']); ?></p>
                </div>
              </a>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <!-- Fallback gallery if no images are set -->
        <div class="masonry-grid">
          <!-- Kashmir Images -->
          <div class="masonry-item fade-up" data-category="kashmir mountains">
            <a href="./assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Dal Lake - Srinagar, Kashmir">
              <img src="./assets/img/bg3.jpg" alt="Dal Lake" />
              <div class="image-overlay">
                <h3>Dal Lake</h3>
                <p>Srinagar, Kashmir</p>
              </div>
            </a>
          </div>

          <div class="masonry-item fade-up" data-category="kashmir adventure">
            <a href="./assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Gulmarg Ski Resort - Kashmir">
              <img src="./assets/img/bg1.jpg" alt="Gulmarg Ski Resort" />
              <div class="image-overlay">
                <h3>Gulmarg Ski Resort</h3>
                <p>Kashmir</p>
              </div>
            </a>
          </div>

          <div class="masonry-item fade-up" data-category="kashmir lakes">
            <a href="./assets/img/bg2.jpg" data-fancybox="gallery" data-caption="Manasbal Lake - Kashmir">
              <img src="./assets/img/bg2.jpg" alt="Manasbal Lake" />
              <div class="image-overlay">
                <h3>Manasbal Lake</h3>
                <p>Kashmir</p>
              </div>
            </a>
          </div>

          <div class="masonry-item fade-up" data-category="ladakh culture">
            <a href="./assets/img/bg3.jpg" data-fancybox="gallery" data-caption="Local Market - Leh">
              <img src="./assets/img/bg3.jpg" alt="Local Market" />
              <div class="image-overlay">
                <h3>Local Market</h3>
                <p>Leh, Ladakh</p>
              </div>
            </a>
          </div>

          <div class="masonry-item fade-up" data-category="kashmir culture">
            <a href="./assets/img/bg2.jpg" data-fancybox="gallery" data-caption="Shikara Ride - Dal Lake">
              <img src="./assets/img/bg2.jpg" alt="Shikara Ride" />
              <div class="image-overlay">
                <h3>Shikara Ride</h3>
                <p>Dal Lake, Kashmir</p>
              </div>
            </a>
          </div>

          <div class="masonry-item fade-up" data-category="ladakh adventure">
            <a href="./assets/img/bg1.jpg" data-fancybox="gallery" data-caption="Biking Expedition - Ladakh">
              <img src="./assets/img/bg1.jpg" alt="Biking Expedition" />
              <div class="image-overlay">
                <h3>Biking Expedition</h3>
                <p>Ladakh</p>
              </div>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </section>
    
    <div class="view-all-container">
      <a href="/public/gallery.php" class="view-all-btn">View All Images</a>
    </div>
  </div>

  <?php
  // Fetch brands section data
  $brands_section_query = mysqli_query($conn, "SELECT * FROM homepage_sections WHERE section_name = 'brands'");
  $brands_section = mysqli_fetch_assoc($brands_section_query);
  
  $brands_title = $brands_section['title'] ?? 'Trusted By';
  $brands_description = $brands_section['description'] ?? 'Our partners and clients who trust us for their travel needs';
  
  // Fetch brands from database
  $brands_query = mysqli_query($conn, "SELECT * FROM homepage_brands ORDER BY created_at");
  ?>
  
  <!-- REGISTERED WITH BRANDS INFINITE CAROUSEL -->
  <div class="brands-section">
    <div class="section-header">
      <h2><?php echo htmlspecialchars($brands_title); ?></h2>
      <p><?php echo htmlspecialchars($brands_description); ?></p>
    </div>
    
    <?php if (mysqli_num_rows($brands_query) > 0): ?>
      <div class="brands-carousel">
        <div class="brands-list">
          <?php while ($brand = mysqli_fetch_assoc($brands_query)): ?>
            <div class="brand-item">
              <img src="./admin/upload/<?php echo htmlspecialchars($brand['logo_path']); ?>" 
                   alt="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                   onerror="this.src='./assets/img/jktc.jpg'" />
            </div>
          <?php endwhile; ?>
          
          <!-- Duplicate for infinite effect -->
          <?php 
          mysqli_data_seek($brands_query, 0); // Reset pointer
          while ($brand = mysqli_fetch_assoc($brands_query)): ?>
            <div class="brand-item">
              <img src="./admin/upload/<?php echo htmlspecialchars($brand['logo_path']); ?>" 
                   alt="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                   onerror="this.src='./assets/img/jktc.jpg'" />
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php else: ?>
      <!-- Fallback brands if none are set -->
      <div class="brands-carousel">
        <div class="brands-list">
          <div class="brand-item">
            <img src="assets/img/jktc.jpg" alt="Brand 1" />
          </div>
          <div class="brand-item">
            <img src="assets/img/Jk_Tourism.webp" alt="Brand 2" />
          </div>
          <div class="brand-item">
            <img src="assets/img/TOA-New.png" alt="Brand 3" />
          </div>
          <div class="brand-item">
            <img src="assets/img/travelassociation.jpg" alt="Brand 4" />
          </div>
          <div class="brand-item">
            <img src="assets/img/jktc (1).jpg" alt="Brand 5" />
          </div>
          <!-- Duplicate for infinite effect -->
          <div class="brand-item">
            <img src="assets/img/jktc.jpg" alt="Brand 1" />
          </div>
          <div class="brand-item">
            <img src="assets/img/Jk_Tourism.webp" alt="Brand 2" />
          </div>
          <div class="brand-item">
            <img src="assets/img/TOA-New.png" alt="Brand 3" />
          </div>
          <div class="brand-item">
            <img src="assets/img/travelassociation.jpg" alt="Brand 4" />
          </div>
          <div class="brand-item">
            <img src="assets/img/jktc (1).jpg" alt="Brand 5" />
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <?php
  // Fetch CTA section data
  $cta_section_query = mysqli_query($conn, "SELECT * FROM homepage_sections WHERE section_name = 'cta'");
  $cta_section = mysqli_fetch_assoc($cta_section_query);
  
  $cta_title = $cta_section['title'] ?? 'Ready to Explore Kashmir & Ladakh?';
  $cta_description = $cta_section['description'] ?? 'Contact us now to plan your dream vacation with our expert guides';
  
  // Parse meta data for CTA buttons
  $cta_meta = !empty($cta_section['meta_data']) ? json_decode($cta_section['meta_data'], true) : [
    'primary_text' => 'Get in Touch',
    'primary_link' => '/public/contact.php',
    'secondary_text' => 'View Packages',
    'secondary_link' => '/public/packages.php'
  ];
  ?>
  
  <!-- CALL-TO-ACTION -->
  <section class="cta-section">
    <div class="cta-content">
      <h2><?php echo htmlspecialchars($cta_title); ?></h2>
      <p><?php echo htmlspecialchars($cta_description); ?></p>
      <div class="cta-buttons">
        <a href="<?php echo htmlspecialchars($cta_meta['primary_link'] ?? '/public/contact.php'); ?>" class="cta-btn primary">
          <?php echo htmlspecialchars($cta_meta['primary_text'] ?? 'Get in Touch'); ?>
        </a>
        <a href="<?php echo htmlspecialchars($cta_meta['secondary_link'] ?? '/public/packages.php'); ?>" class="cta-btn secondary">
          <?php echo htmlspecialchars($cta_meta['secondary_text'] ?? 'View Packages'); ?>
        </a>
      </div>
    </div>
  </section>

  <?php
  include './admin/includes/footer.php';
  ?>

  <!-- GSAP & ScrollTrigger -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Your GSAP animations file -->
  <script src="./assets/js/gsap.js"></script>

  <script>
    // SEARCH TOUR
    document
      .getElementById("tourForm")
      .addEventListener("submit", function(e) {
        e.preventDefault();

        const destination = document.getElementById("destination").value;
        const checkin = document.getElementById("checkin").value;
        const duration = document.getElementById("duration").value;
        const travelers = document.getElementById("travelers").value;

        if (destination && checkin && duration && travelers) {
          alert(`Searching tours for:
  Destination: ${destination}
  Check-in: ${checkin}
  Duration: ${duration} days
  Travelers: ${travelers}`);
        } else {
          alert("Please fill all fields.");
        }
      });

    // Get today's date in YYYY-MM-DD format
    const today = new Date().toISOString().split("T")[0];

    // Set it as default value
    document.getElementById("checkin").value = today;

    // Initialize animations
    document.addEventListener("DOMContentLoaded", function() {
      // Animate destination cards on scroll
      const destinationCards = document.querySelectorAll(".destination-card");

      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = 1;
              entry.target.style.transform = "translateY(0)";
            }
          });
        }, {
          threshold: 0.1
        }
      );

      destinationCards.forEach((card, index) => {
        card.style.opacity = 0;
        card.style.transform = "translateY(30px)";
        card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        card.style.transitionDelay = `${index * 0.1}s`;
        observer.observe(card);
      });
    });
  </script>

  <!-- Linking GSAP script -->
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"
    integrity="sha512-NcZdtrT77bJr4STcmsGAESr06BYGE8woZdSdEgqnpyqac7sugNO+Tr4bGwGF3MsnEkGKhU2KL2xh6Ec+BqsaHA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"></script>

  <script>
    // Brands carousel animation
    let carouselList = document.querySelector('.brands-list');
    if (carouselList) {
      gsap.to(carouselList, {
        x: "-100%",
        duration: 25,
        ease: "linear",
        repeat: -1,
      });
    }
  </script> 

  <!-- fast images loading script
    -->

  <!--=============== MAIN JS ===============-->
  <script src="./assets/js/main.js" defer></script>
</body>

</html>