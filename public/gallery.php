<?php
// Start session and database connection
session_start();
require_once '../admin/includes/connection.php'; 

// Fetch gallery images from database
$gallery_query = "SELECT * FROM gallery WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC";
$gallery_result = $conn->query($gallery_query);

// Fetch all categories for filtering
$categories_query = "SELECT DISTINCT categories FROM gallery WHERE is_active = 1";
$categories_result = $conn->query($categories_query);
$all_categories = [];

// Extract unique categories from the space-separated categories field
while ($row = $categories_result->fetch_assoc()) {
    $cats = explode(' ', $row['categories']);
    foreach ($cats as $cat) {
        if ($cat && !in_array($cat, $all_categories)) {
            $all_categories[] = $cat;
        }
    }
}
sort($all_categories);

// Handle filtering - FIXED: Sanitize input
$active_filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : 'all';
$filtered_gallery = [];

if ($active_filter !== 'all') {
    // Filter gallery items by category
    while ($item = $gallery_result->fetch_assoc()) {
        $item_categories = explode(' ', $item['categories']);
        if (in_array($active_filter, $item_categories)) {
            $filtered_gallery[] = $item;
        }
    }
} else {
    // Reset pointer to beginning for all items
    $gallery_result->data_seek(0);
    while ($item = $gallery_result->fetch_assoc()) {
        $filtered_gallery[] = $item;
    }
}

// Get total counts
$total_count = count($filtered_gallery);
$filtered_count = ($active_filter !== 'all') ? $total_count : null;
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
    <title>Kashmir Tour Gallery | Travel Photos by Zubi Tours</title>
    <meta name="description" content="Browse beautiful travel photos of Kashmir tours including Srinagar, Gulmarg, Pahalgam and Sonamarg captured by Zubi Tours & Holidays.">
    <meta name="keywords" content="Kashmir travel photos, Kashmir tour gallery, Srinagar images, Gulmarg photos, Pahalgam pictures">
    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <!-- --==============Favicon =============-- -->
    <link rel="icon" type="image/png" href="../assets/img/zubilogo.jpg" />
    <title>Zubi tours & Holiday - Gallery</title>
    
    <style>
        /* Gallery specific styles */
        .gallery-hero {
            position: relative;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(135deg, rgba(34, 44, 67, 0.9) 0%, rgba(190, 161, 89, 0.8) 100%);
            color: white;
            margin-bottom: 60px;
            overflow: hidden;
        }
        
        .gallery-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/img/bg1.jpg') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 0 20px;
            z-index: 1;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-content p {
            font-size: 1.2rem;
            color: #cbd5e1;
            margin-bottom: 30px;
        }
        
        /* Category Filters - FIXED: Added missing styles */
        .gallery-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }
        
        .filter-btn {
            padding: 12px 24px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-btn:hover {
            border-color: #060e218d;
            color: #2563eb;
        }
        
        .filter-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .filter-btn i {
            font-size: 1.1rem;
        }
        
        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 0 20px;
            margin-bottom: 60px;
        }
        
        .gallery-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .gallery-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-card:hover .gallery-image {
            transform: scale(1.05);
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            padding: 30px 20px 20px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .gallery-card:hover .gallery-overlay {
            transform: translateY(0);
        }
        
        .gallery-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
        }
        
        .gallery-description {
            font-size: 0.9rem;
            color: #cbd5e1;
            margin-bottom: 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .gallery-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }
        
        .category-tag {
            background: rgba(37, 99, 235, 0.9);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* Image counter */
        .image-counter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 0 20px;
        }
        
        .counter-badge {
            margin: 0 auto;
            max-width: 300px;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .counter-badge i {
            color: #2563eb;
        }
        
        /* Lightbox Modal */
        .lightbox-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .lightbox-content {
            max-width: 90%;
            max-height: 90vh;
            position: relative;
        }
        
        .lightbox-image {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 12px;
        }
        
        .lightbox-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            color: white;
        }
        
        .lightbox-info h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: white;
        }
        
        .lightbox-info p {
            color: #cbd5e1;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .lightbox-close {
            position: absolute;
            top: -50px;
            right: 0;
            color: white;
            font-size: 2.5rem;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }
        
        .lightbox-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            transform: translateY(-50%);
        }
        
        .nav-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        .no-images {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        
        .no-images i {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
            opacity: 0.5;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 20px;
            }
            
            .gallery-filters {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .filter-btn {
                white-space: nowrap;
                flex-shrink: 0;
            }
            
            .lightbox-content {
                max-width: 95%;
            }
            
            .nav-btn {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 480px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
        }
        
        /* Loading animation for images */
        .image-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Floating action button */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #ebb625ff;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .floating-btn:hover {
            background: #d8931dff;
            transform: scale(1.1) rotate(90deg);
        }
    </style>
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
        <div class="hero-content">
            <h1>Explore Our Gallery</h1>
            <p>Discover the breathtaking beauty of Kashmir and Ladakh through our curated collection of images</p>
            <div class="counter-badge">
                <i class="ri-image-line"></i>
                <?php if ($filtered_count): ?>
                    Showing <?php echo $filtered_count; ?> of <?php echo $total_count; ?> images in "<?php echo htmlspecialchars(ucfirst($active_filter)); ?>"
                <?php else: ?>
                    <?php echo $total_count; ?> Stunning Images
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Category Filters -->
    <div class="gallery-filters">
        <button class="filter-btn <?php echo ($active_filter === 'all') ? 'active' : ''; ?>" 
                onclick="filterGallery('all')">
            <i class="ri-grid-fill"></i>
            All Images
        </button>
        
        <?php foreach ($all_categories as $category): ?>
            <button class="filter-btn <?php echo ($active_filter === $category) ? 'active' : ''; ?>" 
                    onclick="filterGallery('<?php echo htmlspecialchars($category); ?>')">
                <i class="ri-price-tag-3-line"></i>
                <?php echo htmlspecialchars(ucfirst($category)); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Image Counter -->
    <div class="image-counter">
        <div class="counter-badge">
            <i class="ri-image-line"></i>
            <?php echo $total_count; ?> images
        </div>
        <div class="counter-badge">
            <i class="ri-filter-line"></i>
            Filter: <?php echo ($active_filter === 'all') ? 'All Categories' : htmlspecialchars(ucfirst($active_filter)); ?>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="gallery-grid" id="gallery-container">
        <?php if (count($filtered_gallery) > 0): ?>
            <?php foreach ($filtered_gallery as $index => $item): 
                // FIXED: Correct image path - remove duplicate ../admin
                $image_path = !empty($item['image_path']) ? '../admin/' . $item['image_path'] : '../assets/img/bg2.jpg';
                $categories = explode(' ', $item['categories']);
            ?>
                <div class="gallery-card" 
                     data-index="<?php echo $index; ?>"
                     onclick="openLightbox(<?php echo $index; ?>)">
                    <!-- FIXED: Remove duplicate ../admin from src -->
                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                         class="gallery-image image-loading"
                         onload="this.classList.remove('image-loading')"
                         onerror="this.src='../assets/img/bg2.jpg'; this.classList.remove('image-loading')">
                    
                    <div class="gallery-overlay">
                        <h3 class="gallery-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <?php if ($item['description']): ?>
                            <p class="gallery-description"><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...</p>
                        <?php endif; ?>
                        
                        <div class="gallery-categories">
                            <?php foreach ($categories as $cat): 
                                if ($cat): ?>
                                    <span class="category-tag"><?php echo htmlspecialchars(ucfirst($cat)); ?></span>
                                <?php endif; 
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-images">
                <i class="ri-image-line"></i>
                <h3>No Images Found</h3>
                <p>No images available in the "<?php echo htmlspecialchars(ucfirst($active_filter)); ?>" category.</p>
                <button class="filter-btn active" onclick="filterGallery('all')" style="margin-top: 20px;">
                    <i class="ri-grid-fill"></i> View All Images
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightbox-modal">
        <div class="lightbox-content">
            <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
            
            <div class="lightbox-nav">
                <button class="nav-btn prev-btn" onclick="changeImage(-1)">
                    <i class="ri-arrow-left-line"></i>
                </button>
                <button class="nav-btn next-btn" onclick="changeImage(1)">
                    <i class="ri-arrow-right-line"></i>
                </button>
            </div>
            
            <img id="lightbox-img" class="lightbox-image" src="" alt="">
            
            <div class="lightbox-info">
                <h3 id="lightbox-title"></h3>
                <p id="lightbox-description"></p>
                <div id="lightbox-categories" class="gallery-categories"></div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="ri-arrow-up-line"></i>
    </div>

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
                    <p><i class="ri-map-pin-line"></i> Srinagar, Jammu & Kashmir</p>
                    <p><i class="ri-phone-line"></i> +91 7006296814</p>
                    <p><i class="ri-mail-line"></i> info@zubitours.com</p>
                    <p><i class="ri-time-line"></i> Mon-Sat: 9AM - 6PM</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <span id="getYear"></span> Zubi Tours & Holidays. All rights reserved.</p>
            <p> Powered By <a href="https://irfanmanzoor.in">KRYON</a></p>
        </div>
    </footer>

    <!-- Linking Swiper script -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    
    <script>
        // Gallery data from PHP
        const galleryItems = <?php echo json_encode($filtered_gallery); ?>;
        let currentImageIndex = 0;
        
        // Filter gallery function
        function filterGallery(category) {
            window.location.href = `gallery.php?filter=${encodeURIComponent(category)}`;
        }
        
        // Lightbox functionality
        function openLightbox(index) {
            currentImageIndex = parseInt(index);
            updateLightbox();
            document.getElementById('lightbox-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Add keyboard navigation
            document.addEventListener('keydown', handleKeyboardNavigation);
        }
        
        function closeLightbox() {
            document.getElementById('lightbox-modal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.removeEventListener('keydown', handleKeyboardNavigation);
        }
        
        function changeImage(direction) {
            currentImageIndex += direction;
            
            // Loop around
            if (currentImageIndex < 0) {
                currentImageIndex = galleryItems.length - 1;
            } else if (currentImageIndex >= galleryItems.length) {
                currentImageIndex = 0;
            }
            
            updateLightbox();
        }
        
        function updateLightbox() {
            const item = galleryItems[currentImageIndex];
            if (!item) return;
            
            // FIXED: Correct image path construction
            const imagePath = item.image_path ? '../admin/' + item.image_path : '../assets/img/bg2.jpg';
            
            document.getElementById('lightbox-img').src = imagePath;
            document.getElementById('lightbox-img').alt = item.title || '';
            document.getElementById('lightbox-title').textContent = item.title || '';
            document.getElementById('lightbox-description').textContent = item.description || '';
            
            // Update categories
            const categoriesContainer = document.getElementById('lightbox-categories');
            categoriesContainer.innerHTML = '';
            
            if (item.categories) {
                const categories = item.categories.split(' ');
                categories.forEach(cat => {
                    if (cat.trim()) {
                        const tag = document.createElement('span');
                        tag.className = 'category-tag';
                        tag.textContent = cat.charAt(0).toUpperCase() + cat.slice(1);
                        categoriesContainer.appendChild(tag);
                    }
                });
            }
        }
        
        function handleKeyboardNavigation(e) {
            switch(e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    changeImage(-1);
                    break;
                case 'ArrowRight':
                    changeImage(1);
                    break;
            }
        }
        
        // Close lightbox when clicking outside
        document.getElementById('lightbox-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set current year in footer
            document.getElementById('getYear').textContent = new Date().getFullYear();
            
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
            
            // Image error handling
            document.querySelectorAll('.gallery-image').forEach(img => {
                img.addEventListener('error', function() {
                    this.src = '../assets/img/bg2.jpg';
                    this.classList.remove('image-loading');
                });
            });
        });
    </script>
  </body>
</html>