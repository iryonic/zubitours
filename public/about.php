<?php
require_once '../admin/includes/connection.php';

// Fetch General Sections
$about_res = $conn->query("SELECT * FROM about_page");
$about_sections = [];
while($row = $about_res->fetch_assoc()) {
    $about_sections[$row['section_key']] = $row;
}

// Fetch Values
$values_res = $conn->query("SELECT * FROM about_values WHERE is_active = 1 ORDER BY display_order");

// Fetch Team
$team_res = $conn->query("SELECT * FROM about_team WHERE is_active = 1 ORDER BY display_order");

// Fetch Stats
$stats_res = $conn->query("SELECT * FROM about_stats ORDER BY display_order");
?>
<!DOCTYPE html>
<html lang="en">  

<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M7PZ56RR');</script>
<!-- End Google Tag Manager -->
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

  <title>About Us | Zubi Tours & Holidays – Kashmir Travel Experts</title>

<meta name="description" content="Zubi Tours & Holidays is a trusted Kashmir-based tour and travel company providing personalized, safe, and memorable travel experiences across Jammu & Kashmir.">

<meta name="keywords" content="
about Zubi Tours,
Kashmir tour company,
Kashmir tour operator,
travel agency in Srinagar,
Kashmir tourism experts,
best tour planner Kashmir
">

<!-- --==============Favicon =============-- -->
<link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/img/zubilogo.jpg" />


  <!--=============== REMIXICONS ===============-->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!--=============== CSS ===============-->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css" />

  
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7PZ56RR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
 
  <!--==================== HEADER ====================-->
  <?php include '../admin/includes/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="section-header">
      <h2><?php echo htmlspecialchars($about_sections['hero']['title'] ?? 'About Us'); ?></h2>
      <p><?php echo htmlspecialchars($about_sections['hero']['subtitle'] ?? 'Your trusted partner for tours in Kashmir'); ?></p>
    </div>
  </section>

  <!-- About Content -->
  <section class="about-content">
    <div class="about-container">
      <div class="about-text">
        <h2><?php echo htmlspecialchars($about_sections['story']['title'] ?? 'Our Story'); ?></h2>
        <div class="story-content">
            <?php echo nl2br(htmlspecialchars($about_sections['story']['content'] ?? 'Founded in 2010...')); ?>
        </div>

        <div class="stats-container">
          <?php while($stat = $stats_res->fetch_assoc()): ?>
          <div class="stat">
            <h3><span class="count" data-target="<?php echo $stat['value']; ?>">0</span>+</h3>
            <p><?php echo htmlspecialchars($stat['label']); ?></p>
          </div>
          <?php endwhile; ?>
        </div>
      </div>

      <div class="about-image">
        <?php 
          $story_img = $about_sections['story']['image_path'] ?? '';
          $story_src = (!empty($story_img)) 
                       ? BASE_URL . 'admin/upload/' . $story_img 
                       : BASE_URL . 'assets/img/bg1.jpg';
        ?>
        <img loading="lazy" src="<?php echo $story_src; ?>" alt="Our Story" onerror="this.src='../assets/img/bg1.jpg'">
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="values-section">
    <div class="section-heading">
      <h2>Our Values</h2>
      <p>The principles that guide everything we do</p>
    </div>

    <div class="values-container">
      <?php while($value = $values_res->fetch_assoc()): ?>
      <div class="value-card">
        <div class="value-icon">
          <i class="<?php echo htmlspecialchars($value['icon']); ?>"></i>
        </div>
        <h3><?php echo htmlspecialchars($value['title']); ?></h3>
        <p><?php echo htmlspecialchars($value['description']); ?></p>
      </div>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- Team Section -->
  <section class="team-section">
    <div class="section-heading">
      <h2>Meet Our Team</h2>
      <p>The passionate people behind Zubi Tours</p>
    </div>

    <div class="team-container">
      <?php while($member = $team_res->fetch_assoc()): ?>
      <div class="team-member">
        <div class="member-image">
          <?php 
            $member_img = $member['image_path'] ?? '';
            $member_src = (!empty($member_img)) 
                         ? BASE_URL . 'admin/upload/' . $member_img 
                         : BASE_URL . 'assets/img/bg1.jpg';
          ?>
          <img loading="lazy" src="<?php echo $member_src; ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" onerror="this.src='../assets/img/bg1.jpg'">
        </div>
        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
        <p class="role"><?php echo htmlspecialchars($member['role']); ?></p>
        <p><?php echo htmlspecialchars($member['description']); ?></p>
      </div>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- Testimonials -->
  <?php
  $testimonials_res = $conn->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC LIMIT 10");
  if ($testimonials_res->num_rows > 0):
  ?>
  <section class="testimonials-section">
    <div class="section-heading">
      <h2>What Our Travelers Say</h2>
      <p>Authentic experiences from real customers</p>
    </div>

    <div class="testimonials-swiper swiper" style="padding: 20px 0 50px;">
      <div class="swiper-wrapper">
        <?php while($testi = $testimonials_res->fetch_assoc()): ?>
        <div class="swiper-slide">
          <div class="testimonial">
            <div class="testimonial-content">
              <p>"<?php echo htmlspecialchars($testi['testimonial_text']); ?>"</p>
            </div>
            <div class="testimonial-author" style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
              <img src="<?php echo BASE_URL; ?>admin/upload/<?php echo $testi['avatar_path'] ?: 'bg1.jpg'; ?>" 
                   style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" 
                   onerror="this.src='<?php echo BASE_URL; ?>assets/img/iry.png'">
              <div>
                <h4 style="margin: 0;"><?php echo htmlspecialchars($testi['author_name']); ?></h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--primary-color);"><?php echo htmlspecialchars($testi['package_name'] ?? 'Verified Customer'); ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </section>
  <?php endif; ?>

  <!-- CTA Section -->
  <section class="about-cta">
    <div class="cta-content">
      <h2><?php echo htmlspecialchars($about_sections['cta']['title'] ?? 'Ready to Explore with Us?'); ?></h2>
      <p><?php echo htmlspecialchars($about_sections['cta']['subtitle'] ?? 'Join thousands of satisfied travelers...'); ?></p>
      <div class="cta-buttons">
        <a href="<?php echo BASE_URL; ?>packages" class="cta-btn primary">View Packages</a>
        <a href="<?php echo BASE_URL; ?>contact" class="cta-btn secondary">Contact Us</a>
      </div>
    </div>
  </section>


  <!-- FOOTER -->
<?php include '../admin/includes/footer.php'; ?>

  <!-- Linking Swiper script -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <!--=============== MAIN JS ===============-->
  <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

  <script>
    // Animate stats when they scroll into view
    function animateCount(el) {
      const target = +el.getAttribute('data-target');
      const duration = 2200;
      const start = 0;
      const startTime = performance.now();

      function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        el.innerText = Math.floor(progress * (target - start) + start);
        if (progress < 1) {
          requestAnimationFrame(update);
        } else {
          el.innerText = target;
        }
      }
      requestAnimationFrame(update);
    }

    // Use Intersection Observer for scroll trigger
    const counters = document.querySelectorAll('.count');
    let counted = false;
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !counted) {
            counters.forEach(animateCount);
            counted = true;
            obs.disconnect();
          }
        });
      }, {
        threshold: 0.4
      });
      counters.forEach(counter => observer.observe(counter));
    } else {
      // Fallback: animate immediately
      counters.forEach(animateCount);
    }

    // Initialize Testimonials Swiper
    new Swiper(".testimonials-swiper", {
      slidesPerView: 1,
      spaceBetween: 30,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      },
    });
  </script>
</body>

</html>