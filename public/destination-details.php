<?php
require_once '../admin/includes/connection.php';

if (isset($_GET['id'])) {
    $identifier = $_GET['id'];
    $where_clause = "d.id = ?";
} elseif (isset($_GET['slug'])) {
    $identifier = $_GET['slug'];
    $where_clause = "d.slug = ?";
} else {
    header('Location: destinations.php');
    exit();
}

// Fetch destination details
$dest_query = $conn->prepare("
    SELECT d.*, 
           GROUP_CONCAT(DISTINCT di.image_path ORDER BY di.is_primary DESC, di.id ASC) as all_images
    FROM destinations d 
    LEFT JOIN destination_images di ON d.id = di.destination_id 
    WHERE $where_clause AND d.is_active = 1
    GROUP BY d.id
");
$param_type = is_numeric($identifier) && isset($_GET['id']) ? "i" : "s";
$dest_query->bind_param($param_type, $identifier);
$dest_query->execute();
$dest_result = $dest_query->get_result();

if ($dest_result->num_rows === 0) {
    header('Location: destinations.php');
    exit();
}

$destination = $dest_result->fetch_assoc();
$destination_id = $destination['id'];

// Decode JSON fields
$best_seasons = json_decode($destination['best_seasons'], true) ?: [];
$dest_itinerary = json_decode($destination['itinerary'] ?? '[]', true) ?: [];
$dest_inclusions = json_decode($destination['inclusions'] ?? '[]', true) ?: [];
$dest_exclusions = json_decode($destination['exclusions'] ?? '[]', true) ?: [];
$dest_faqs = json_decode($destination['faqs'] ?? '[]', true) ?: [];
$dest_duration = $destination['duration_days'] ?? 0;
$dest_price = $destination['price_per_person'] ?? 0;
$dest_max_people = $destination['max_people'] ?? 0;
$dest_accommodation = $destination['accommodation_type'] ?? '';

// Get all images
$destination_images = [];
if ($destination['all_images']) {
    $destination_images = explode(',', $destination['all_images']);
}

// Fetch highlights
$highlights_query = $conn->prepare("SELECT * FROM destination_highlights WHERE destination_id = ? ORDER BY display_order ASC");
$highlights_query->bind_param("i", $destination_id);
$highlights_query->execute();
$highlights = $highlights_query->get_result();

// Fetch activities
$activities_query = $conn->prepare("SELECT * FROM destination_activities WHERE destination_id = ?");
$activities_query->bind_param("i", $destination_id);
$activities_query->execute();
$activities = $activities_query->get_result();

// Fetch nearby attractions
$nearby_query = $conn->prepare("SELECT * FROM nearby_attractions WHERE destination_id = ?");
$nearby_query->bind_param("i", $destination_id);
$nearby_query->execute();
$nearby = $nearby_query->get_result();

// Fetch tips
$tips_query = $conn->prepare("SELECT * FROM destination_tips WHERE destination_id = ?");
$tips_query->bind_param("i", $destination_id);
$tips_query->execute();
$tips = $tips_query->get_result();

// Handle Quick Inquiry Form Submission
$form_message = '';
$form_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $adults = intval($_POST['adults'] ?? 0);
    $children = intval($_POST['children'] ?? 0);
    $travel_date = $_POST['travel_date'] ?? null;
    $message = "Inquiry for: " . $destination['destination_name'] . "\n" . trim($_POST['message'] ?? '');
    $subject = "Destination Inquiry: " . $destination['destination_name'];
    
    if (!empty($name) && !empty($email)) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO contact_messages (destination_id, name, email, phone, adults, children, travel_date, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiisssss", $destination_id, $name, $email, $phone, $adults, $children, $travel_date, $subject, $message, $ip_address, $user_agent);
        
        if ($stmt->execute()) {
            $form_message = "Thank you! Our travel expert will contact you shortly.";
            $form_success = true;
        } else {
            $form_message = "Sorry, something went wrong. Please try again.";
        }
    }
}

// Helper function for robust image path checking
function get_image_path($path) {
    if (empty($path)) return BASE_URL . 'assets/img/bg1.jpg';
    
    // If it's already an absolute path or starts with http, return it
    if (strpos($path, 'http') === 0) return $path;

    // Check if it's in the upload directory
    return BASE_URL . 'admin/upload/' . ltrim($path, '/');
}

// Fetch THE primary package for this destination to show its full details (Landing Page Mode)
$dest_name_search = "%" . $destination['destination_name'] . "%";
$pkg_stmt = $conn->prepare("
    SELECT p.*, 
           GROUP_CONCAT(DISTINCT pi.image_path ORDER BY pi.is_primary DESC, pi.id ASC) as all_pkg_images
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id 
    WHERE p.is_active = 1 AND (p.package_name LIKE ? OR p.description LIKE ?)
    GROUP BY p.id
    ORDER BY p.is_featured DESC, p.rating DESC 
    LIMIT 1
");
$pkg_stmt->bind_param("ss", $dest_name_search, $dest_name_search);
$pkg_stmt->execute();
$pkg_result = $pkg_stmt->get_result();

$package = null;
if ($pkg_result->num_rows > 0) {
    $package = $pkg_result->fetch_assoc();
    // Decode JSON fields
    $package['highlights'] = json_decode($package['highlights'], true) ?: [];
    $package['inclusions'] = json_decode($package['inclusions'], true) ?: [];
    $package['exclusions'] = json_decode($package['exclusions'], true) ?: [];
    $package['faqs'] = json_decode($package['faqs'], true) ?: [];
    $package['itinerary'] = json_decode($package['itinerary'], true) ?: [];
}

// Override package data with destination-specific data if available
$itinerary = !empty($dest_itinerary) ? $dest_itinerary : ($package['itinerary'] ?? []);
$inclusions = !empty($dest_inclusions) ? $dest_inclusions : ($package['inclusions'] ?? []);
$exclusions = !empty($dest_exclusions) ? $dest_exclusions : ($package['exclusions'] ?? []);
$faqs = !empty($dest_faqs) ? $dest_faqs : ($package['faqs'] ?? []);
$duration_days = $dest_duration > 0 ? $dest_duration : ($package['duration_days'] ?? 0);
$price_per_person = $dest_price > 0 ? $dest_price : ($package['price_per_person'] ?? 0);
$max_people = $dest_max_people > 0 ? $dest_max_people : ($package['max_people'] ?? 0);
$accommodation_type = !empty($dest_accommodation) ? $dest_accommodation : ($package['accommodation_type'] ?? '');
$package_badge = !empty($destination['badge']) ? $destination['badge'] : ($package['badge'] ?? '');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($destination['destination_name']); ?> - Explore Kashmir & Ladakh | Zubi Tours</title>
    
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/img/zubilogo.jpg" />
    
    <!--=============== TAILWIND CSS ===============-->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e8862a',
                        'primary-dark': '#d1751f',
                        'accent-yellow': '#f9de73',
                        'slate-dark': '#0f172a',
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '3rem',
                    },
                    boxShadow: {
                        'premium': '0 20px 50px rgba(0,0,0,0.08)',
                        'hover': '0 30px 60px rgba(0,0,0,0.12)',
                    }
                }
            }
        }
    </script>
    
    <!--=============== Swiper & Lightbox ===============-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/fslightbox@3.4.1/index.min.js"></script>


    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css" />

    <style>
        /* Ultra-Premium Destination Detail Styles */
        :root {
            --accent-gradient: linear-gradient(135deg, #f9de73 0%, #e8862a 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.4);
            --shadow-premium: 0 20px 50px rgba(0,0,0,0.08);
            --text-main: #1e293b;
            --text-light: #64748b;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Custom Tailwind Supplements */
        .glass-nav { background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .hero-gradient { background: linear-gradient(to top, rgba(0,0,0,0.8), transparent 60%); }
        .accent-gradient { background: linear-gradient(135deg, #f9de73 0%, #e8862a 100%); }
        
        /* Swiper Fixes */
        .gallery-main { height: 550px; }
        .gallery-thumbs { height: 110px; }
        .gallery-thumbs .swiper-slide { opacity: 0.4; cursor: pointer; transition: 0.3s; filter: grayscale(1); }
        .gallery-thumbs .swiper-slide-thumb-active { opacity: 1; filter: grayscale(0); border: 2px solid #e8862a; }
        
        .header-fixed {
            background: rgba(255,255,255,0.9) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            position: fixed !important;
            top: 0; left: 0; width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* Prevent footer overlap */
        .main-wrapper { padding-bottom: 100px; }
        footer { margin-top: 0 !important; }

        /* Itinerary & Package Details Styles */
        .itinerary-tab.active {
            background: #e8862a;
            color: white;
            box-shadow: 0 10px 20px rgba(232, 134, 42, 0.2);
        }
        .itinerary-content { display: none; }
        .itinerary-content.active { display: block; animation: fadeIn 0.5s ease; }
        
        .faq-item { border: 1px solid #e2e8f0; border-radius: 1.5rem; overflow: hidden; transition: all 0.3s ease; }
        .faq-item.active { border-color: #e8862a; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease; opacity: 0; }
        .faq-item.active .faq-answer { max-height: 500px; padding-bottom: 2rem; opacity: 1; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Animation */
        .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1); }
        .reveal-up.active { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans overflow-x-hidden">

    <!--==================== HEADER ====================-->
    <?php include '../admin/includes/navbar.php'; ?>

    <main class="main-wrapper">
        <!-- Modern Hero -->
        <section class="relative h-[85vh] min-h-[500px] flex items-center justify-center overflow-hidden bg-black">
            <div class="hero-bg absolute inset-0 z-0">
                <?php $primary_image = !empty($destination_images) ? get_image_path($destination_images[0]) : '../assets/img/bg1.jpg'; ?>
                <img src="<?php echo $primary_image; ?>" class="w-full h-full object-cover filter brightness-[0.6] saturate-[1.2] scale-110" alt="<?php echo htmlspecialchars($destination['destination_name']); ?>">
            </div>
            <div class="hero-gradient absolute inset-0 z-[1]"></div>
            
            <div class="relative z-[2] max-w-5xl px-6 text-center text-white">
                <?php if ($package && !empty($package['badge'])): ?>
                <span class="bg-accent-yellow text-primary-dark px-6 py-2 rounded-full font-black uppercase tracking-[0.2em] text-[10px] mb-4 inline-block shadow-lg animate-pulse">
                    <?php echo htmlspecialchars($package['badge']); ?>
                </span>
                <?php endif; ?>
                <span class="accent-gradient inline-flex items-center gap-2 px-6 py-2 rounded-full font-bold uppercase tracking-widest text-xs mb-8 shadow-lg">
                    <i class="ri-map-2-line"></i> <?php echo htmlspecialchars($destination['region']); ?>
                </span>
                <h1 class="text-4xl md:text-7xl lg:text-8xl font-black mb-6 leading-none tracking-tight drop-shadow-2xl">
                    <?php echo htmlspecialchars($destination['destination_name']); ?>
                </h1>
                <!-- Hero Badges -->
                <div class="flex flex-wrap items-center justify-center gap-4 mb-8">
                    <?php if ($package_badge): ?>
                    <span class="bg-accent-yellow text-primary-dark px-6 py-2 rounded-full font-black uppercase tracking-[0.2em] text-[10px] shadow-lg animate-pulse">
                        <?php echo htmlspecialchars($package_badge); ?>
                    </span>
                    <?php endif; ?>
                    
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 px-6 py-3 rounded-full flex items-center gap-3 transition hover:bg-white/20">
                        <i class="ri-time-line text-accent-yellow text-xl"></i>
                        <span class="font-medium"><?php echo $duration_days; ?> Days</span>
                    </div>

         
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 px-6 py-3 rounded-full flex items-center gap-3 transition hover:bg-white/20">
                        <i class="ri-star-fill text-accent-yellow text-xl"></i>
                        <span class="font-medium"><?php echo number_format($destination['rating'], 1); ?> Rating</span>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-[2] cursor-pointer animate-bounce" onclick="window.scrollTo({top: window.innerHeight * 0.8, behavior: 'smooth'})">
                <i class="ri-arrow-down-s-line text-5xl text-white/80"></i>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 -mt-12 sm:-mt-24 relative z-10 flex flex-col lg:flex-row gap-8">
            <!-- Main Content Area -->
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-3xl md:rounded-[2.5rem] p-6 md:p-12 shadow-premium space-y-12 md:space-y-20">
                    <!-- Overview Section -->
                    <section class="reveal-up active">
                        <div class="mb-8">
                            <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Destination Insight</span>
                            <h2 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 tracking-tight">Discover <?php echo htmlspecialchars($destination['destination_name']); ?></h2>
                        </div>
                        <p class="text-base sm:text-lg lg:text-xl leading-[1.8] text-slate-600">
                            <?php 
                            $full_desc = !empty($destination['detailed_description']) ? $destination['detailed_description'] : $destination['short_description'];
                            echo nl2br(htmlspecialchars($full_desc)); 
                            ?>
                        </p>
                    </section>

                    <!-- Destination Highlights -->
                    <?php if ($highlights->num_rows > 0): ?>
                    <section class="reveal-up">
                        <div class="mb-10">
                            <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Experiences</span>
                            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Destination Highlights</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php while ($h = $highlights->fetch_assoc()): ?>
                            <div class="bg-white p-6 md:p-8 rounded-2xl md:rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-hover hover:-translate-y-1 transition-all group">
                                <div class="flex gap-6 items-start">
                                    <div class="h-14 w-14 accent-gradient rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg group-hover:scale-110 transition-transform">
                                        <i class="<?php echo $h['icon'] ?: 'ri-check-line'; ?>"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black mb-2 text-slate-900"><?php echo htmlspecialchars($h['title']); ?></h3>
                                        <p class="text-slate-500 leading-relaxed"><?php echo htmlspecialchars($h['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Package Highlights -->
                    <?php if ($package && !empty($package['highlights'])): ?>
                    <section class="reveal-up">
                        <div class="mb-10 text-center">
                            <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Why Book This Tour</span>
                            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Package Highlights</h2>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php foreach ($package['highlights'] as $index => $h): 
                                $icons = ['ri-hotel-line', 'ri-restaurant-line', 'ri-car-line', 'ri-guide-line', 'ri-camera-line', 'ri-landscape-line', 'ri-heart-line', 'ri-shield-flash-line'];
                                $icon = $icons[$index % count($icons)];
                            ?>
                            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 text-center group hover:bg-white hover:shadow-hover transition-all">
                                <div class="h-12 w-12 bg-white rounded-xl flex items-center justify-center text-primary text-2xl mx-auto mb-4 shadow-sm group-hover:rotate-12 transition-transform">
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <h4 class="font-black text-slate-900 mb-1"><?php echo htmlspecialchars($h['title']); ?></h4>
                                <p class="text-xs text-slate-500 font-bold"><?php echo htmlspecialchars($h['description']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Dynamic Swiper Gallery -->
                    <?php 
                    $all_display_images = $destination_images;
                    if ($package && !empty($package['all_pkg_images'])) {
                        $pkg_imgs = explode(',', $package['all_pkg_images']);
                        // Filter out empty strings and prefix with path if needed
                        foreach($pkg_imgs as $pi) {
                            if (!empty($pi) && !in_array($pi, $all_display_images)) {
                                $all_display_images[] = $pi;
                            }
                        }
                    }
                    ?>
                    <?php if (count($all_display_images) > 0): ?>
                    <section class="reveal-up">
                        <div class="mb-10">
                            <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Visual Journey</span>
                            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Visual Experiences</h2>
                        </div>
                        <div class="space-y-4">
                            <div class="swiper gallery-main rounded-2xl md:rounded-[3rem] shadow-premium group/gallery">
                                <div class="swiper-wrapper">
                                    <?php foreach ($all_display_images as $img): ?>
                                    <div class="swiper-slide h-[300px] sm:h-[450px] md:h-[600px] lg:h-[700px]">
                                        <a data-fslightbox="gallery" href="<?php echo get_image_path($img); ?>" class="block w-full h-full">
                                            <img src="<?php echo get_image_path($img); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover/gallery:scale-105" alt="Gallery Image">
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="swiper-button-next text-white drop-shadow-md"></div>
                                <div class="swiper-button-prev text-white drop-shadow-md"></div>
                            </div>
                            <div class="swiper gallery-thumbs px-2">
                                <div class="swiper-wrapper">
                                    <?php foreach($all_display_images as $img): ?>
                                    <div class="swiper-slide rounded-xl overflow-hidden">
                                        <img src="<?php echo get_image_path($img); ?>" class="w-full h-full object-cover">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Activities -->
                    <?php if ($activities->num_rows > 0): ?>
                    <section class="reveal-up">
                        <div class="mb-10">
                            <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Things to do</span>
                            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Thrilling Activities</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php while ($a = $activities->fetch_assoc()): ?>
                            <div class="bg-slate-50 p-6 md:p-8 rounded-2xl md:rounded-[2rem] border border-slate-100 transition-all hover:bg-white hover:shadow-hover group relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-full h-1.5 accent-gradient opacity-0 transition-opacity group-hover:opacity-100"></div>
                                <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-6 group-hover:scale-110 transition-transform">
                                    <i class="<?php echo $a['icon'] ?: 'ri-direction-line text-blue-500'; ?>"></i>
                                </div>
                                <h3 class="text-xl font-black mb-3 text-slate-900"><?php echo htmlspecialchars($a['activity_name']); ?></h3>
                                <p class="text-slate-500 leading-relaxed"><?php echo htmlspecialchars($a['description']); ?></p>
                                <div class="mt-4 flex items-center justify-between text-sm font-bold">
                                    <span class="text-primary"><i class="ri-dashboard-3-line"></i> <?php echo ucfirst($a['difficulty_level']); ?></span>
                                    <span class="text-slate-400"><i class="ri-time-line"></i> <?php echo $a['duration_hours']; ?> Hrs</span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Package Full Details (Landing Page Sections) -->
                    <?php if (!empty($itinerary) || !empty($inclusions) || !empty($exclusions) || !empty($faqs)): ?>
                        <!-- Itinerary Section -->
                        <?php if (!empty($itinerary)): ?>
                        <section class="reveal-up" id="itinerary">
                            <div class="mb-10">
                                <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Plan of Action</span>
                                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Day-wise Itinerary</h2>
                            </div>
                            <div class="space-y-8">
                                <div class="flex gap-2 overflow-x-auto pb-4 scrollbar-hide">
                                    <?php foreach ($itinerary as $index => $day): ?>
                                    <button onclick="switchDay(<?php echo $index; ?>)" class="itinerary-tab whitespace-nowrap px-4 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-black text-[10px] sm:text-sm uppercase transition-all <?php echo $index === 0 ? 'active' : 'bg-slate-100 text-slate-400'; ?>" data-day-btn="<?php echo $index; ?>">
                                        Day <?php echo $day['day']; ?>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="itinerary-contents">
                                    <?php foreach ($itinerary as $index => $day): ?>
                                    <div class="itinerary-content <?php echo $index === 0 ? 'active' : ''; ?>" data-day-content="<?php echo $index; ?>">
                                        <div class="bg-slate-50 p-6 md:p-12 rounded-3xl md:rounded-[2.5rem] border border-slate-100">
                                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-4">
                                                        <span class="h-12 w-12 accent-gradient rounded-xl flex items-center justify-center text-white text-xl"><?php echo $day['day']; ?></span>
                                                        <?php echo htmlspecialchars($day['title']); ?>
                                                    </h3>
                                                    <p class="text-base text-slate-600 leading-relaxed mb-8">
                                                        <?php echo nl2br(htmlspecialchars($day['description'])); ?>
                                                    </p>
                                                    
                                                    <?php if (!empty($day['activities'])): ?>
                                                    <div class="space-y-4">
                                                        <h4 class="text-sm font-black uppercase tracking-widest text-primary">Today's Highlights</h4>
                                                        <div class="grid grid-cols-1 gap-4">
                                                            <?php foreach ($day['activities'] as $act): ?>
                                                            <div class="bg-white p-6 rounded-2xl border border-slate-100 flex gap-6 items-center group hover:border-primary/30 transition-colors">
                                                                <div class="text-primary font-black text-sm whitespace-nowrap bg-primary/10 px-4 py-2 rounded-lg">
                                                                    <?php echo htmlspecialchars($act['time']); ?>
                                                                </div>
                                                                <div class="text-slate-700 font-bold">
                                                                    <?php echo htmlspecialchars($act['description']); ?>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </section>
                        <?php endif; ?>

                        <!-- Inclusions & Exclusions -->
                        <section class="reveal-up grid grid-cols-1 md:grid-cols-2 gap-12" id="in-ex">
                            <?php if (!empty($inclusions)): ?>
                            <div>
                                <div class="mb-10">
                                    <span class="text-green-500 font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">What's Covered</span>
                                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Package Inclusions</h2>
                                </div>
                                <div class="space-y-4">
                                    <?php foreach ($inclusions as $inc): ?>
                                    <div class="flex gap-4 items-start p-4 rounded-2xl bg-green-50/50 border border-green-100">
                                        <i class="ri-checkbox-circle-fill text-green-500 text-xl"></i>
                                        <span class="text-slate-700 font-bold"><?php echo htmlspecialchars($inc); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($exclusions)): ?>
                            <div>
                                <div class="mb-10">
                                    <span class="text-red-500 font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Not Included</span>
                                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Package Exclusions</h2>
                                </div>
                                <div class="space-y-4">
                                    <?php foreach ($exclusions as $exc): ?>
                                    <div class="flex gap-4 items-start p-4 rounded-2xl bg-red-50/50 border border-red-100">
                                        <i class="ri-close-circle-fill text-red-500 text-xl"></i>
                                        <span class="text-slate-700 font-bold"><?php echo htmlspecialchars($exc); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </section>

                        <!-- Frequently Asked Questions -->
                        <?php if (!empty($faqs)): ?>
                        <section class="reveal-up" id="faqs">
                            <div class="mb-10 text-center">
                                <span class="text-primary font-black uppercase tracking-widest text-xs sm:text-sm block mb-2">Common Queries</span>
                                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Traveler's FAQ</h2>
                            </div>
                            <div class="max-w-4xl mx-auto space-y-4">
                                <?php foreach ($faqs as $index => $faq): ?>
                                <div class="faq-item group" data-faq="<?php echo $index; ?>">
                                    <button onclick="toggleFaq(<?php echo $index; ?>)" class="w-full text-left p-8 flex justify-between items-center transition-colors">
                                        <span class="text-xl font-black text-slate-900"><?php echo htmlspecialchars($faq['question']); ?></span>
                                        <i class="ri-add-line text-2xl text-primary transition-transform duration-300"></i>
                                    </button>
                                    <div class="faq-answer px-8">
                                        <p class="text-lg text-slate-600 leading-relaxed">
                                            <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Enhanced Sidebar -->
            <aside class="w-full lg:w-[400px] flex-shrink-0">
                <div class="sticky top-28 space-y-6">
                    <!-- Stats Card -->
                    <div class="bg-white/80 backdrop-blur-2xl border border-white p-6 md:p-10 rounded-3xl md:rounded-[2.5rem] shadow-premium">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="accent-gradient h-12 w-12 rounded-xl flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="ri-information-line"></i>
                            </div>
                            <h3 class="text-2xl font-black">Travel DNA</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold text-sm">Base Price</span>
                                <span class="text-primary font-black text-sm sm:text-lg">₹<?php echo number_format($price_per_person); ?> <span class="text-xs text-slate-400 font-normal">/ person</span></span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold text-sm">Region</span>
                                <span class="text-slate-900 font-black text-sm sm:text-base"><?php echo ucfirst($destination['region']); ?></span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold text-sm">Duration</span>
                                <span class="text-slate-900 font-black text-sm sm:text-base"><?php echo $duration_days; ?> Days</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold text-sm">Stay Style</span>
                                <span class="text-slate-900 font-black text-sm sm:text-base"><?php echo !empty($accommodation_type) ? $accommodation_type : 'Standard Hotels'; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold text-sm">Capacity</span>
                                <span class="text-slate-900 font-black text-sm sm:text-base">Up to <?php echo $max_people; ?> Persons</span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold text-sm">Nature</span>
                                <span class="text-slate-900 font-black text-sm sm:text-base"><?php echo ucfirst($destination['destination_type']); ?></span>
                            </div>
                            <div class="flex justify-between items-center py-4">
                                <span class="text-slate-500 font-bold text-sm">Best Months</span>
                                <span class="text-slate-900 font-black text-sm sm:text-base text-right"><?php echo implode(', ', array_map('ucfirst', $best_seasons)); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing/Call Card -->
                    <div class="bg-slate-900 text-white p-6 md:p-10 rounded-3xl md:rounded-[2.5rem] shadow-premium relative overflow-hidden group">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/20 blur-[60px] rounded-full"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl font-black mb-6">Quick Inquiry</h3>
                            
                            <?php if ($form_success): ?>
                                <div class="bg-green-500/20 text-green-400 p-4 rounded-2xl border border-green-500/30 mb-6 text-sm font-bold">
                                    <i class="ri-checkbox-circle-line mr-2"></i> <?php echo $form_message; ?>
                                </div>
                            <?php elseif ($form_message): ?>
                                <div class="bg-red-500/20 text-red-400 p-4 rounded-2xl border border-red-500/30 mb-6 text-sm font-bold">
                                    <i class="ri-error-warning-line mr-2"></i> <?php echo $form_message; ?>
                                </div>
                            <?php endif; ?>

                            <form action="#quick-inquiry" method="POST" id="quick-inquiry" class="space-y-4">
                                <input type="hidden" name="submit_inquiry" value="1">
                                <div>
                                    <input type="text" name="name" placeholder="Your Name" required class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-sm focus:border-primary focus:bg-white/10 transition outline-none">
                                </div>
                                <div>
                                    <input type="email" name="email" placeholder="Email Address" required class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-sm focus:border-primary focus:bg-white/10 transition outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest block mb-1">Adults</label>
                                        <select name="adults" id="sidebar_adults" onchange="updateSidebarSummary()" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-primary focus:bg-white/10 transition outline-none appearance-none">
                                            <?php for($i=1; $i<=10; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo $i==2 ? 'selected' : ''; ?> class="bg-slate-900"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest block mb-1">Children</label>
                                        <select name="children" id="sidebar_children" onchange="updateSidebarSummary()" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-primary focus:bg-white/10 transition outline-none appearance-none">
                                            <?php for($i=0; $i<=5; $i++): ?>
                                                <option value="<?php echo $i; ?>" class="bg-slate-900"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest block mb-1">Travel Date</label>
                                    <input type="date" name="travel_date" class="w-full bg-white/10 border border-white/10 rounded-xl px-5 py-4 text-sm focus:border-primary focus:bg-white/10 transition outline-none">
                                </div>
                                <div>
                                    <input type="tel" name="phone" placeholder="Phone Number" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-sm focus:border-primary focus:bg-white/10 transition outline-none">
                                </div>
                                
                                <?php if ($price_per_person > 0): ?>
                                <div class="bg-white/5 rounded-2xl p-6 mt-4 space-y-3">
                                    <div class="flex justify-between text-xs font-bold text-slate-400">
                                        <span>Adults x <span id="summary_adult_count">2</span></span>
                                        <span class="text-white">₹<span id="summary_adult_total">0</span></span>
                                    </div>
                                    <div class="flex justify-between text-xs font-bold text-slate-400">
                                        <span>Children x <span id="summary_child_count">0</span></span>
                                        <span class="text-white">₹<span id="summary_child_total">0</span></span>
                                    </div>
                                    <div class="flex justify-between text-xs font-black text-primary pt-3 border-t border-white/10">
                                        <span>Estimated Total</span>
                                        <span class="text-lg">₹<span id="summary_total">0</span></span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <button type="submit" class="accent-gradient w-full flex items-center justify-center gap-3 py-5 rounded-2xl font-black text-sm uppercase transition hover:scale-[1.02] shadow-xl text-white">
                                    <i class="ri-send-plane-2-line"></i> Book This Tour
                                </button>
                            </form>

                            <div class="mt-8 pt-8 border-t border-white/5 flex flex-col gap-4">
                                <a href="https://wa.me/917006296814" class="flex items-center justify-center gap-3 py-4 rounded-2xl font-black text-xs uppercase border-2 border-green-500/30 text-green-500 hover:bg-green-500 hover:text-white transition-all">
                                    <i class="ri-whatsapp-line"></i> Chat with Expert
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Expert Tips -->
                    <?php if ($tips->num_rows > 0): ?>
                    <div class="bg-white/80 backdrop-blur-2xl border border-white p-10 rounded-[2.5rem] shadow-premium">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="accent-gradient h-12 w-12 rounded-xl flex items-center justify-center text-white text-xl">
                                <i class="ri-lightbulb-line"></i>
                            </div>
                            <h3 class="text-2xl font-black">Pro Tips</h3>
                        </div>
                        <div class="space-y-6">
                            <?php while ($t = $tips->fetch_assoc()): 
                                $tip_icons = [
                                    'general' => 'ri-information-line',
                                    'best_time' => 'ri-calendar-line',
                                    'what_to_pack' => 'ri-briefcase-line',
                                    'safety' => 'ri-shield-check-line',
                                    'transport' => 'ri-car-line',
                                    'food' => 'ri-restaurant-line'
                                ];
                                $tip_icon = $tip_icons[$t['tip_type']] ?? 'ri-lightbulb-line';
                            ?>
                            <div class="flex gap-4">
                                <i class="<?php echo $tip_icon; ?> text-primary mt-1 text-lg"></i>
                                <div>
                                    <h5 class="text-slate-900 font-black mb-1"><?php echo htmlspecialchars($t['title']); ?></h5>
                                    <p class="text-sm text-slate-500 line-clamp-3"><?php echo htmlspecialchars($t['description']); ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Nearby Explore -->
                    <?php if ($nearby->num_rows > 0): ?>
                    <div class="bg-white/80 backdrop-blur-2xl border border-white p-10 rounded-[2.5rem] shadow-premium">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="accent-gradient h-12 w-12 rounded-xl flex items-center justify-center text-white text-xl">
                                <i class="ri-compass-3-line"></i>
                            </div>
                            <h3 class="text-2xl font-black">Nearby Spots</h3>
                        </div>
                        <div class="space-y-4">
                            <?php while ($n = $nearby->fetch_assoc()): ?>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-b-0">
                                <span class="text-slate-900 font-bold"><?php echo htmlspecialchars($n['attraction_name']); ?></span>
                                <span class="text-primary text-sm font-bold"><?php echo $n['distance_km']; ?> KM</span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <!-- Dynamic Inquiry Section -->
        <!-- <section class="max-w-7xl mx-auto px-6 mt-32 relative reveal-up">
            <div class="accent-gradient rounded-[3rem] p-12 md:p-20 text-white text-center shadow-premium relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-[100px] rounded-full"></div>
                <div class="relative z-10">
                    <span class="bg-white/20 px-6 py-2 rounded-full text-xs font-black uppercase tracking-widest mb-6 inline-block">Plan Your Trip</span>
                    <h2 class="text-4xl md:text-6xl font-black mb-6 tracking-tight">Wanna experience <?php echo htmlspecialchars($destination['destination_name']); ?>?</h2>
                    <p class="text-xl text-white/80 max-w-3xl mx-auto mb-10 leading-relaxed">
                        Let Zubi Tours & Holidays craft a legendary itinerary tailored to your rhythm.
                    </p>
                    <a href="contact.php?destination=<?php echo urlencode($destination['destination_name']); ?>" class="bg-white text-primary px-12 py-5 rounded-2xl font-black uppercase text-sm inline-block shadow-2xl hover:scale-105 transition-transform">Get My Luxury Quote</a>
                </div>
            </div>
        </section> -->


    </main>

    <!-- Global Floating CTAs -->
    <div class="fixed right-6 bottom-6 flex flex-col gap-4 z-[1000]">
        <a href="https://wa.me/917006296814?text=Interested in <?php echo urlencode($destination['destination_name']); ?>" class="w-16 h-16 bg-[#25D366] rounded-2xl flex items-center justify-center text-white text-3xl shadow-2xl hover:rotate-12 transition-all group relative">
            <i class="ri-whatsapp-line"></i>
            <span class="absolute right-20 bg-slate-900 text-white text-[10px] font-black uppercase py-2 px-4 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap">WhatsApp Expert</span>
        </a>
        <a href="tel:+917006296814" class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center text-white text-3xl shadow-2xl hover:-rotate-12 transition-all group relative">
            <i class="ri-phone-fill"></i>
            <span class="absolute right-20 bg-slate-900 text-white text-[10px] font-black uppercase py-2 px-4 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap">Call Assistance</span>
        </a>
    </div>

    <!--==================== FOOTER ====================-->
       <!-- FOOTER -->
<?php include '../admin/includes/footer.php'; ?>

    <!--=============== JS ===============-->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <script>
        // Reveal Section On Scroll logic
        const observerOptions = { threshold: 0.1 };
        const revealSection = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        };
        const observer = new IntersectionObserver(revealSection, observerOptions);
        document.querySelectorAll('.reveal-up').forEach(s => observer.observe(s));

        // Swiper Gallery logic
        var galleryThumbs = new Swiper(".gallery-thumbs", {
            spaceBetween: 12,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: { 768: { slidesPerView: 5 }, 1024: { slidesPerView: 6 } }
        });
        
        var galleryMain = new Swiper(".gallery-main", {
            spaceBetween: 10,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            thumbs: { swiper: galleryThumbs },
            autoplay: { delay: 5000, disableOnInteraction: false }
        });


        // Package Details Interactivity
        function switchDay(index) {
            document.querySelectorAll('[data-day-btn]').forEach(btn => {
                btn.classList.remove('active', 'accent-gradient', 'text-white');
                btn.classList.add('bg-slate-100', 'text-slate-400');
            });
            document.querySelectorAll('[data-day-content]').forEach(content => content.classList.remove('active'));

            const activeBtn = document.querySelector(`[data-day-btn="${index}"]`);
            activeBtn.classList.remove('bg-slate-100', 'text-slate-400');
            activeBtn.classList.add('active');
            
            document.querySelector(`[data-day-content="${index}"]`).classList.add('active');
        }

        function toggleFaq(index) {
            const item = document.querySelector(`[data-faq="${index}"]`);
            const isActive = item.classList.contains('active');
            
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            if (!isActive) item.classList.add('active');
        }

        // Sidebar Pricing Logic
        const pkgPrice = <?php echo (float)$price_per_person; ?>;
        const childDiscount = 0.3; // 30% off for kids

        function updateSidebarSummary() {
            if (!pkgPrice) return;
            
            const adults = parseInt(document.getElementById('sidebar_adults').value);
            const children = parseInt(document.getElementById('sidebar_children').value);
            
            const adultTotal = adults * pkgPrice;
            const childTotal = children * (pkgPrice * (1 - childDiscount));
            const total = adultTotal + childTotal;
            
            document.getElementById('summary_adult_count').textContent = adults;
            document.getElementById('summary_adult_total').textContent = adultTotal.toLocaleString();
            document.getElementById('summary_child_count').textContent = children;
            document.getElementById('summary_child_total').textContent = childTotal.toLocaleString();
            document.getElementById('summary_total').textContent = total.toLocaleString();
        }

        // Initialize summary
        if (pkgPrice) updateSidebarSummary();

        // Header and Parallax
        window.addEventListener('scroll', () => {
            const heroImg = document.querySelector('.hero-bg img');
            const scroll = window.pageYOffset;
            
            if (heroImg) {
                heroImg.style.transform = `scale(1.1) translateY(${scroll * 0.2}px)`;
            }
        });
    </script>
</body>
</html>
