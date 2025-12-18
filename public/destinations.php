<?php
require_once '../admin/includes/connection.php';

// Server-side search and filtering (supports GET parameters: q, region, type, season)
$q = trim($_GET['q'] ?? '');
$region = $_GET['region'] ?? '';
$type = $_GET['type'] ?? '';
$season = $_GET['season'] ?? '';

$where = "d.is_active = 1";
$params = [];
$types = '';

if ($q !== '') {
    $where .= " AND (d.destination_name LIKE ? OR d.location LIKE ? OR d.short_description LIKE ?)";
    $like = "%{$q}%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}

if ($region !== '' && $region !== 'all') {
    $where .= " AND d.region = ?";
    $params[] = $region; $types .= 's';
}

if ($type !== '' && $type !== 'all') {
    $where .= " AND d.destination_type = ?";
    $params[] = $type; $types .= 's';
}

if ($season !== '' && $season !== 'all') {
    // best_seasons is a JSON array stored as text, so match the quoted value
    $where .= " AND d.best_seasons LIKE ?";
    $params[] = "%\"{$season}\"%"; $types .= 's';
}

$sql = "SELECT d.*, di.image_path 
          FROM destinations d 
          LEFT JOIN destination_images di ON d.id = di.destination_id AND di.is_primary = 1 
          WHERE $where 
          ORDER BY d.is_featured DESC, d.created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        // bind params dynamically
        $bind_names = [];
        $bind_names[] = & $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = & $params[$i];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // fallback to a simple query if prepare() fails
    $safe_q = $conn->real_escape_string($q);
    $result = $conn->query("SELECT d.*, di.image_path FROM destinations d LEFT JOIN destination_images di ON d.id = di.destination_id AND di.is_primary = 1 WHERE d.is_active = 1 ORDER BY d.is_featured DESC, d.created_at DESC");
}
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


<title>Kashmir Tourist Destinations | Srinagar, Gulmarg, Pahalgam</title>

<meta name="description" content="Explore top tourist destinations in Kashmir including Srinagar, Gulmarg, Pahalgam, Sonamarg and more with Zubi Tours & Holidays.">

<meta name="keywords" content="
Kashmir destinations,
Srinagar tourism,
Gulmarg tour,
Pahalgam sightseeing,
Sonamarg travel,
places to visit in Kashmir
">


    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />

   
    
   <style>
    /* Modern Hero Section */
    .modern-hero {
        position: relative;
        height: 30vh;
        min-height: 500px;
        background: linear-gradient(135deg, 
                    rgba(50, 50, 51, 0.95) 0%, 
                    rgba(15, 23, 42, 0.9) 100%),
                    url('../assets/img/bg1.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        overflow: hidden;
        margin-top: 80px;
    }

    .modern-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 20%, 
                    rgba(37, 99, 235, 0.2) 0%, 
                    transparent 50%),
                    radial-gradient(circle at 70% 80%, 
                    rgba(16, 185, 129, 0.2) 0%, 
                    transparent 50%);
        animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        padding: 0 20px;
        animation: fadeInUp 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hero-content h1 {
        font-size: 4.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(135deg, 
                    #fff 0%, 
                    #93c5fd 50%, 
                    #60a5fa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        background-size: 200% auto;
        animation: gradientText 3s ease infinite;
        text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    @keyframes gradientText {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .hero-content p {
        font-size: 1.3rem;
        margin-bottom: 40px;
        opacity: 0.9;
        line-height: 1.6;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 1s ease 0.3s forwards;
        opacity: 0;
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 50px;
        margin-top: 50px;
        flex-wrap: wrap;
        animation: fadeInUp 1s ease 0.5s forwards;
        opacity: 0;
    }

    .stat-item {
        text-align: center;
        position: relative;
        padding: 20px;
        min-width: 140px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-color);
        display: block;
        margin-bottom: 5px;
        text-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
    }

    /* Enhanced Filters */
    .enhanced-filters {
        background: linear-gradient(135deg, 
                    var(--card-bg) 0%, 
                    rgba(255, 255, 255, 0.05) 100%);
        padding: 40px;
        border-radius: 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.1);
        max-width: 1200px;
        margin: -80px auto 60px;
        position: relative;
        z-index: 10;
        backdrop-filter: blur(20px);
        animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(37, 99, 235, 0.1);
    }

    .filter-header h3 {
        font-size: 1.8rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 700;
    }

    .filter-header h3 i {
        color: var(--primary-color);
        font-size: 2rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }

    .filter-group {
        position: relative;
    }

    .filter-group label {
        display: block;
        margin-bottom: 12px;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-group label i {
        color: var(--primary-color);
        font-size: 1.2rem;
    }

    .filter-select {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid var(--border-color);
        border-radius: 15px;
        font-size: 1rem;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 20px center;
        background-size: 20px;
        font-weight: 500;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15),
                    inset 0 2px 4px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .filter-actions {
        display: flex;
        gap: 20px;
        justify-content: flex-end;
        padding-top: 30px;
        border-top: 2px solid rgba(37, 99, 235, 0.1);
    }

    .filter-btn {
        padding: 16px 40px;
        border: none;
        border-radius: 15px;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
                    transparent, 
                    rgba(255, 255, 255, 0.2), 
                    transparent);
        transition: left 0.6s ease;
    }

    .filter-btn:hover::before {
        left: 100%;
    }

    .filter-btn.primary {
        background: linear-gradient(135deg, 
                    var(--primary-color) 0%, 
                    var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
    }

    .filter-btn.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
    }

    .filter-btn.secondary {
        background: transparent;
        color: var(--text-primary);
        border: 2px solid var(--border-color);
    }

    .filter-btn.secondary:hover {
        background: rgba(37, 99, 235, 0.05);
        border-color: var(--primary-color);
        transform: translateY(-3px);
    }

    /* Admin edit overlay button (visible to logged-in admins) */
    .admin-edit-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 6px 8px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        z-index: 3;
        transition: transform 0.15s ease, opacity 0.15s ease;
        opacity: 0;
        text-decoration: none;
    }

    .card-media:hover .admin-edit-btn,
    .admin-edit-btn:focus {
        opacity: 1;
        transform: translateY(-2px);
    }

    /* Destinations Section */
    .destinations-section {
        padding: 80px 20px;
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
    }

    .section-header {
        text-align: center;
        margin-bottom: 70px;
        position: relative;
    }

    .section-header::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, 
                    var(--primary-color), 
                    var(--success-color));
        border-radius: 2px;
    }

    .section-header h2 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(135deg, 
                    var(--primary-color) 0%, 
                    var(--primary-dark) 50%, 
                    var(--success-color) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        background-size: 200% auto;
        animation: gradientText 4s ease infinite;
    }

    .section-header p {
        color: var(--text-secondary);
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* Search Box */
    .search-container {
        max-width: 800px;
        margin: 0 auto 60px;
        position: relative;
    }

    .search-box {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 20px 25px 20px 65px;
        border: 2px solid var(--border-color);
        border-radius: 20px;
        font-size: 1.1rem;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 15px 40px rgba(37, 99, 235, 0.15),
                    0 0 0 4px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }

    .search-icon {
        position: absolute;
        left: 25px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        font-size: 1.5rem;
        z-index: 2;
    }

    /* Destinations Grid */
    .destinations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 40px;
        margin-bottom: 80px;
    }

    /* Destination Card */
    .destination-card {
        background: linear-gradient(135deg, 
                    var(--card-bg) 0%, 
                    rgba(255, 255, 255, 0.05) 100%);
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        opacity: 0;
        transform: translateY(40px) scale(0.95);
        animation: cardAppear 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    @keyframes cardAppear {
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .destination-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15),
                    0 0 0 1px rgba(37, 99, 235, 0.1);
    }

    .card-media {
        position: relative;
        height: 280px;
        overflow: hidden;
    }

    .card-media::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, 
                    transparent 50%, 
                    rgba(0, 0, 0, 0.5) 100%);
        z-index: 1;
    }

    .card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .destination-card:hover .card-media img {
        transform: scale(1.15);
    }

    .card-badges {
        position: absolute;
        top: 25px;
        right: 25px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 2;
    }

    .badge {
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: translateX(-5px);
    }

    .badge.region {
        background: linear-gradient(135deg, 
                    rgba(37, 99, 235, 0.9) 0%, 
                    rgba(29, 78, 216, 0.9) 100%);
        color: white;
    }

    .badge.featured {
        background: linear-gradient(135deg, 
                    rgba(245, 158, 11, 0.9) 0%, 
                    rgba(217, 119, 6, 0.9) 100%);
        color: white;
    }

    .badge.type {
        background: linear-gradient(135deg, 
                    rgba(16, 185, 129, 0.9) 0%, 
                    rgba(5, 150, 105, 0.9) 100%);
        color: white;
    }

    .card-content {
        padding: 30px;
        position: relative;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .card-header h3 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        flex: 1;
        line-height: 1.3;
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, 
                    rgba(245, 158, 11, 0.15) 0%, 
                    rgba(217, 119, 6, 0.1) 100%);
        padding: 8px 15px;
        border-radius: 25px;
        min-width: 85px;
        justify-content: center;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .rating i {
        color: #f59e0b;
        font-size: 1rem;
    }

    .rating span {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .card-description {
        color: var(--text-secondary);
        line-height: 1.7;
        margin-bottom: 25px;
        font-size: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-secondary);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .meta-item i {
        color: var(--primary-color);
        font-size: 1.2rem;
    }

    .seasons {
        display: flex;
        gap: 8px;
    }

    .season-icon {
        font-size: 1.3rem;
        transition: transform 0.3s ease;
    }

    .season-icon:hover {
        transform: scale(1.3);
    }

    .card-actions {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .card-btn {
        flex: 1;
        padding: 16px;
        text-align: center;
        border-radius: 15px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        overflow: hidden;
    }

    .card-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
                    transparent, 
                    rgba(255, 255, 255, 0.2), 
                    transparent);
        transition: left 0.6s ease;
    }

    .card-btn:hover::before {
        left: 100%;
    }

    .card-btn.primary {
        background: linear-gradient(135deg, 
                    var(--primary-color) 0%, 
                    var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
    }

    .card-btn.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
    }

    .card-btn.secondary {
        background: transparent;
        color: var(--text-primary);
        border: 2px solid var(--border-color);
    }

    .card-btn.secondary:hover {
        background: rgba(37, 99, 235, 0.05);
        border-color: var(--primary-color);
        transform: translateY(-3px);
    }

    /* No Results */
    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 100px 20px;
        background: linear-gradient(135deg, 
                    var(--bg-primary) 0%, 
                    rgba(255, 255, 255, 0.05) 100%);
        border-radius: 25px;
        border: 2px dashed var(--border-color);
    }

    .no-results i {
        font-size: 5rem;
        color: var(--text-secondary);
        margin-bottom: 30px;
        opacity: 0.5;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    .no-results h3 {
        font-size: 2.2rem;
        margin-bottom: 15px;
        color: var(--text-primary);
        font-weight: 700;
    }

    .no-results p {
        color: var(--text-secondary);
        margin-bottom: 40px;
        font-size: 1.1rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, 
                    var(--primary-color) 0%, 
                    var(--primary-dark) 50%, 
                    var(--success-color) 100%);
        padding: 100px 20px;
        text-align: center;
        border-radius: 30px;
        margin: 80px auto;
        max-width: 1200px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, 
                    rgba(255, 255, 255, 0.1) 0%, 
                    transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .cta-content {
        position: relative;
        z-index: 2;
    }

    .cta-content h2 {
        font-size: 3.5rem;
        margin-bottom: 25px;
        font-weight: 800;
        text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .cta-content p {
        font-size: 1.3rem;
        margin-bottom: 40px;
        opacity: 0.95;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.7;
    }

    .cta-buttons {
        display: flex;
        gap: 25px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .cta-btn {
        padding: 18px 45px;
        border-radius: 15px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        position: relative;
        overflow: hidden;
        min-width: 200px;
    }

    .cta-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
                    transparent, 
                    rgba(255, 255, 255, 0.2), 
                    transparent);
        transition: left 0.6s ease;
    }

    .cta-btn:hover::before {
        left: 100%;
    }

    .cta-btn.primary {
        background: white;
        color: var(--primary-color);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .cta-btn.primary:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .cta-btn.secondary {
        background: transparent;
        border: 2px solid white;
        color: white;
    }

    .cta-btn.secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-5px) scale(1.05);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(60px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .destinations-grid {
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
        }
    }

    @media (max-width: 992px) {
        .modern-hero {
            height: 60vh;
            min-height: 400px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
        }

        .hero-stats {
            gap: 30px;
        }

        .stat-item {
            min-width: 120px;
            padding: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
        }

        .enhanced-filters {
            margin: -60px auto 40px;
            padding: 30px;
        }

        .section-header h2 {
            font-size: 3rem;
        }
    }

    @media (max-width: 768px) {
        .modern-hero {
            height: 50vh;
            min-height: 350px;
        }

        .hero-content h1 {
            font-size: 2.8rem;
        }

        .hero-content p {
            font-size: 1.1rem;
        }

        .hero-stats {
            gap: 20px;
        }

        .stat-item {
            min-width: 100px;
            padding: 12px;
        }

        .stat-number {
            font-size: 2rem;
        }

        .enhanced-filters {
            margin: -40px auto 30px;
            padding: 25px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .filter-actions {
            flex-direction: column;
        }

        .filter-btn {
            width: 100%;
            justify-content: center;
        }

        .destinations-grid {
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .section-header h2 {
            font-size: 2.5rem;
        }

        .section-header p {
            font-size: 1.1rem;
        }

        .cta-section {
            padding: 70px 20px;
        }

        .cta-content h2 {
            font-size: 2.8rem;
        }

        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }

        .cta-btn {
            width: 100%;
            max-width: 300px;
        }

        .card-actions {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .hero-content h1 {
            font-size: 2.2rem;
        }

        .hero-stats {
            flex-direction: column;
            gap: 15px;
        }

        .stat-item {
            width: 100%;
            max-width: 200px;
        }

        .section-header h2 {
            font-size: 2rem;
        }

        .cta-content h2 {
            font-size: 2.2rem;
        }

        .search-input {
            padding: 18px 20px 18px 55px;
            font-size: 1rem;
        }

        .search-icon {
            left: 20px;
        }
    }
</style>
</head>
<body>


    <!--==================== HEADER ====================-->
   <?php
    include '../admin/includes/navbar.php';
   ?>

    <!-- Modern Hero Section -->
    <section class="modern-hero">
        <div class="hero-content">
            <h1>Discover Paradise</h1>
            <p>Explore the breathtaking destinations of Kashmir and Ladakh, where every view is a postcard and every moment is magical.</p>
            
            <
        </div>
    </section>

    <!-- Enhanced Filters -->
    <!-- <div class="enhanced-filters">
        <div class="filter-header">
            <div>
                <h3><i class="ri-filter-3-line"></i> Find Your Perfect Destination</h3>
                <div class="active-filters" id="activeFilters" aria-live="polite"></div>
            </div>
            <div class="header-actions" role="toolbar" aria-label="Filter actions">
                <button type="button" class="filter-btn header secondary" onclick="resetAllFilters()">
                    <i class="ri-refresh-line"></i> Reset
                </button>
                <button type="button" class="filter-btn header primary" onclick="filterDestinations()">
                    <i class="ri-search-line"></i> Apply
                </button>
            </div>
        </div>
        
        <div class="filter-grid">
            <div class="filter-group">
                <label for="region">Region</label>
                <select id="region" class="filter-select">
                    <option value="all">All Regions</option>
                    <option value="kashmir">Kashmir</option>
                    <option value="ladakh">Ladakh</option>
                    <option value="jammu">Jammu</option>
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
                    <option value="desert">Desert</option>
                    <option value="cultural">Cultural</option>
                    <option value="adventure">Adventure</option>
                    <option value="scenic">Scenic</option>
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
        </div>
        
        <div class="filter-actions">
            <button class="filter-btn secondary" id="resetFilters">
                <i class="ri-refresh-line"></i> Reset Filters
            </button>
            <button class="filter-btn primary" id="applyFilters">
                <i class="ri-search-line"></i> Apply Filters
            </button>
        </div>
    </div> -->

    <!-- Destinations Section -->
    <section class="destinations-section">
        <div class="section-header">
           
        </div>

        <!-- Search Box -->
        <div class="search-container">
            <form method="GET" action="">
                <div class="search-box">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" id="searchInput" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" class="search-input" placeholder="Search destinations by name, location, or description...">
                </div>
            </form>
            <?php if (!empty($_GET['q'])): ?>
                <div class="search-results-meta" style="text-align:center;margin-top:12px;color:var(--text-secondary);">
                    Showing results for &ldquo;<strong><?php echo htmlspecialchars($_GET['q']); ?></strong>&rdquo;
                </div>
            <?php endif; ?>
        </div>

        <!-- Destinations Grid -->
        <div class="destinations-grid" id="destinationsGrid">
            <?php
            if ($result->num_rows > 0):
                $counter = 0;
                while ($destination = $result->fetch_assoc()):
                    $best_seasons = json_decode($destination['best_seasons'], true) ?: [];
                    
                    // Season icons
                    $season_icons = [
                        'spring' => '🌼',
                        'summer' => '☀️',
                        'autumn' => '🍂',
                        'winter' => '❄️'
                    ];
                    
                    // Type icons
                    $type_icons = [
                        'lake' => 'ri-water-flash-line',
                        'valley' => 'ri-landscape-line',
                        'mountain' => 'ri-mountain-line',
                        'monastery' => 'ri-building-line',
                        'hill' => 'ri-hills-line',
                        'desert' => 'ri-sun-line',
                        'cultural' => 'ri-ancient-gate-line',
                        'adventure' => 'ri-map-pin-line',
                        'scenic' => 'ri-image-line'
                    ];
                    
                    // Region colors
                    $region_colors = [
                        'kashmir' => '#10b981',
                        'ladakh' => '#f59e0b',
                        'jammu' => '#8b5cf6'
                    ];
            ?>
            <div class="destination-card" 
                 data-region="<?php echo $destination['region']; ?>"
                 data-type="<?php echo $destination['destination_type']; ?>"
                 data-seasons="<?php echo htmlspecialchars(json_encode($best_seasons)); ?>"
                 data-name="<?php echo htmlspecialchars(strtolower($destination['destination_name'])); ?>"
                 data-location="<?php echo htmlspecialchars(strtolower($destination['location'])); ?>"
                 data-description="<?php echo htmlspecialchars(strtolower($destination['short_description'])); ?>"
                 style="animation-delay: <?php echo $counter * 0.1; ?>s;">
                
                <div class="card-media">
                    <img src="<?php echo !empty($destination['image_path']) ? '../upload/'.$destination['image_path'] : '../assets/img/bg2.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($destination['destination_name']); ?>"
                         loading="lazy" decoding="async"
                         onerror="this.src='../assets/img/bg3.jpg'">
                    
                    <div class="card-badges">
                        <span class="badge region" style="background: <?php echo $region_colors[$destination['region']] ?? '#2563eb'; ?>">
                            <?php echo ucfirst($destination['region']); ?>
                        </span>
                        <?php if ($destination['is_featured']): ?>
                        <span class="badge featured">Featured</span>
                        <?php endif; ?>
                        <span class="badge type">
                            <i class="<?php echo $type_icons[$destination['destination_type']] ?? 'ri-map-pin-line'; ?>"></i>
                            <?php echo ucfirst($destination['destination_type']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-content">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($destination['destination_name']); ?></h3>
                        <div class="rating">
                            <i class="ri-star-fill"></i>
                            <span><?php echo number_format($destination['rating'], 1); ?></span>
                        </div>
                    </div>
                    
                    <p class="card-description">
                        <?php echo htmlspecialchars($destination['short_description']); ?>
                    </p>
                    
                    <div class="card-meta">
                        <div class="meta-item">
                            <i class="ri-map-pin-line"></i>
                            <span><?php echo htmlspecialchars($destination['location']); ?></span>
                        </div>
                        
                        <div class="meta-item seasons">
                            <?php foreach ($best_seasons as $season): ?>
                                <span class="season-icon"><?php echo $season_icons[$season] ?? '📅'; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                 
                </div>
            </div>
            <?php
                    $counter++;
                endwhile;
            else:
            ?>
            <div class="no-results">
                <i class="ri-compass-discover-line"></i>
                <h3>No Destinations Available</h3>
                <p>We're currently updating our destination list. Please check back soon!</p>
                <a href="../public/contact" class="cta-btn primary">Contact Us</a>
            </div>
            <?php endif; ?>
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
            // Set current year in footer
            document.getElementById('currentYear').textContent = new Date().getFullYear();
            
            // Filter functionality
            const regionFilter = document.getElementById('region');
            const typeFilter = document.getElementById('type');
            const seasonFilter = document.getElementById('season');
            const applyFiltersBtn = document.getElementById('applyFilters');
            const resetFiltersBtn = document.getElementById('resetFilters');
            const searchInput = document.getElementById('searchInput');
            const destinationCards = document.querySelectorAll('.destination-card');
            const destinationsGrid = document.getElementById('destinationsGrid');
            
            // Filter function
            function filterDestinations() {
                const regionValue = regionFilter.value;
                const typeValue = typeFilter.value;
                const seasonValue = seasonFilter.value;
                const searchValue = searchInput.value.toLowerCase();
                
                let visibleCount = 0;
                
                destinationCards.forEach(card => {
                    const cardRegion = card.getAttribute('data-region');
                    const cardType = card.getAttribute('data-type');
                    const cardSeasons = JSON.parse(card.getAttribute('data-seasons') || '[]');
                    const cardName = card.getAttribute('data-name');
                    const cardLocation = card.getAttribute('data-location');
                    const cardDescription = card.getAttribute('data-description');
                    
                    // Check filters
                    const regionMatch = regionValue === 'all' || regionValue === cardRegion;
                    const typeMatch = typeValue === 'all' || typeValue === cardType;
                    const seasonMatch = seasonValue === 'all' || cardSeasons.includes(seasonValue);
                    
                    // Check search
                    const searchMatch = searchValue === '' || 
                        cardName.includes(searchValue) || 
                        cardLocation.includes(searchValue) || 
                        cardDescription.includes(searchValue);
                    
                    // Show/hide card
                    if (regionMatch && typeMatch && seasonMatch && searchMatch) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                        visibleCount++;
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
                
                // Show no results message if no cards are visible
                showNoResultsMessage(visibleCount === 0);

                // Update active filter chips to reflect current state
                renderActiveFilters();
            }
            
            // Show no results message
            function showNoResultsMessage(show) {
                let noResults = document.querySelector('.no-results');
                
                if (show && !noResults) {
                    noResults = document.createElement('div');
                    noResults.className = 'no-results';
                    noResults.innerHTML = `
                        <i class="ri-search-line"></i>
                        <h3>No Destinations Found</h3>
                        <p>Try adjusting your filters or search terms</p>
                        <button class="cta-btn primary" onclick="resetAllFilters()">Reset All Filters</button>
                    `;
                    destinationsGrid.appendChild(noResults);
                } else if (!show && noResults) {
                    noResults.remove();
                }
            }
            
            // Reset all filters
            window.resetAllFilters = function() {
                regionFilter.value = 'all';
                typeFilter.value = 'all';
                seasonFilter.value = 'all';
                searchInput.value = '';
                filterDestinations();
            }
            
            // Reset filters
            function resetFilters() {
                resetAllFilters();
            }
            
            // Initialize animations
            function initializeAnimations() {
                destinationCards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.1}s`;
                });
            }
            
            // Small debounce helper for better UX on mobile keyboards
            function debounce(fn, wait = 180) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            // Event listeners
            applyFiltersBtn.addEventListener('click', filterDestinations);
            resetFiltersBtn.addEventListener('click', resetFilters);
            searchInput.addEventListener('input', debounce(filterDestinations, 200));
            
            // Add change event listeners to filters
            [regionFilter, typeFilter, seasonFilter].forEach(filter => {
                filter.addEventListener('change', filterDestinations);
            });
            
            // Initialize
            initializeAnimations();

            // Render any active filter chips initially
            renderActiveFilters();
            
            // Add hover effect to cards
            destinationCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.zIndex = '10';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.zIndex = '1';
                });
            });
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + F to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
                
                // Escape to reset filters
                if (e.key === 'Escape') {
                    resetAllFilters();
                }
            });
            
            // Initialize with filter
            filterDestinations();

            // Render chips when filters are applied via the Apply button
            applyFiltersBtn.addEventListener('click', function() {
                renderActiveFilters();
            });

            // Remove chips and reset filters via Reset button
            resetFiltersBtn.addEventListener('click', function() {
                renderActiveFilters();
            });

            // Click on chip remove buttons should clear corresponding filter
            document.getElementById('activeFilters').addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-chip')) {
                    const key = e.target.getAttribute('data-key');
                    clearFilter(key);
                }
            });

            // The function to render active filters as chips
            function renderActiveFilters() {
                const container = document.getElementById('activeFilters');
                container.innerHTML = '';
                const entries = [];

                if (regionFilter.value && regionFilter.value !== 'all') {
                    const txt = regionFilter.options[regionFilter.selectedIndex].text;
                    entries.push({ key: 'region', label: txt });
                }
                if (typeFilter.value && typeFilter.value !== 'all') {
                    const txt = typeFilter.options[typeFilter.selectedIndex].text;
                    entries.push({ key: 'type', label: txt });
                }
                if (seasonFilter.value && seasonFilter.value !== 'all') {
                    const txt = seasonFilter.options[seasonFilter.selectedIndex].text;
                    entries.push({ key: 'season', label: txt });
                }
                if (searchInput.value && searchInput.value.trim() !== '') {
                    entries.push({ key: 'search', label: `Search: "${searchInput.value.trim()}"` });
                }

                // If we have any entries, add a Clear All chip first
                if (entries.length > 0) {
                    const clearChip = document.createElement('div');
                    clearChip.className = 'filter-chip clear-all';
                    clearChip.setAttribute('role', 'button');
                    clearChip.setAttribute('tabindex', '0');
                    clearChip.innerHTML = `Clear All <button class="remove-chip" data-key="all" aria-label="Clear all filters">&times;</button>`;
                    container.appendChild(clearChip);
                }

                entries.forEach(entry => {
                    const chip = document.createElement('div');
                    chip.className = 'filter-chip';
                    chip.setAttribute('role', 'button');
                    chip.setAttribute('tabindex', '0');
                    chip.innerHTML = `${entry.label} <button class="remove-chip" data-key="${entry.key}" aria-label="Remove filter ${entry.label}">&times;</button>`;
                    container.appendChild(chip);
                });
            }

            // Clear a specific filter by key
            function clearFilter(key) {
                switch (key) {
                    case 'region':
                        regionFilter.value = 'all';
                        break;
                    case 'type':
                        typeFilter.value = 'all';
                        break;
                    case 'season':
                        seasonFilter.value = 'all';
                        break;
                    case 'search':
                        searchInput.value = '';
                        break;
                    case 'all':
                        resetAllFilters();
                        return; // resetAllFilters already calls render
                }
                filterDestinations();
                renderActiveFilters();
            }

            // Keyboard accessibility for chips (Enter to remove)
            document.getElementById('activeFilters').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const chip = e.target.closest('.filter-chip');
                    if (chip) {
                        const btn = chip.querySelector('.remove-chip');
                        if (btn) btn.click();
                    }
                }
            });
        });
    </script>
</body>
</html>