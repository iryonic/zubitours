<?php
session_start();
require_once '../admin/includes/connection.php'; // Database connection

// Fetch all active gallery images
$sql = "SELECT * FROM gallery WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC";
$result = mysqli_query($conn, $sql);
$galleryItems = [];

if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $galleryItems[] = $row;
  }
}

// Get all unique categories for filter buttons
$categories = [];
foreach ($galleryItems as $item) {
  $itemCategories = explode(' ', $item['categories']);
  foreach ($itemCategories as $cat) {
    if (trim($cat) && !in_array($cat, $categories)) {
      $categories[] = trim($cat);
    }
  }
}
sort($categories); // Sort categories alphabetically
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Zubi tours & Holiday - Gallery</title>

  <!--=============== REMIXICONS ===============-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Masonry layout CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

  <!--=============== CSS ===============-->
  <link rel="stylesheet" href="../assets/css/styles.css" />

  <style>
    
    /* Additional styles for dynamic gallery */
    .no-images {
      text-align: center;
      padding: 60px 20px;
      background: #f8fafc;
      border-radius: 12px;
      margin: 30px;
    }

    .no-images i {
      font-size: 4rem;
      color: #64748b;
      margin-bottom: 20px;
      opacity: 0.5;
    }

    .no-images h3 {
      color: #475569;
      margin-bottom: 10px;
    }

    .no-images p {
      color: #64748b;
      max-width: 400px;
      margin: 0 auto;
    }

    .loading-spinner {
      display: none;
      text-align: center;
      padding: 40px;
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .category-count {
      display: inline-block;
      background: rgba(37, 99, 235, 0.1);
      color: #2563eb;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      margin-left: 8px;
      font-weight: 600;
    }

    .image-loader {
      width: 100%;
      height: 250px;
      background: #f3f4f6;
      border-radius: 8px;
      position: relative;
      overflow: hidden;
    }

    .image-loader::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
      animation: loading 1.5s infinite;
    }

    @keyframes loading {
      0% {
        left: -100%;
      }

      100% {
        left: 100%;
      }
    }

    .image-stats {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      z-index: 2;
      display: none;
    }

    .masonry-item:hover .image-stats {
      display: block;
    }

    .admin-edit-btn {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(37, 99, 235, 0.9);
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      text-decoration: none;
      z-index: 2;
      display: none;
    }

    .masonry-item:hover .admin-edit-btn {
      display: block;
    }

    <?php if (isset($_SESSION['admin_id'])): ?>.admin-edit-btn {
      display: block;
    }

    <?php endif; ?>
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
    <div class="section-header">
      <h2>Our Gallery</h2>
      <p>Showcasing the beauty of Kashmir and Ladakh</p>
      <p style="margin-top: 10px; font-size: 0.9rem; color: #64748b;">
        <i class="ri-image-line"></i> <?php echo count($galleryItems); ?> Photos Available
      </p>
    </div>

    <div class="gallery-filters">
      <button class="filter-btn active" data-filter="all">
        All Photos <span class="category-count"><?php echo count($galleryItems); ?></span>
      </button>

      <?php foreach ($categories as $category):
        // Count items in this category
        $count = 0;
        foreach ($galleryItems as $item) {
          if (strpos($item['categories'], $category) !== false) {
            $count++;
          }
        }
        if ($count > 0): ?>
          <button class="filter-btn" data-filter="<?php echo htmlspecialchars($category); ?>">
            <?php echo ucfirst($category); ?> <span class="category-count"><?php echo $count; ?></span>
          </button>
      <?php endif;
      endforeach; ?>
    </div>
  </section>

  <!-- Masonry Gallery -->
  <section class="masonry-gallery-section">
    <?php if (empty($galleryItems)): ?>
      <div class="no-images">
        <i class="ri-image-line"></i>
        <h3>No Gallery Images Found</h3>
        <p>Our photo gallery is currently being updated. Please check back soon!</p>
      </div>
    <?php else: ?>
      <div class="masonry-grid" id="masonryGrid">
        <?php foreach ($galleryItems as $index => $item):
          $itemCategories = explode(' ', $item['categories']);
          $categoryClass = implode(' ', $itemCategories);
        ?>
          <div class="masonry-item" data-category="<?php echo $categoryClass; ?>" data-id="<?php echo $item['id']; ?>">
            <?php if (isset($_SESSION['admin_id'])): ?>
              <a href="../admin/pages/manage-gallery.php" class="admin-edit-btn" target="_blank">
                <i class="ri-edit-line"></i> Edit
              </a>
            <?php endif; ?>

            <span class="image-stats">
              <i class="ri-eye-line"></i> #<?php echo $item['display_order']; ?>
            </span>

            <!-- In the gallery loop section -->
            <a href="../admin/uploads/gallery/<?php echo htmlspecialchars($item['image_path']); ?>"
              data-fancybox="gallery"
              data-caption="<strong><?php echo htmlspecialchars($item['title']); ?></strong><br><?php echo htmlspecialchars($item['description']); ?>">
              <img loading="lazy"
                src="../admin/uploads/gallery/<?php echo htmlspecialchars(basename($item['image_path'])); ?>"
                alt="<?php echo htmlspecialchars($item['title']); ?>"
                onerror="this.src='https://images.unsplash.com/photo-1552733407-5d5c46c3bb3b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'">
              <!-- ... rest of code ... -->
            </a>
            <div class="image-overlay">
              <h3><?php echo htmlspecialchars($item['title']); ?></h3>
              <?php if ($item['description']): ?>
                <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?><?php echo strlen($item['description']) > 100 ? '...' : ''; ?></p>
              <?php endif; ?>
              <div style="margin-top: 8px; font-size: 0.8rem;">
                <?php foreach ($itemCategories as $cat):
                  if (trim($cat)): ?>
                    <span style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px; margin-right: 4px;">
                      <?php echo ucfirst(trim($cat)); ?>
                    </span>
                <?php endif;
                endforeach; ?>
              </div>
            </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
      <div class="spinner"></div>
      <p>Loading more images...</p>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="gallery-cta">
    <h2>Experience the Beauty Yourself</h2>
    <p>Let us help you create unforgettable memories in the paradise of Kashmir and the majestic landscapes of Ladakh</p>
    <a href="./packages.php" class="cta-button">Explore Our Packages</a>
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
          <li><a href="../index.php">Home</a></li>
          <li><a href="./about.php">About Us</a></li>
          <li><a href="./destinations.php">Destinations</a></li>
          <li><a href="./packages.php">Packages</a></li>
          <li><a href="./gallery.php">Gallery</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Services</h4>
        <ul>
          <li><a href="./packages.php">Tour Packages</a></li>
          <li><a href="./car-rentals.php">Car Rentals</a></li>
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

  <!-- Masonry Layout -->
  <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
  <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>

  <!--=============== MAIN JS ===============-->
  <script src="../assets/js/main.js"></script>

  <script>
    // Wait for page to load
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize masonry layout
      var $grid = $('#masonryGrid');
      if ($grid.length) {
        // Use imagesLoaded to wait for all images
        $grid.imagesLoaded(function() {
          $grid.masonry({
            itemSelector: '.masonry-item',
            columnWidth: '.masonry-item',
            percentPosition: true,
            transitionDuration: '0.3s'
          });

          // Animate items on load
          $('.masonry-item').each(function(index) {
            var $item = $(this);
            setTimeout(function() {
              $item.css({
                opacity: 1,
                transform: 'translateY(0)'
              });
            }, index * 100);
          });
        });
      }

      // Filter functionality
      const filterButtons = document.querySelectorAll('.filter-btn');
      const galleryItems = document.querySelectorAll('.masonry-item');

      filterButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all buttons
          filterButtons.forEach(btn => btn.classList.remove('active'));

          // Add active class to clicked button
          this.classList.add('active');

          const filterValue = this.getAttribute('data-filter');

          // Show loading spinner
          document.getElementById('loadingSpinner').style.display = 'block';

          // Small delay for smooth animation
          setTimeout(() => {
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

            // Re-layout masonry after filtering
            if ($grid.length) {
              $grid.masonry('layout');
            }

            // Hide loading spinner
            document.getElementById('loadingSpinner').style.display = 'none';

            // Update URL hash without reloading
            history.replaceState(null, null, '#filter-' + filterValue);
          }, 300);
        });
      });

      // Check URL hash for filter on page load
      if (window.location.hash) {
        const filter = window.location.hash.replace('#filter-', '');
        const button = document.querySelector(`[data-filter="${filter}"]`);
        if (button) {
          button.click();
        }
      }

      // Initialize fancybox with custom settings
      $('[data-fancybox="gallery"]').fancybox({
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
        transitionDuration: 800,
        clickContent: "zoom",
        clickSlide: "close",
        caption: function(instance, item) {
          return $(this).data('caption');
        },
        afterLoad: function(instance, current) {
          // Add image info to caption
          var imageId = current.opts.$orig.closest('.masonry-item').data('id');
          console.log('Viewing image ID:', imageId);

          // You could track image views here with AJAX
          
          $.ajax({
              url: 'track-view.php',
              method: 'POST',
              data: { image_id: imageId },
              error: function() {
                  console.log('View tracking failed');
              }
          });
          
        }
      });

      // Lazy loading for images
      const lazyImages = document.querySelectorAll('img[loading="lazy"]');
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src || img.src;
            img.classList.add('loaded');
            observer.unobserve(img);
          }
        });
      }, {
        rootMargin: '50px'
      });

      lazyImages.forEach(img => imageObserver.observe(img));

      // Infinite scroll (optional feature)
      let isLoading = false;
      let page = 1;
      const totalImages = <?php echo count($galleryItems); ?>;

      window.addEventListener('scroll', function() {
        // Only implement if you have pagination in the future
        
        if (isLoading || totalImages <= page * 12) return;
        
        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight - 500;
        
        if (scrollPosition >= pageHeight) {
            loadMoreImages();
        }
        
      });

      function loadMoreImages() {
        isLoading = true;
        document.getElementById('loadingSpinner').style.display = 'block';

        // Simulate AJAX call for more images
        setTimeout(() => {
          // In real implementation, fetch more images from server
          // $.ajax({
          //     url: 'load-more-gallery.php',
          //     method: 'GET',
          //     data: { page: page + 1 },
          //     success: function(data) {
          //         // Append new images
          //         // Update masonry
          //         page++;
          //     },
          //     complete: function() {
          //         isLoading = false;
          //         document.getElementById('loadingSpinner').style.display = 'none';
          //     }
          // });

          isLoading = false;
          document.getElementById('loadingSpinner').style.display = 'none';
        }, 1000);
      }

      // Update year in footer
      document.getElementById('getYear').textContent = new Date().getFullYear();

      // Add smooth scroll for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;

          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            window.scrollTo({
              top: targetElement.offsetTop - 100,
              behavior: 'smooth'
            });
          }
        });
      });

      // Image error handler
      document.querySelectorAll('.masonry-item img').forEach(img => {
        img.addEventListener('error', function() {
          this.src = 'https://images.unsplash.com/photo-1552733407-5d5c46c3bb3b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80';
        });
      });

      // Add keyboard navigation for fancybox
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          $.fancybox.close();
        }
      });

      // Share functionality for fancybox
      $.fancybox.defaults.btnTpl.share = '<button data-fancybox-share class="fancybox-button fancybox-button--share" title="Share">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>' +
        '</button>';

      // Handle share button click
      $(document).on('click', '[data-fancybox-share]', function() {
        var current = $.fancybox.getInstance().current;
        var imageUrl = current.opts.$orig[0].href;
        var title = current.opts.$orig.data('caption') || document.title;

        // Create share modal
        var shareHtml = `
                    <div class="share-modal" style="background: white; padding: 20px; border-radius: 10px; max-width: 400px;">
                        <h3 style="margin-bottom: 15px;">Share this image</h3>
                        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(imageUrl)}" 
                               target="_blank" 
                               style="background: #1877f2; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
                                <i class="ri-facebook-fill"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(imageUrl)}&text=${encodeURIComponent(title)}" 
                               target="_blank" 
                               style="background: #1da1f2; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
                                <i class="ri-twitter-fill"></i> Twitter
                            </a>
                        </div>
                        <div style="margin-top: 15px;">
                            <input type="text" value="${imageUrl}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" readonly>
                            <button onclick="navigator.clipboard.writeText('${imageUrl}')" style="margin-top: 10px; padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                Copy Link
                            </button>
                        </div>
                    </div>
                `;

        $.fancybox.open({
          src: shareHtml,
          type: 'html'
        });
      });
    });

    // Handle loader
    window.addEventListener('load', function() {
      setTimeout(function() {
        document.getElementById('loader').classList.add('hidden');
      }, 1000);
    });
  </script>
</body>

</html>