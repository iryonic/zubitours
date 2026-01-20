<?php
require_once '../admin/includes/connection.php';

if (!isset($_GET['id'])) {
    header('Location: destinations.php');
    exit();
}

$destination_id = $_GET['id'];

// Fetch destination details
$dest_query = $conn->prepare("
    SELECT d.*, 
           GROUP_CONCAT(DISTINCT di.image_path ORDER BY di.is_primary DESC, di.id ASC) as all_images
    FROM destinations d 
    LEFT JOIN destination_images di ON d.id = di.destination_id 
    WHERE d.id = ? AND d.is_active = 1
    GROUP BY d.id
");
$dest_query->bind_param("i", $destination_id);
$dest_query->execute();
$dest_result = $dest_query->get_result();

if ($dest_result->num_rows === 0) {
    header('Location: destinations.php');
    exit();
}

$destination = $dest_result->fetch_assoc();

// Decode JSON fields
$best_seasons = json_decode($destination['best_seasons'], true) ?: [];

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
    $message = "Inquiry for: " . $destination['destination_name'] . "\n" . trim($_POST['message'] ?? '');
    $subject = "Destination Inquiry: " . $destination['destination_name'];
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $phone, $subject, $message, $ip_address, $user_agent);
        
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
    if (empty($path)) return '../assets/img/bg1.jpg';
    
    $possible_paths = [
        '../uploads/' . $path,
        '../upload/' . $path,
        '../admin/uploads/' . $path,
        '../admin/upload/' . $path,
        './admin/upload/' . $path,
        $path
    ];
    
    foreach ($possible_paths as $p) {
        if (file_exists($p)) return $p;
    }
    
    return '../assets/img/bg1.jpg';
}

// Fetch related packages (try to match destination name)
$dest_name_search = "%" . $destination['destination_name'] . "%";
$pkg_stmt = $conn->prepare("
    SELECT p.*, pi.image_path 
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    WHERE p.is_active = 1 AND (p.package_name LIKE ? OR p.description LIKE ?)
    ORDER BY p.is_featured DESC, p.created_at DESC 
    LIMIT 3
");
$pkg_stmt->bind_param("ss", $dest_name_search, $dest_name_search);
$pkg_stmt->execute();
$packages_query = $pkg_stmt->get_result();

// If no matching packages, fall back to featured ones
if ($packages_query->num_rows == 0) {
    $packages_query = $conn->query("
        SELECT p.*, pi.image_path 
        FROM packages p 
        LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
        WHERE p.is_active = 1 
        ORDER BY p.is_featured DESC, p.created_at DESC 
        LIMIT 3
    ");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($destination['destination_name']); ?> - Explore Kashmir & Ladakh | Zubi Tours</title>
    
    <link rel="icon" type="image/png" href="../assets/img/zubilogo.jpg" />
    
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
    <link rel="stylesheet" href="../assets/css/styles.css" />

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
        <section class="relative h-[85vh] min-h-[600px] flex items-center justify-center overflow-hidden bg-black">
            <div class="hero-bg absolute inset-0 z-0">
                <?php $primary_image = !empty($destination_images) ? get_image_path($destination_images[0]) : '../assets/img/bg1.jpg'; ?>
                <img src="<?php echo $primary_image; ?>" class="w-full h-full object-cover filter brightness-[0.6] saturate-[1.2] scale-110" alt="<?php echo htmlspecialchars($destination['destination_name']); ?>">
            </div>
            <div class="hero-gradient absolute inset-0 z-[1]"></div>
            
            <div class="relative z-[2] max-w-5xl px-6 text-center text-white">
                <span class="accent-gradient inline-flex items-center gap-2 px-6 py-2 rounded-full font-bold uppercase tracking-widest text-xs mb-8 shadow-lg">
                    <i class="ri-map-2-line"></i> <?php echo htmlspecialchars($destination['region']); ?>
                </span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-6 leading-none tracking-tight drop-shadow-2xl">
                    <?php echo htmlspecialchars($destination['destination_name']); ?>
                </h1>
                <div class="flex flex-wrap justify-center gap-4 mt-8">
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 px-6 py-3 rounded-full flex items-center gap-3 transition hover:bg-white/20">
                        <i class="ri-map-pin-2-fill text-accent-yellow text-xl"></i>
                        <span class="font-medium"><?php echo htmlspecialchars($destination['location']); ?></span>
                    </div>
                    <?php if ($destination['rating']): ?>
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 px-6 py-3 rounded-full flex items-center gap-3 transition hover:bg-white/20">
                        <i class="ri-star-fill text-accent-yellow text-xl"></i>
                        <span class="font-medium"><?php echo number_format($destination['rating'], 1); ?> Rating</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-[2] cursor-pointer animate-bounce" onclick="window.scrollTo({top: window.innerHeight * 0.8, behavior: 'smooth'})">
                <i class="ri-arrow-down-s-line text-5xl text-white/80"></i>
            </div>
        </section>

        <!-- Content Grid -->
        <div class="max-w-7xl mx-auto px-6 -mt-24 relative z-10 flex flex-col lg:flex-row gap-8">
            <!-- Main Content Area -->
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-premium space-y-20">
                    <!-- Overview Section -->
                    <section class="reveal-up active">
                        <div class="mb-8">
                            <span class="text-primary font-black uppercase tracking-widest text-sm block mb-2">Destination Insight</span>
                            <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">Discover <?php echo htmlspecialchars($destination['destination_name']); ?></h2>
                        </div>
                        <p class="text-xl leading-[1.8] text-slate-600">
                            <?php 
                            $full_desc = !empty($destination['detailed_description']) ? $destination['detailed_description'] : $destination['short_description'];
                            echo nl2br(htmlspecialchars($full_desc)); 
                            ?>
                        </p>
                    </section>

                    <!-- Highlights Grid -->
                    <?php if ($highlights->num_rows > 0): ?>
                    <section class="reveal-up">
                        <div class="mb-10">
                            <span class="text-primary font-black uppercase tracking-widest text-sm block mb-2">The Best Of</span>
                            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Trip Highlights</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php while ($h = $highlights->fetch_assoc()): ?>
                            <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 transition-all hover:bg-white hover:shadow-hover group relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-full h-1.5 accent-gradient opacity-0 transition-opacity group-hover:opacity-100"></div>
                                <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center text-3xl text-primary shadow-sm mb-6 group-hover:scale-110 transition-transform">
                                    <i class="<?php echo $h['icon'] ?: 'ri-check-double-line'; ?>"></i>
                                </div>
                                <h3 class="text-xl font-black mb-3 text-slate-900"><?php echo htmlspecialchars($h['title']); ?></h3>
                                <p class="text-slate-500 leading-relaxed"><?php echo htmlspecialchars($h['description']); ?></p>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Dynamic Swiper Gallery -->
                    <?php if (count($destination_images) > 0): ?>
                    <section class="reveal-up">
                        <div class="mb-10">
                            <span class="text-primary font-black uppercase tracking-widest text-sm block mb-2">Visual Journey</span>
                            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Visual Experiences</h2>
                        </div>
                        <div class="space-y-4">
                            <div class="swiper gallery-main rounded-[3rem] shadow-premium group/gallery">
                                <div class="swiper-wrapper">
                                    <?php foreach ($destination_images as $img): ?>
                                    <div class="swiper-slide h-[500px] md:h-[700px]">
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
                                    <?php foreach($destination_images as $img): ?>
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
                            <span class="text-primary font-black uppercase tracking-widest text-sm block mb-2">Things to do</span>
                            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Thrilling Activities</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php while ($a = $activities->fetch_assoc()): ?>
                            <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 transition-all hover:bg-white hover:shadow-hover group relative overflow-hidden">
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
                </div>
            </div>

            <!-- Enhanced Sidebar -->
            <aside class="w-full lg:w-[400px] flex-shrink-0">
                <div class="sticky top-28 space-y-6">
                    <!-- Stats Card -->
                    <div class="bg-white/80 backdrop-blur-2xl border border-white p-10 rounded-[2.5rem] shadow-premium">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="accent-gradient h-12 w-12 rounded-xl flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="ri-information-line"></i>
                            </div>
                            <h3 class="text-2xl font-black">Travel DNA</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold">Region</span>
                                <span class="text-slate-900 font-black"><?php echo ucfirst($destination['region']); ?></span>
                            </div>
                            <div class="flex justify-between items-center py-4 border-b border-slate-100">
                                <span class="text-slate-500 font-bold">Nature</span>
                                <span class="text-slate-900 font-black"><?php echo ucfirst($destination['destination_type']); ?></span>
                            </div>
                            <div class="flex justify-between items-center py-4">
                                <span class="text-slate-500 font-bold">Best Months</span>
                                <span class="text-slate-900 font-black text-right"><?php echo implode(', ', array_map('ucfirst', $best_seasons)); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing/Call Card -->
                    <div class="bg-slate-900 text-white p-10 rounded-[2.5rem] shadow-premium relative overflow-hidden group">
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
                                <div>
                                    <input type="tel" name="phone" placeholder="Phone Number" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-sm focus:border-primary focus:bg-white/10 transition outline-none">
                                </div>
                                <div>
                                    <textarea name="message" placeholder="Special requirements..." class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-sm focus:border-primary focus:bg-white/10 transition outline-none h-24 resize-none"></textarea>
                                </div>
                                <button type="submit" class="accent-gradient w-full flex items-center justify-center gap-3 py-5 rounded-2xl font-black text-sm uppercase transition hover:scale-[1.02] shadow-xl text-white">
                                    <i class="ri-send-plane-2-line"></i> Send Request
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
        <section class="max-w-7xl mx-auto px-6 mt-32 relative reveal-up">
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
        </section>

        <!-- Dynamic Related Packages -->
        <section class="py-32" id="related-packages">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="text-primary font-black uppercase tracking-[0.3em] text-xs">Curated Selection</span>
                    <h2 class="text-4xl font-black text-slate-900 mt-2">Recommended Tour Packages</h2>
                </div>
                
                <div class="swiper pkg-swiper !overflow-visible">
                    <div class="swiper-wrapper">
                        <?php if ($packages_query->num_rows > 0): ?>
                            <?php while ($p = $packages_query->fetch_assoc()): ?>
                            <div class="swiper-slide h-auto">
                                <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-premium group transition-all hover:shadow-hover border border-slate-100 flex flex-col h-full">
                                    <div class="h-72 overflow-hidden relative">
                                        <img src="<?php echo get_image_path($p['image_path']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?php echo htmlspecialchars($p['package_name']); ?>">
                                        <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-xs font-black text-primary flex items-center gap-2">
                                            <i class="ri-history-line"></i> <?php echo $p['duration_days']; ?> Days
                                        </div>
                                    </div>
                                    <div class="p-8 flex flex-col flex-1">
                                        <h3 class="text-2xl font-black text-slate-900 mb-4"><?php echo htmlspecialchars($p['package_name']); ?></h3>
                                        <div class="flex gap-4 mb-8 text-slate-500 text-sm font-bold">
                                            <span class="flex items-center gap-2"><i class="ri-hotel-bed-line"></i> Boutique Stay</span>
                                            <span class="flex items-center gap-2"><i class="ri-taxi-line"></i> Pvt. Cab</span>
                                        </div>
                                        <div class="mt-auto pt-8 border-t border-slate-50 flex justify-between items-center">
                                            <div>
                                                <span class="block text-[10px] text-slate-400 uppercase font-black tracking-widest">Pricing From</span>
                                                <span class="text-2xl font-black text-primary">₹<?php echo number_format($p['price_per_person']); ?></span>
                                            </div>
                                            <a href="package-details.php?id=<?php echo $p['id']; ?>" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-xs uppercase hover:bg-primary transition-colors">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination !-bottom-12"></div>
                </div>
            </div>
        </section>
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
    <?php include '../admin/includes/footer.php'; ?>

    <!--=============== JS ===============-->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
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

        // Packages swiper
        new Swiper(".pkg-swiper", {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: { el: ".swiper-pagination", clickable: true },
            breakpoints: { 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } }
        });

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
