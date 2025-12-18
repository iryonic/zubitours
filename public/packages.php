<?php
require_once '../admin/includes/connection.php';

// Handle filters
$whereConditions = [];
$params = [];
$types = "";
$search_term = "";

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $whereConditions[] = "(p.package_name LIKE ? OR p.description LIKE ? OR p.package_type LIKE ?)";
    $search_param = "%" . $search_term . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Duration filter
if (isset($_GET['duration']) && $_GET['duration'] !== 'all') {
    $duration = $_GET['duration'];
    switch($duration) {
        case '3-5':
            $whereConditions[] = "p.duration_days BETWEEN 3 AND 5";
            break;
        case '6-8':
            $whereConditions[] = "p.duration_days BETWEEN 6 AND 8";
            break;
        case '9-12':
            $whereConditions[] = "p.duration_days BETWEEN 9 AND 12";
            break;
        case '12+':
            $whereConditions[] = "p.duration_days >= 12";
            break;
    }
}

// Type filter
if (isset($_GET['type']) && $_GET['type'] !== 'all') {
    $whereConditions[] = "p.package_type = ?";
    $params[] = $_GET['type'];
    $types .= "s";
}

// Price filter
if (isset($_GET['price']) && $_GET['price'] !== 'all') {
    $price = $_GET['price'];
    switch($price) {
        case 'budget':
            $whereConditions[] = "p.price_per_person < 20000";
            break;
        case 'mid-range':
            $whereConditions[] = "p.price_per_person BETWEEN 20000 AND 40000";
            break;
        case 'premium':
            $whereConditions[] = "p.price_per_person > 40000";
            break;
    }
}

// Only show active and featured packages first
$whereConditions[] = "p.is_active = 1";

// Optional check-in date and travelers filter (server-side availability)
$checkin = trim($_GET['checkin'] ?? '');
$travelers = isset($_GET['travelers']) ? max(0, intval($_GET['travelers'])) : 0;

if ($checkin !== '' && $travelers > 0) {
    // Ensure package has enough capacity considering existing bookings overlapping the requested window
    $whereConditions[] = "(p.max_people - (
        SELECT COALESCE(SUM(pb.number_of_adults + pb.number_of_children), 0)
        FROM package_bookings pb
        WHERE pb.package_id = p.id
          AND pb.booking_status NOT IN ('cancelled', 'refunded')
          AND (pb.checkin_date < DATE_ADD(?, INTERVAL p.duration_days DAY) AND pb.checkout_date > ?)
    )) >= ?";
    // bind checkin for DATE_ADD and overlap check, then the travelers count
    $params[] = $checkin;
    $params[] = $checkin;
    $params[] = $travelers;
    $types .= 'ssi';
}

// Build WHERE clause
$whereClause = "";
if (!empty($whereConditions)) {
    $whereClause = "WHERE " . implode(" AND ", $whereConditions);
}

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM packages p $whereClause";
$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_packages = $count_result->fetch_assoc()['total'];

// Pagination
$limit = 9;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_packages / $limit);

// Fetch packages with their primary images
$query = "
    SELECT p.*, pi.image_path 
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    $whereClause
    ORDER BY p.is_featured DESC, p.created_at DESC
    LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$packages = $stmt->get_result();

// Get featured packages for hero section
$featured_query = $conn->query("
    SELECT p.*, pi.image_path 
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    WHERE p.is_active = 1 AND p.is_featured = 1 
    ORDER BY p.created_at DESC 
    LIMIT 3
");
$featured_packages = $featured_query->fetch_all(MYSQLI_ASSOC);

// Get testimonials
$testimonials_query = $conn->query("
    SELECT * FROM testimonials 
    WHERE is_active = 1 
    ORDER BY display_order, created_at DESC 
    LIMIT 3
");
$testimonials = $testimonials_query->fetch_all(MYSQLI_ASSOC);

// Get package types for filter options
$types_query = $conn->query("SELECT DISTINCT package_type FROM packages WHERE is_active = 1 ORDER BY package_type");
$package_types = $types_query->fetch_all(MYSQLI_ASSOC);
?>

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

<!-- --==============Favicon =============-- -->
<link rel="icon" type="image/png" href="../assets/img/zubilogo.jpg" />

<title>Kashmir Tour Packages | Honeymoon, Family & Group Tours</title>

<meta name="description" content="Choose from the best Kashmir tour packages including honeymoon, family, group and adventure tours. Affordable prices with expert local guides.">

<meta name="keywords" content="
Kashmir tour packages,
Kashmir honeymoon packages,
Kashmir family tour packages,
cheap Kashmir tour packages,
luxury Kashmir tours,
group tour Kashmir,
custom Kashmir tour
">



    <!--=============== REMIXICONS ===============-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />

 
    <style>
        /* Enhanced Styles */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-color-light);
            font-size: 1.2rem;
            grid-column: 1 / -1;
        }
        
        .filter-active {
            background: var(--first-color) !important;
            color: white !important;
            border-color: var(--first-color) !important;
        }

        /* Enhanced filter toolbar */
        .enhanced-filters {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: flex-start;
            background: #fff;
            padding: 14px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(10, 10, 20, 0.04);
            border: 1px solid var(--border-color);
            max-width: 1100px;
            margin: 20px auto 30px;
        }

        .enhanced-filters form {
            display: flex;
            gap: 12px;
            width: 100%;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 180px;
            flex: 1 1 180px;
        }

        .filter-group label {
            font-size: 0.85rem;
            color: var(--text-color-light);
            font-weight: 600;
        }

        .filter-select {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #fff;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            box-shadow: 0 6px 18px rgba(42, 61, 232, 0.08);
            border-color: var(--first-color);
        }

        .filter-btn {
            margin-left: auto;
            background: transparent;
            border: 2px solid var(--border-color);
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--text-color);
            transition: all 0.15s ease;
        }

        .filter-btn i { font-size: 1.1rem; }

        .filter-btn:hover {
            background: var(--first-color);
            color: #fff;
            border-color: var(--first-color);
        }

        /* Responsive adjustments */
        @media (max-width: 900px) {
            .enhanced-filters { padding: 12px; gap: 10px; }
            .filter-group { min-width: 140px; flex: 1 1 140px; }
            .filter-btn { padding: 9px 12px; }
        }

        @media (max-width: 640px) {
            .enhanced-filters { flex-direction: column; align-items: stretch; }
            .enhanced-filters form { flex-direction: column; align-items: stretch; gap: 0; }
            .filter-group { width: 100%; min-width: unset; }
            .filter-btn { width: 100%; margin-left: 0; }
        }

        .search-container {
            max-width: 800px;
            margin: 0 auto 40px;
            position: relative;
        }
        
        .search-box {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid var(--border-color);
            border-radius: 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .search-box:focus {
            outline: none;
            border-color: var(--first-color);
            box-shadow: 0 5px 20px rgba(42, 61, 232, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color-light);
            font-size: 1.2rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }
        
        .page-link {
            padding: 10px 18px;
            border-radius: 10px;
            background: white;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--border-color);
        }
        
        .page-link:hover,
        .page-link.active {
            background: var(--first-color);
            color: white;
            border-color: var(--first-color);
        }
        
        .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .results-count {
            text-align: center;
            color: var(--text-color-light);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .results-count strong {
            color: var(--first-color);
        }
        
        .featured-packages {
            margin-bottom: 60px;
        }
        
        .featured-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .featured-header h2 {
            font-size: 2.5rem;
            color: var(--title-color);
            margin-bottom: 15px;
        }
        
        .featured-header p {
            color: var(--text-color-light);
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .featured-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        /* Hero Section Styles */
        .hero-background {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5)), url('../assets/img/bg1.jpg');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 0 0 30px 30px;
            margin-bottom: 60px;
        }
        
        .hero-content {
            padding: 0 20px;
            max-width: 800px;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 800;
        }
        
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 40px;
        }
        
        .hero-stat {
            text-align: center;
        }
        
        .hero-stat i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--first-color);
        }
        
        .hero-stat h3 {
            font-size: 2rem;
            margin-bottom: 5px;
            font-weight: 800;
        }
        
        .hero-stat p {
            font-size: 1rem;
            opacity: 0.8;
            margin: 0;
        }
        
        /* Featured Packages Carousel */
        .featured-carousel {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .featured-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .featured-card:hover {
            transform: translateY(-10px);
        }
        
        .featured-card .card-image {
            height: 250px;
            position: relative;
            overflow: hidden;
        }
        
        .featured-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .featured-card:hover .card-image img {
            transform: scale(1.05);
        }
        
        .featured-card .card-content {
            padding: 25px;
        }
        
        .featured-card .card-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--title-color);
        }
        
        .featured-card .card-content p {
            color: var(--text-color);
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        /* Type badges */
        .type-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .type-cultural { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .type-adventure { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .type-luxury { background: rgba(168, 85, 247, 0.15); color: #8b5cf6; }
        .type-honeymoon { background: rgba(236, 72, 153, 0.15); color: #ec4899; }
        .type-family { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1.1rem;
            }
            
            .hero-stats {
                gap: 20px;
            }
            
            .hero-stat h3 {
                font-size: 1.5rem;
            }
            
            .enhanced-filters {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                width: 100%;
            }
        }
    </style>
  </head>
  <body>

  
  

    <!--==================== HEADER ====================-->
    <?php include '../admin/includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="modern-hero">
      <div class="hero-background">
        <div class="hero-content">
          <h1>  OUR PACKAGES</h1>
          <p>Experience breathtaking landscapes, rich culture, and unforgettable adventures with our curated tour packages</p>
          
         
        </div>
      </div>
    </section>

    <!-- Featured Packages -->
  

    <!-- Search Box -->
    <div class="search-container">
      <form method="GET" action="" id="search-form">
        <i class="ri-search-line search-icon"></i>
        <input type="text" 
               name="search" 
               class="search-box" 
               placeholder="Search packages by name, destination, or activity..."
               value="<?php echo htmlspecialchars($search_term); ?>"
               >
      </form>
    </div>

    <!-- Results Count -->
    <div class="results-count">
      Found <strong><?php echo $total_packages; ?></strong> package<?php echo $total_packages != 1 ? 's' : ''; ?>
      <?php if ($search_term): ?>
        for "<strong><?php echo htmlspecialchars($search_term); ?></strong>"
      <?php endif; ?>
    </div>

    <!-- Enhanced Filters -->
    <div class="enhanced-filters">
      <form method="GET" action="" id="filter-form">
        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
        
        <div class="filter-group">
          <label for="duration">Package Duration</label>
          <select id="duration" name="duration" class="filter-select" onchange="this.form.submit()">
            <option value="all" <?php echo (!isset($_GET['duration']) || $_GET['duration'] == 'all') ? 'selected' : ''; ?>>Any Duration</option>
            <option value="3-5" <?php echo (isset($_GET['duration']) && $_GET['duration'] == '3-5') ? 'selected' : ''; ?>>3-5 Days</option>
            <option value="6-8" <?php echo (isset($_GET['duration']) && $_GET['duration'] == '6-8') ? 'selected' : ''; ?>>6-8 Days</option>
            <option value="9-12" <?php echo (isset($_GET['duration']) && $_GET['duration'] == '9-12') ? 'selected' : ''; ?>>9-12 Days</option>
            <option value="12+" <?php echo (isset($_GET['duration']) && $_GET['duration'] == '12+') ? 'selected' : ''; ?>>12+ Days</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="type">Package Type</label>
          <select id="type" name="type" class="filter-select" onchange="this.form.submit()">
            <option value="all" <?php echo (!isset($_GET['type']) || $_GET['type'] == 'all') ? 'selected' : ''; ?>>All Types</option>
            <?php foreach ($package_types as $type): ?>
              <option value="<?php echo $type['package_type']; ?>" 
                <?php echo (isset($_GET['type']) && $_GET['type'] == $type['package_type']) ? 'selected' : ''; ?>>
                <?php echo ucfirst($type['package_type']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="price">Price Range</label>
          <select id="price" name="price" class="filter-select" onchange="this.form.submit()">
            <option value="all" <?php echo (!isset($_GET['price']) || $_GET['price'] == 'all') ? 'selected' : ''; ?>>Any Price</option>
            <option value="budget" <?php echo (isset($_GET['price']) && $_GET['price'] == 'budget') ? 'selected' : ''; ?>>Budget (Under ₹20,000)</option>
            <option value="mid-range" <?php echo (isset($_GET['price']) && $_GET['price'] == 'mid-range') ? 'selected' : ''; ?>>Mid-Range (₹20,000-₹40,000)</option>
            <option value="premium" <?php echo (isset($_GET['price']) && $_GET['price'] == 'premium') ? 'selected' : ''; ?>>Premium (₹40,000+)</option>
          </select>
        </div>
        
        <button type="button" class="filter-btn" id="clearFilters">
          <i class="ri-refresh-line"></i> Clear Filters
        </button>
      </form>
    </div>

    <!-- Enhanced Packages Grid -->
    <section class="enhanced-grid" id="packages-grid">
      <?php if ($packages->num_rows > 0): ?>
        <?php while ($package = $packages->fetch_assoc()): 
          $badges = [];
          if ($package['badge']) $badges[] = $package['badge'];
          if ($package['is_featured']) $badges[] = 'Featured';
          
          $highlights = json_decode($package['highlights'], true) ?: [];
          $firstHighlight = !empty($highlights) ? $highlights[0]['description'] : substr($package['description'], 0, 150) . '...';
          
          // Calculate discount if any
          $original_price = $package['price_per_person'] * 1.15; // Assuming 15% discount
          $has_discount = $package['badge'] == 'Bestseller' || $package['is_featured'];
          ?>
          
          <div class="enhanced-card" 
               data-duration="<?php echo getDurationRange($package['duration_days']); ?>" 
               data-type="<?php echo $package['package_type']; ?>" 
               data-price="<?php echo getPriceRange($package['price_per_person']); ?>">
            <div class="card-image">
              <img loading="lazy" src="../admin/upload/<?php echo $package['image_path'] ?: 'bg1.jpg'; ?>" 
                   alt="<?php echo htmlspecialchars($package['package_name']); ?>"
                   onerror="this.src='../assets/img/bg1.jpg'">
              <?php foreach ($badges as $badge): ?>
                <span class="card-badge"><?php echo $badge; ?></span>
              <?php endforeach; ?>
            </div>
            <div class="card-content">
              <span class="type-badge type-<?php echo $package['package_type']; ?>">
                <?php echo ucfirst($package['package_type']); ?>
              </span>
              
              <h3><?php echo htmlspecialchars($package['package_name']); ?></h3>
              <p><?php echo htmlspecialchars($firstHighlight); ?></p>
              
              <div class="package-details">
                <div class="detail-item">
                  <i class="ri-calendar-event-line"></i> <?php echo $package['duration_days']; ?> Days
                </div>
                <div class="detail-item">
                  <i class="ri-user-line"></i> Max <?php echo $package['max_people']; ?> People
                </div>
                <div class="detail-item">
                  <i class="ri-hotel-bed-line"></i> <?php echo $package['accommodation_type'] ?: 'Standard'; ?>
                </div>
              </div>
              
              <div class="package-price">
                <?php if ($has_discount): ?>
                  <span style="text-decoration: line-through; color: var(--text-color-light); font-size: 0.9rem; margin-right: 10px;">
                    ₹<?php echo number_format($original_price, 2); ?>
                  </span>
                <?php endif; ?>
                ₹<?php echo number_format($package['price_per_person'], 2); ?> / person
              </div>
              
              <?php if ($has_discount): ?>
                <div class="price-note" style="color: #ea580c; font-weight: 600;">
                  <i class="ri-discount-percent-line"></i> Save 15% on this package
                </div>
              <?php else: ?>
                <div class="price-note">All inclusive package with meals, accommodation and transport</div>
              <?php endif; ?>
              
              <div class="card-meta">
                <div class="location">
                  <i class="ri-map-pin-line"></i> 
                  <?php 
                  $locations = extractLocations($package['description']);
                  echo $locations ?: 'Kashmir Valley';
                  ?>
                </div>
                <div class="rating">
                  <i class="ri-star-fill"></i>
                  <span><?php echo $package['rating'] ? number_format($package['rating'], 1) : '4.9'; ?></span>
                  <span style="color: var(--text-color-light); font-size: 0.9rem;">
                    (<?php echo $package['reviews_count'] ?: '128'; ?> reviews)
                  </span>
                </div>
              </div>
              <div class="card-buttons">
                <a href="./package-details.php?id=<?php echo $package['id']; ?>" class="card-button">View Details</a>
                <a href="./package-details.php?id=<?php echo $package['id']; ?>#booking" class="card-button outline">Book Now</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-results">
          <i class="ri-search-line" style="font-size: 3rem; margin-bottom: 20px; color: var(--text-color-light);"></i>
          <h3>No packages found</h3>
          <p>Try adjusting your filters or check back later for new packages.</p>
          <a href="./packages.php" class="card-button" style="margin-top: 20px; display: inline-block;">View All Packages</a>
        </div>
      <?php endif; ?>
    </section>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?<?php echo buildQueryString(['page' => $page - 1]); ?>" class="page-link">
          <i class="ri-arrow-left-line"></i> Previous
        </a>
      <?php else: ?>
        <span class="page-link disabled"><i class="ri-arrow-left-line"></i> Previous</span>
      <?php endif; ?>
      
      <?php 
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);
      
      if ($start_page > 1) {
        echo '<a href="?' . buildQueryString(['page' => 1]) . '" class="page-link">1</a>';
        if ($start_page > 2) echo '<span class="page-link">...</span>';
      }
      
      for ($i = $start_page; $i <= $end_page; $i++): 
        if ($i == $page): ?>
          <span class="page-link active"><?php echo $i; ?></span>
        <?php else: ?>
          <a href="?<?php echo buildQueryString(['page' => $i]); ?>" class="page-link"><?php echo $i; ?></a>
        <?php endif;
      endfor;
      
      if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) echo '<span class="page-link">...</span>';
        echo '<a href="?' . buildQueryString(['page' => $total_pages]) . '" class="page-link">' . $total_pages . '</a>';
      }
      ?>
      
      <?php if ($page < $total_pages): ?>
        <a href="?<?php echo buildQueryString(['page' => $page + 1]); ?>" class="page-link">
          Next <i class="ri-arrow-right-line"></i>
        </a>
      <?php else: ?>
        <span class="page-link disabled">Next <i class="ri-arrow-right-line"></i></span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
      <div class="section-header">
        <h2>Guest Experiences</h2>
        <p>What our travelers say about our packages</p>
      </div>
      
      <div class="testimonial-cards">
        <?php if (!empty($testimonials)): ?>
          <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-card">
              <div class="rating-stars" style="margin-bottom: 15px;">
                <?php 
                $rating = $testimonial['rating'] ?? 5;
                for ($i = 1; $i <= 5; $i++): 
                  if ($i <= $rating): ?>
                    <i class="ri-star-fill" style="color: #f59e0b;"></i>
                  <?php else: ?>
                    <i class="ri-star-line" style="color: #f59e0b;"></i>
                  <?php endif;
                endfor; ?>
              </div>
              
              <div class="testimonial-text">
                "<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"
              </div>
              <div class="testimonial-author">
                <div class="author-avatar">
                  <img loading="lazy" src="../assets/img/<?php echo $testimonial['avatar_path'] ?: 'default-avatar.jpg'; ?>" 
                       alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>"
                       onerror="this.src='../assets/img/bg1.jpg'">
                </div>
                <div class="author-details">
                  <h4><?php echo htmlspecialchars($testimonial['author_name']); ?></h4>
                  <p><?php echo htmlspecialchars($testimonial['package_name'] ?? 'Kashmir Valley Explorer'); ?></p>
                  <small><?php echo date('F Y', strtotime($testimonial['created_at'])); ?></small>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback testimonials -->
          <div class="testimonial-card">
            <div class="rating-stars" style="margin-bottom: 15px;">
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
            </div>
            <div class="testimonial-text">
              "The Kashmir Valley Explorer package exceeded our expectations. Every detail was perfectly arranged, and our guide made the experience truly special."
            </div>
            <div class="testimonial-author">
              <div class="author-avatar">
                <img loading="lazy" src="../assets/img/default-avatar.jpg" alt="Rajesh Kumar">
              </div>
              <div class="author-details">
                <h4>Rajesh Kumar</h4>
                <p>Kashmir Valley Explorer</p>
                <small>January 2024</small>
              </div>
            </div>
          </div>
          
          <div class="testimonial-card">
            <div class="rating-stars" style="margin-bottom: 15px;">
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
            </div>
            <div class="testimonial-text">
              "Our Ladakh adventure was the trip of a lifetime! The itinerary was perfectly paced, and the team took care of everything from permits to accommodations."
            </div>
            <div class="testimonial-author">
              <div class="author-avatar">
                <img loading="lazy" src="../assets/img/default-avatar.jpg" alt="Priya Singh">
              </div>
              <div class="author-details">
                <h4>Priya Singh</h4>
                <p>Ladakh Adventure Expedition</p>
                <small>December 2023</small>
              </div>
            </div>
          </div>
          
          <div class="testimonial-card">
            <div class="rating-stars" style="margin-bottom: 15px;">
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
              <i class="ri-star-fill" style="color: #f59e0b;"></i>
            </div>
            <div class="testimonial-text">
              "The honeymoon package was absolutely romantic. The houseboat stay was magical, and the private shikara ride at sunset was unforgettable."
            </div>
            <div class="testimonial-author">
              <div class="author-avatar">
                <img loading="lazy" src="../assets/img/bg1.jpg" alt="Amit & Sunita">
              </div>
              <div class="author-details">
                <h4>Amit & Sunita</h4>
                <p>Kashmir Honeymoon Special</p>
                <small>November 2023</small>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-content">
        <h2>Ready for Your Adventure?</h2>
        <p>Contact us to customize a package that perfectly matches your travel dreams</p>
        <div class="cta-buttons">
          <a href="tel:+917006296814" class="cta-button primary">
            <i class="ri-phone-line"></i> Call Now
          </a>
          <a href="./contact.php" class="cta-button secondary">
            <i class="ri-mail-line"></i> Get Quote
          </a>
          <a href="./destinations.php" class="cta-button outline">
            <i class="ri-map-pin-line"></i> Explore Destinations
          </a>
        </div>
      </div>
    </section>

     <!-- FOOTER -->
<?php include '../admin/includes/footer.php'; ?>

    <!-- Linking Swiper script -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Clear filters button
        document.getElementById('clearFilters').addEventListener('click', function() {
          window.location.href = './packages.php';
        });

        // Show active filter state
        const urlParams = new URLSearchParams(window.location.search);
        ['duration', 'type', 'price'].forEach(param => {
          if (urlParams.has(param) && urlParams.get(param) !== 'all') {
            const select = document.querySelector(`select[name="${param}"]`);
            if (select) {
              select.classList.add('filter-active');
            }
          }
        });

        // Initialize animations
        setTimeout(() => {
          document.querySelectorAll('.enhanced-card').forEach((card, index) => {
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, index * 100);
          });
        }, 500);
        
        // Initialize Swiper for featured packages
        <?php if (!empty($featured_packages)): ?>
        const featuredSwiper = new Swiper('.featured-carousel', {
          slidesPerView: 1,
          spaceBetween: 20,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          breakpoints: {
            640: {
              slidesPerView: 2,
              spaceBetween: 20,
            },
            1024: {
              slidesPerView: 3,
              spaceBetween: 30,
            },
          },
        });
        <?php endif; ?>
        
        // Update year in footer
        document.getElementById('getYear').textContent = new Date().getFullYear();
      });

      // Filter packages on frontend (optional enhancement)
      function filterPackages() {
        const durationFilter = document.getElementById('duration').value;
        const typeFilter = document.getElementById('type').value;
        const priceFilter = document.getElementById('price').value;
        
        document.querySelectorAll('.enhanced-card').forEach(card => {
          const cardDuration = card.getAttribute('data-duration');
          const cardType = card.getAttribute('data-type');
          const cardPrice = card.getAttribute('data-price');
          
          const durationMatch = durationFilter === 'all' || durationFilter === cardDuration;
          const typeMatch = typeFilter === 'all' || typeFilter === cardType;
          const priceMatch = priceFilter === 'all' || priceFilter === cardPrice;
          
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
      
      // Auto-search functionality
      let searchTimeout;
      document.querySelector('.search-box').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          if (e.target.value.length >= 2 || e.target.value.length === 0) {
            e.target.form.submit();
          }
        }, 500);
      });
    </script>
  </body>
</html>

<?php
// Helper functions
function getDurationRange($days) {
    if ($days <= 5) return '3-5';
    if ($days <= 8) return '6-8';
    if ($days <= 12) return '9-12';
    return '12+';
}

function getPriceRange($price) {
    if ($price < 20000) return 'budget';
    if ($price <= 40000) return 'mid-range';
    return 'premium';
}

function extractLocations($description) {
    $locations = [];
    $commonLocations = ['Srinagar', 'Gulmarg', 'Pahalgam', 'Sonamarg', 'Leh', 'Nubra', 'Pangong', 'Dal Lake', 'Kashmir', 'Ladakh'];
    
    foreach ($commonLocations as $location) {
        if (stripos($description, $location) !== false && !in_array($location, $locations)) {
            $locations[] = $location;
        }
    }
    
    return $locations ? implode(' · ', array_slice($locations, 0, 3)) : '';
}

function buildQueryString($newParams = []) {
    $params = $_GET;
    foreach ($newParams as $key => $value) {
        $params[$key] = $value;
    }
    
    // Remove empty values
    $params = array_filter($params, function($value) {
        return $value !== '' && $value !== 'all';
    });
    
    return http_build_query($params);
}
?>