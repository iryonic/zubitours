<?php
require_once '../admin/includes/connection.php';

if (!isset($_GET['id'])) {
    header('Location: packages.php');
    exit();
}

$package_id = $_GET['id'];

// Fetch package details with all images
$package_query = $conn->prepare("
    SELECT p.*, 
           GROUP_CONCAT(DISTINCT pi.image_path ORDER BY pi.is_primary DESC, pi.id ASC) as all_images,
           COUNT(DISTINCT pi.id) as image_count
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id 
    WHERE p.id = ? AND p.is_active = 1
    GROUP BY p.id
");
$package_query->bind_param("i", $package_id);
$package_query->execute();
$package_result = $package_query->get_result();

if ($package_result->num_rows === 0) {
    header('Location: packages.php');
    exit();
}

$package = $package_result->fetch_assoc();

// Decode JSON fields
$package['highlights'] = json_decode($package['highlights'], true) ?: [];
$package['inclusions'] = json_decode($package['inclusions'], true) ?: [];
$package['exclusions'] = json_decode($package['exclusions'], true) ?: [];
$package['faqs'] = json_decode($package['faqs'], true) ?: [];
$package['itinerary'] = json_decode($package['itinerary'], true) ?: [];

// Get all images
$package_images = [];
if ($package['all_images']) {
    $package_images = explode(',', $package['all_images']);
}

// Get similar packages (same type, excluding current)
$similar_query = $conn->prepare("
    SELECT p.*, pi.image_path 
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    WHERE p.package_type = ? AND p.id != ? AND p.is_active = 1 
    ORDER BY p.is_featured DESC, p.rating DESC 
    LIMIT 3
");
$similar_query->bind_param("si", $package['package_type'], $package_id);
$similar_query->execute();
$similar_packages = $similar_query->get_result();

// Handle package booking
$booking_message = '';
$booking_message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_package'])) {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_notes = $_POST['customer_notes'] ?? '';
    $checkin_date = $_POST['checkin_date'];
    $number_of_adults = $_POST['number_of_adults'];
    $number_of_children = $_POST['number_of_children'] ?? 0;
    
    // Generate booking reference
    $booking_reference = 'PKG' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Calculate checkout date and total amount
    $checkin_obj = new DateTime($checkin_date);
    $checkout_obj = clone $checkin_obj;
    $checkout_obj->modify("+{$package['duration_days']} days");
    $checkout_date = $checkout_obj->format('Y-m-d');
    
    $total_days = $package['duration_days'];
    $total_guests = $number_of_adults + $number_of_children;
    
    // Calculate price (children get 30% discount)
    $adult_price = $number_of_adults * $package['price_per_person'];
    $child_price = $number_of_children * $package['price_per_person'] * 0.7;
    $total_amount = $adult_price + $child_price;
    
    // Check if package has available slots for the dates
    $check_availability = $conn->prepare("
        SELECT SUM(number_of_adults + number_of_children) as total_booked 
        FROM package_bookings 
        WHERE package_id = ? 
        AND booking_status NOT IN ('cancelled', 'refunded') 
        AND ((checkin_date BETWEEN ? AND ?) OR (checkout_date BETWEEN ? AND ?))
    ");
    $check_availability->bind_param("issss", $package_id, $checkin_date, $checkout_date, $checkin_date, $checkout_date);
    $check_availability->execute();
    $availability_result = $check_availability->get_result();
    $availability = $availability_result->fetch_assoc();
    
    $available_slots = $package['max_people'] - ($availability['total_booked'] ?? 0);
    
    if ($total_guests > $available_slots) {
        $booking_message = "Sorry, only {$available_slots} spot(s) available for your selected dates. Please reduce the number of guests or choose different dates.";
        $booking_message_type = "error";
    } else {
        // Insert booking
        $stmt = $conn->prepare("
            INSERT INTO package_bookings (
                booking_reference, package_id, customer_name, customer_email, 
                customer_phone, customer_notes, checkin_date, checkout_date, 
                total_days, number_of_adults, number_of_children, total_amount
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "sissssssiiid", 
            $booking_reference, $package_id, $customer_name, $customer_email,
            $customer_phone, $customer_notes, $checkin_date, $checkout_date,
            $total_days, $number_of_adults, $number_of_children, $total_amount
        );
        
        if ($stmt->execute()) {
            $booking_message = "Booking successful! Your booking reference is: <strong>{$booking_reference}</strong>. We'll contact you shortly for confirmation.";
            $booking_message_type = "success";
            
            // Clear form
            $_POST = [];
        } else {
            $booking_message = "Error creating booking. Please try again or contact us directly.";
            $booking_message_type = "error";
        }
    }
}

// Increment view count
$conn->query("UPDATE packages SET views = COALESCE(views, 0) + 1 WHERE id = $package_id");
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

<title>Kashmir Tour Package Itinerary & Cost | Zubi Tours</title>

<meta name="description" content="Get complete details of Kashmir tour package including itinerary, hotel options, sightseeing, cab services and pricing with Zubi Tours & Holidays.">

<meta name="keywords" content="
Kashmir tour itinerary,
Kashmir trip cost,
Kashmir travel plan,
Kashmir package details,
Srinagar tour itinerary
">
 <!-- --==============Favicon =============-- -->
<link rel="icon" type="image/png" href="../assets/img/zubilogo.jpg" />


    <!--=============== REMIXICONS ===============-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css"
    />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />

    <title><?php echo htmlspecialchars($package['package_name']); ?> - Zubi Tours</title>
    <style>
      /* EXACTLY YOUR ORIGINAL STYLES FROM package-details.php */
      /* Package Detail Styles */
      .package-detail-hero {
        position: relative;
        height: 80vh;
        min-height: 600px;
        max-height: 900px;
        display: flex;
        align-items: flex-end;
        color: white;
        overflow: hidden;
        padding: 0 20px 80px;
      }
      
      .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.3) 100%);
        background-size: cover;
        background-position: center;
        z-index: -2;
      }
      
      .hero-content {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        z-index: 1;
      }
      
      .package-badge {
        display: inline-block;
        background: var(--first-color);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 20px;
      }
      
      .hero-content h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        font-weight: 800;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        line-height: 1.2;
        max-width: 800px;
      }
      
      .hero-content p {
        font-size: 1.4rem;
        margin-bottom: 30px;
        max-width: 700px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        line-height: 1.6;
      }
      
      .package-meta {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
      }
      
      .meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      
      .meta-item i {
        font-size: 1.5rem;
        color: var(--first-color);
      }
      
      .meta-item span {
        font-weight: 500;
        font-size: 1.1rem;
      }
      
      /* Package Detail Content */
      .package-detail-container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 40px;
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 60px;
        display: flex;
        flex-direction: column;
      }
      
      
      /* Overview Section */
      .overview-section {
        margin-bottom: 60px;
      }
      
      .section-title {
        font-size: 2.2rem;
        color: var(--title-color);
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
        font-weight: 700;
      }
      
      .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 5px;
        background: var(--first-color);
        border-radius: 3px;
      }
      
      .overview-content p {
        line-height: 1.8;
        margin-bottom: 25px;
        color: var(--text-color);
        font-size: 1.1rem;
      }
      
      .highlights-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin: 35px 0;
      }
      
      .highlight-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .highlight-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      }
      
      .highlight-icon {
        width: 60px;
        height: 60px;
        background: rgba(115, 155, 249, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--first-color);
        font-size: 1.5rem;
        flex-shrink: 0;
      }
      
      .highlight-item h4 {
        font-size: 1.2rem;
        margin-bottom: 5px;
        color: var(--title-color);
      }
      
      .highlight-item p {
        margin: 0;
        font-size: 1rem;
        color: var(--text-color);
      }
      
      /* Itinerary Section */
      .itinerary-section {
        margin-bottom: 60px;
      }
      
      .itinerary-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
        gap: 10px;
        margin-bottom: 35px;
        border-bottom: 2px solid #e2e8f0;
        flex-wrap: wrap;
      }
      
      .itinerary-tab {
        padding: 15px 30px;
        background: #f8fafc;
        border: none;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
      }
      
      .itinerary-tab:hover {
        background: #e2e8f0;
      }
      
      .itinerary-tab.active {
        background: var(--first-color);
        color: white;
      }
      
      .itinerary-content {
        display: none;
      }
      
      .itinerary-content.active {
        display: block;
      }
      
      .day-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-left: 5px solid var(--first-color);
      }
      
      .day-title {
        font-size: 1.5rem;
        color: var(--title-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
      }
      
      .day-title i {
        color: var(--first-color);
        font-size: 1.8rem;
      }
      
      .activities-list {
        list-style: none;
      }
      
      .activity-item {
        display: flex;
        gap: 20px;
        padding: 20px 0;
        border-bottom: 1px solid #f1f1f1;
        align-items: flex-start;
      }
      
      .activity-item:last-child {
        border-bottom: none;
      }
      
      .activity-time {
        min-width: 120px;
        font-weight: 700;
        color: var(--first-color);
        font-size: 1.1rem;
        padding: 8px 15px;
        background: rgba(115, 155, 249, 0.1);
        border-radius: 8px;
        text-align: center;
      }
      
      .activity-details h4 {
        margin-bottom: 10px;
        color: var(--title-color);
        font-size: 1.2rem;
      }
      
      .activity-details p {
        color: var(--text-color);
        margin: 0;
        line-height: 1.7;
        font-size: 1.1rem;
      }
      
      /* Inclusions Section */
      .inclusions-section {
        margin-bottom: 60px;
      }
      
      .inclusions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
      }
      
      .inclusion-category {
        margin-bottom: 35px;
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      }
      
      .inclusion-category h4 {
        font-size: 1.4rem;
        color: var(--title-color);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f1f1;
      }
      
      .inclusion-list {
        list-style: none;
      }
      
      .inclusion-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }
      
      .inclusion-item i {
        color: #22c55e;
        font-size: 1.3rem;
      }
      
      .exclusion-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }
      
      .exclusion-item i {
        color: #ef4444;
        font-size: 1.3rem;
      }
      
      /* Booking Widget */
      .booking-widget {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        position: sticky;
        top: 120px;
        height: fit-content;
      }
      
      .price-section {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f1f1f1;
      }
      
      .price-amount {
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--first-color);
        line-height: 1;
        margin-bottom: 10px;
      }
      
      .price-note {
        color: var(--text-color-light);
        font-size: 1.1rem;
        margin-bottom: 20px;
      }
      
      .discount-badge {
        display: inline-block;
        background: #ffedd5;
        color: #ea580c;
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 700;
      }
      
      .booking-form .form-group {
        margin-bottom: 25px;
      }
      
      .booking-form label {
        display: block;
        margin-bottom: 12px;
        font-weight: 600;
        color: var(--title-color);
        font-size: 1.1rem;
      }
      
      .booking-form input,
      .booking-form select {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
      }
      
      .booking-form input:focus,
      .booking-form select:focus {
        border-color: var(--first-color);
        outline: none;
        box-shadow: 0 0 0 4px rgba(42, 61, 232, 0.1);
      }
      
      .form-row {
        display: flex;
        gap: 20px;
      }
      
      .form-row .form-group {
        flex: 1;
      }
      
      .guest-counter {
        display: flex;
        align-items: center;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
      }
      
      .counter-btn {
        width: 50px;
        height: 50px;
        background: #f8fafc;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      
      .counter-btn:hover {
        background: #e2e8f0;
      }
      
      .guest-counter input {
        width: 60px;
        border: none;
        text-align: center;
        padding: 0;
        font-weight: 700;
        font-size: 1.2rem;
      }
      
      .booking-summary {
        background: #f8fafc;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
      }
      
      .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }
      
      .summary-item:last-child {
        margin-bottom: 0;
        padding-top: 15px;
        border-top: 2px solid #e2e8f0;
        font-weight: 800;
        font-size: 1.3rem;
        color: var(--title-color);
      }
      
      .book-now-btn {
        width: 100%;
        padding: 20px;
        background: var(--first-color);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.2rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(42, 61, 232, 0.3);
      }
      
      .book-now-btn:hover {
        background: var(--first-color-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(42, 61, 232, 0.4);
      }
      
      /* Gallery Section */
      .gallery-section {
        margin-bottom: 60px;
      }
      
      .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
      }
      
      .gallery-item {
        border-radius: 15px;
        overflow: hidden;
        height: 250px;
        position: relative;
        cursor: pointer;
      }
      
      .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
      }
      
      .gallery-item:hover img {
        transform: scale(1.08);
      }
      
      .gallery-item:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      
      .gallery-item:hover:after {
        opacity: 1;
      }
      
      /* FAQ Section */
      .faq-section {
        margin-bottom: 60px;
      }
      
      .faq-item {
        background: white;
        border-radius: 15px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
      }
      
      .faq-item:hover {
        transform: translateY(-3px);
      }
      
      .faq-question {
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 700;
        color: var(--title-color);
        font-size: 1.2rem;
      }
      
      .faq-answer {
        padding: 0 25px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.4s ease;
        color: var(--text-color);
        line-height: 1.7;
        font-size: 1.1rem;
      }
      
      .faq-item.active .faq-answer {
        padding: 0 25px 25px;
        max-height: 500px;
      }
      
      .faq-item.active .faq-question i {
        transform: rotate(180deg);
      }
      
      /* Similar Packages */
      .similar-packages {
        margin-top: 60px;
        padding-top: 60px;
        border-top: 2px solid #e2e8f0;
      }
      
      .similar-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 30px;
      }
      
      .similar-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
      }
      
      .similar-card:hover {
        transform: translateY(-10px);
      }
      
      .similar-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
      }
      
      .similar-content {
        padding: 20px;
      }
      
      .similar-content h4 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: var(--title-color);
      }
      
      .similar-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--first-color);
        margin: 10px 0;
      }
      
      .similar-button {
        display: inline-block;
        padding: 10px 20px;
        background: var(--first-color);
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 10px;
        transition: all 0.3s ease;
      }
      
   
      
      /* Message Styles */
      .booking-message {
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 20px 25px;
        border-radius: 12px;
        color: white;
        z-index: 1000;
        animation: slideInRight 0.5s ease;
        max-width: 400px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 15px;
      }
      
      .booking-message.success {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border-left: 5px solid #16a34a;
      }
      
      .booking-message.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-left: 5px solid #dc2626;
      }
      
      @keyframes slideInRight {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }


      textarea{
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        resize: vertical;
      }
      
      .message-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
      }
      
      /* Itinerary Days */
      .itinerary-days {
        margin-top: 30px;
      }
      
      .day-section {
        margin-bottom: 30px;
      }
      
      .day-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
      }
      
      .day-number {
        width: 50px;
        height: 50px;
        background: var(--first-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
      }
      
      .day-title-text {
        font-size: 1.5rem;
        color: var(--title-color);
        margin: 0;
      }
      
      .day-description {
        color: var(--text-color);
        line-height: 1.7;
        margin-bottom: 20px;
        font-size: 1.1rem;
      }
      
      /* Package Badges */
      .badge-container {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
      }
      
      .badge {
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
      }
      
      .badge-bestseller {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
      }
      
      .badge-featured {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
      }
      
      .badge-popular {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
      }
      
      .type-badge {
        background: rgba(115, 155, 249, 0.1);
        color: var(--first-color);
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 20px;
      }
      
      /* Responsive Adjustments */
      @media (max-width: 1440px) {
        .package-detail-container {
          max-width: 1200px;
          grid-template-columns: 1fr 400px;
          gap: 50px;
        }
        
        .hero-content h1 {
          font-size: 3rem;
        }
      }
      
      @media (max-width: 1200px) {
        .package-detail-container {
          grid-template-columns: 1fr 380px;
          gap: 40px;
          padding: 0 30px;
        }
        
        .hero-content h1 {
          font-size: 2.8rem;
        }
        
        .hero-content p {
          font-size: 1.2rem;
        }
        
        .section-title {
          font-size: 2rem;
        }
      }
      
      @media (max-width: 1024px) {
        .package-detail-container {
          grid-template-columns: 1fr;
          gap: 40px;
        }
        
        .booking-widget {
          position: static;
          max-width: 600px;
          margin: 0 auto;
        }
        
        .gallery-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        
        .package-detail-hero {
          height: 70vh;
          padding: 0 20px 60px;
        }
        
        .similar-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      
      @media (max-width: 768px) {
        .package-detail-hero {
          height: 60vh;
          padding: 0 15px 40px;
        }
        
        .hero-content h1 {
          font-size: 2.2rem;
        }
        
        .hero-content p {
          font-size: 1.1rem;
        }
        
        .package-meta {
          flex-direction: column;
          gap: 15px;
        }
        
        .highlights-grid,
        .inclusions-grid {
          grid-template-columns: 1fr;
        }
        
        .itinerary-tabs {
          flex-wrap: wrap;
        }
        
        .form-row {
          flex-direction: column;
          gap: 0;
        }
        
        .gallery-grid {
          grid-template-columns: 1fr;
        }
        
        .package-detail-container {
          padding: 0 20px;
          margin: 40px auto;
        }
        
        .similar-grid {
          grid-template-columns: 1fr;
        }
        
        .booking-message {
          left: 20px;
          right: 20px;
          max-width: none;
        }
      }
      
      @media (max-width: 480px) {
        .package-detail-hero {
          height: 50vh;
          min-height: 500px;
        }
        
        .hero-content h1 {
          font-size: 1.8rem;
        }
        
        .hero-content p {
          font-size: 1rem;
        }
        
        .section-title {
          font-size: 1.7rem;
        }
        
        .price-amount {
          font-size: 2.2rem;
        }
        
        .activity-item {
          flex-direction: column;
          gap: 10px;
        }
        
        .activity-time {
          min-width: auto;
          align-self: flex-start;
        }
      }
      
      /* Large screen optimizations */
      @media (min-width: 1600px) {
        .package-detail-container {
          max-width: 1500px;
          grid-template-columns: 1fr 500px;
        }
        
        .hero-content h1 {
          font-size: 4rem;
        }
        
        .hero-content p {
          font-size: 1.5rem;
        }
        
        .section-title {
          font-size: 2.5rem;
        }
        
        .overview-content p {
          font-size: 1.2rem;
        }
        
        .highlight-item {
          padding: 25px;
        }
        
        .highlight-icon {
          width: 70px;
          height: 70px;
          font-size: 1.8rem;
        }
        
        .highlight-item h4 {
          font-size: 1.4rem;
        }
        
        .highlight-item p {
          font-size: 1.1rem;
        }
      }
      
      @media (min-width: 2000px) {
        .package-detail-container {
          max-width: 1800px;
        }
        
        .hero-content {
          max-width: 1600px;
        }
      }
    </style>
  </head>
  <body>
    <!-- Loader -->
  

    <!--==================== HEADER ====================-->
    <?php include '../admin/includes/navbar.php'; ?>

    <!-- Booking Message -->
    <?php if ($booking_message): ?>
      <div class="booking-message <?php echo $booking_message_type; ?>">
        <i class="ri-<?php echo $booking_message_type == 'success' ? 'check' : 'close'; ?>-circle-fill" style="font-size: 1.5rem;"></i>
        <div>
          <strong><?php echo $booking_message_type == 'success' ? 'Booking Successful!' : 'Booking Alert'; ?></strong>
          <p style="margin: 5px 0 0; font-size: 0.95rem;"><?php echo $booking_message; ?></p>
        </div>
        <button class="message-close" onclick="this.parentElement.remove()">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Package Hero Section -->
    <section class="package-detail-hero">
      <div class="hero-background" style="background-image: url('../admin/upload/<?php echo $package_images[0] ?? 'bg1.jpg'; ?>');"></div>
      <div class="hero-content">
        <div class="badge-container">
          <?php if ($package['badge']): ?>
            <span class="badge badge-<?php echo strtolower($package['badge']); ?>">
              <?php echo $package['badge']; ?>
            </span>
          <?php endif; ?>
          <?php if ($package['is_featured']): ?>
            <span class="badge badge-featured">
              <i class="ri-star-line"></i> Featured
            </span>
          <?php endif; ?>
        </div>
        
        <h1><?php echo htmlspecialchars($package['package_name']); ?></h1>
        <p><?php echo htmlspecialchars(substr($package['description'], 0, 200)); ?>...</p>
        
        <div class="package-meta">
          <div class="meta-item">
            <i class="ri-calendar-event-line"></i>
            <span><?php echo $package['duration_days']; ?> Days / <?php echo $package['duration_days'] - 1; ?> Nights</span>
          </div>
          <div class="meta-item">
            <i class="ri-user-line"></i>
            <span>Max <?php echo $package['max_people']; ?> People</span>
          </div>
          <div class="meta-item">
            <i class="ri-hotel-bed-line"></i>
            <span><?php echo $package['accommodation_type'] ?: 'Standard Accommodation'; ?></span>
          </div>
          <div class="meta-item">
            <i class="ri-star-fill"></i>
            <span><?php echo $package['rating'] ? number_format($package['rating'], 1) : '4.9'; ?> (<?php echo $package['reviews_count'] ?: '128'; ?> Reviews)</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Package Detail Content -->
    <div class="package-detail-container">
      <!-- Main Content -->
      <div class="package-main-content">
        <!-- Type Badge -->
        <div class="type-badge">
          <?php echo ucfirst($package['package_type']); ?> Package
        </div>

        <!-- Overview Section -->
        <section class="overview-section">
          <h2 class="section-title">Overview</h2>
          <div class="overview-content">
            <p><?php echo htmlspecialchars($package['description']); ?></p>
            
            <?php if (!empty($package['highlights'])): ?>
              <div class="highlights-grid">
                <?php foreach ($package['highlights'] as $index => $highlight): 
                  $icons = ['ri-hotel-line', 'ri-restaurant-line', 'ri-car-line', 'ri-guide-line', 'ri-map-pin-line', 'ri-camera-line', 'ri-landscape-line', 'ri-heart-line'];
                  $icon = $icons[$index % count($icons)];
                ?>
                  <div class="highlight-item">
                    <div class="highlight-icon">
                      <i class="<?php echo $icon; ?>"></i>
                    </div>
                    <div>
                      <h4><?php echo htmlspecialchars($highlight['title'] ?? 'Experience Highlight'); ?></h4>
                      <p><?php echo htmlspecialchars($highlight['description']); ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <!-- Itinerary Section -->
        <?php if (!empty($package['itinerary'])): ?>
        <section class="itinerary-section">
          <h2 class="section-title">Itinerary</h2>
          
          <div class="itinerary-tabs">
            <?php foreach ($package['itinerary'] as $index => $day): ?>
              <button class="itinerary-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-day="day<?php echo $day['day']; ?>">
                Day <?php echo $day['day']; ?>
              </button>
            <?php endforeach; ?>
          </div>
          
          <?php foreach ($package['itinerary'] as $index => $day): ?>
            <div class="itinerary-content <?php echo $index === 0 ? 'active' : ''; ?>" id="day<?php echo $day['day']; ?>">
              <div class="day-card">
                <h3 class="day-title">
                  <i class="ri-map-pin-line"></i>
                  Day <?php echo $day['day']; ?>: <?php echo htmlspecialchars($day['title']); ?>
                </h3>
                
                <?php if (!empty($day['description'])): ?>
                  <p style="color: var(--text-color); margin-bottom: 25px; line-height: 1.7; font-size: 1.1rem;">
                    <?php echo htmlspecialchars($day['description']); ?>
                  </p>
                <?php endif; ?>
                
                <?php if (!empty($day['activities'])): ?>
                  <ul class="activities-list">
                    <?php foreach ($day['activities'] as $activity): ?>
                      <li class="activity-item">
                        <span class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></span>
                        <div class="activity-details">
                          <h4>Activity</h4>
                          <p><?php echo htmlspecialchars($activity['description']); ?></p>
                        </div>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <!-- Inclusions Section -->
        <section class="inclusions-section">
          <h2 class="section-title">Inclusions & Exclusions</h2>
          
          <div class="inclusions-grid">
            <?php if (!empty($package['inclusions'])): ?>
              <div class="inclusion-category">
                <h4>What's Included</h4>
                <ul class="inclusion-list">
                  <?php foreach ($package['inclusions'] as $inclusion): ?>
                    <li class="inclusion-item">
                      <i class="ri-checkbox-circle-fill"></i>
                      <span><?php echo htmlspecialchars($inclusion); ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            
            <?php if (!empty($package['exclusions'])): ?>
              <div class="inclusion-category">
                <h4>What's Not Included</h4>
                <ul class="inclusion-list">
                  <?php foreach ($package['exclusions'] as $exclusion): ?>
                    <li class="exclusion-item">
                      <i class="ri-close-circle-fill"></i>
                      <span><?php echo htmlspecialchars($exclusion); ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <!-- Gallery Section -->
        <?php if (!empty($package_images)): ?>
          <section class="gallery-section">
            <h2 class="section-title">Package Gallery</h2>
            
            <div class="gallery-grid">
              <?php foreach ($package_images as $index => $image): ?>
                <div class="gallery-item">
                  <img src="../admin/upload/<?php echo $image; ?>" 
                       alt="<?php echo htmlspecialchars($package['package_name']); ?> - Image <?php echo $index + 1; ?>"
                       onerror="this.src='../assets/img/bg1.jpg'">
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- FAQ Section -->
        <?php if (!empty($package['faqs'])): ?>
          <section class="faq-section">
            <h2 class="section-title">Frequently Asked Questions</h2>
            
            <?php foreach ($package['faqs'] as $faq): ?>
              <div class="faq-item">
                <div class="faq-question">
                  <?php echo htmlspecialchars($faq['question']); ?>
                  <i class="ri-arrow-down-s-line"></i>
                </div>
                <div class="faq-answer">
                  <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </section>
        <?php endif; ?>

        <!-- Similar Packages Section -->
        <?php if ($similar_packages->num_rows > 0): ?>
          <section class="similar-packages">
            <h2 class="section-title">Similar Packages</h2>
            <div class="similar-grid">
              <?php while ($similar = $similar_packages->fetch_assoc()): 
                $similar_highlights = json_decode($similar['highlights'], true) ?: [];
                $similar_desc = !empty($similar_highlights) ? $similar_highlights[0]['description'] : substr($similar['description'], 0, 100) . '...';
              ?>
                <div class="similar-card">
                  <img src="../admin/upload/<?php echo $similar['image_path'] ?: 'bg1.jpg'; ?>" 
                       alt="<?php echo htmlspecialchars($similar['package_name']); ?>"
                       onerror="this.src='../assets/img/bg1.jpg'">
                  <div class="similar-content">
                    <h4><?php echo htmlspecialchars($similar['package_name']); ?></h4>
                    <p style="color: var(--text-color); font-size: 0.95rem; margin-bottom: 10px;">
                      <?php echo htmlspecialchars($similar_desc); ?>
                    </p>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                      <i class="ri-calendar-event-line" style="color: var(--first-color);"></i>
                      <span><?php echo $similar['duration_days']; ?> Days</span>
                      <i class="ri-user-line" style="color: var(--first-color); margin-left: 10px;"></i>
                      <span>Max <?php echo $similar['max_people']; ?></span>
                    </div>
                    <div class="similar-price">
                      ₹<?php echo number_format($similar['price_per_person'], 2); ?>
                    </div>
                    <a href="./package-details.php?id=<?php echo $similar['id']; ?>" class="similar-button">View Details</a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </section>
        <?php endif; ?>
      </div>

      <!-- Booking Widget -->
      <div class="booking-widget">
        <div class="price-section">
          <div class="price-amount">₹<?php echo number_format($package['price_per_person'], 2); ?></div>
          <div class="price-note">per person (double occupancy)</div>
          <?php if ($package['badge'] == 'Bestseller' || $package['is_featured']): ?>
            <span class="discount-badge">
              <i class="ri-flashlight-line"></i> Limited Time Offer
            </span>
          <?php endif; ?>
        </div>
        
        <form class="booking-form" method="POST" action="">
          <input type="hidden" name="book_package" value="1">
          
          <div class="form-group">
            <label for="checkin_date">Check-in Date</label>
            <input type="date" id="checkin_date" name="checkin_date" 
                   min="<?php echo date('Y-m-d'); ?>" 
                   value="<?php echo isset($_POST['checkin_date']) ? htmlspecialchars($_POST['checkin_date']) : date('Y-m-d', strtotime('+7 days')); ?>" 
                   required>
          </div>
          
          <div class="form-group">
            <label for="customer_name">Full Name</label>
            <input type="text" id="customer_name" name="customer_name" 
                   value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>"
                   required>
          </div>
          
          <div class="form-group">
            <label for="customer_email">Email</label>
            <input type="email" id="customer_email" name="customer_email" 
                   value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>"
                   required>
          </div>
          
          <div class="form-group">
            <label for="customer_phone">Phone Number</label>
            <input type="tel" id="customer_phone" name="customer_phone" 
                   value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>"
                   required>
          </div>
          
          <div class="form-group">
            <label for="guests">Number of Guests</label>
            <div class="guest-counter">
              <button type="button" class="counter-btn" id="decrease-guests">-</button>
              <input type="number" id="guests" value="<?php echo isset($_POST['number_of_adults']) ? max(1, min($package['max_people'], intval($_POST['number_of_adults']) + intval($_POST['number_of_children'] ?? 0))) : 2; ?>" min="1" max="<?php echo $package['max_people']; ?>" readonly>
              <button type="button" class="counter-btn" id="increase-guests">+</button>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="number_of_adults">Adults</label>
              <select id="number_of_adults" name="number_of_adults" required>
                <?php 
                $selected_adults = isset($_POST['number_of_adults']) ? intval($_POST['number_of_adults']) : 2;
                for ($i = 1; $i <= $package['max_people']; $i++): 
                ?>
                  <option value="<?php echo $i; ?>" <?php echo $selected_adults == $i ? 'selected' : ''; ?>>
                    <?php echo $i; ?> Adult<?php echo $i > 1 ? 's' : ''; ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="number_of_children">Children</label>
              <select id="number_of_children" name="number_of_children">
                <?php 
                $selected_children = isset($_POST['number_of_children']) ? intval($_POST['number_of_children']) : 0;
                for ($i = 0; $i <= 3; $i++): 
                ?>
                  <option value="<?php echo $i; ?>" <?php echo $selected_children == $i ? 'selected' : ''; ?>>
                    <?php echo $i == 0 ? 'No Children' : $i . ' Child' . ($i > 1 ? 'ren' : ''); ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label for="customer_notes">Special Requests (Optional)</label>
            <textarea id="customer_notes" name="customer_notes" rows="3" placeholder="Any special requirements or requests..."><?php echo isset($_POST['customer_notes']) ? htmlspecialchars($_POST['customer_notes']) : ''; ?></textarea>
          </div>
          
          <div class="booking-summary">
            <div class="summary-item">
              <span>2 Adults x ₹<?php echo number_format($package['price_per_person'], 2); ?></span>
              <span>₹<span id="summary-adults"><?php echo number_format($package['price_per_person'] * 2, 2); ?></span></span>
            </div>
            <div class="summary-item">
              <span>Children Discount (30%)</span>
              <span>-₹<span id="summary-discount">0.00</span></span>
            </div>
            <div class="summary-item">
              <span>Taxes & Fees</span>
              <span>₹<span id="summary-taxes"><?php echo number_format($package['price_per_person'] * 0.1, 2); ?></span></span>
            </div>
            <div class="summary-item">
              <span>Total</span>
              <span>₹<span id="summary-total"><?php echo number_format($package['price_per_person'] * 2 * 1.1, 2); ?></span></span>
            </div>
          </div>
          
          <button type="submit" class="book-now-btn">Book Now</button>
        </form>
      </div>
    </div>

    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    
    <script>
      // Itinerary Tab Functionality
      document.addEventListener('DOMContentLoaded', function() {
        const itineraryTabs = document.querySelectorAll('.itinerary-tab');
        const itineraryContents = document.querySelectorAll('.itinerary-content');
        
        itineraryTabs.forEach(tab => {
          tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            itineraryTabs.forEach(t => t.classList.remove('active'));
            itineraryContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const day = tab.getAttribute('data-day');
            document.getElementById(day).classList.add('active');
          });
        });
        
        // FAQ Accordion Functionality
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
          question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            faqItem.classList.toggle('active');
          });
        });
        
        // Guest Counter Functionality
        const decreaseBtn = document.getElementById('decrease-guests');
        const increaseBtn = document.getElementById('increase-guests');
        const guestsInput = document.getElementById('guests');
        const adultsSelect = document.getElementById('number_of_adults');
        const childrenSelect = document.getElementById('number_of_children');
        
        decreaseBtn.addEventListener('click', () => {
          let value = parseInt(guestsInput.value);
          if (value > 1) {
            guestsInput.value = value - 1;
            updateGuestDetails();
          }
        });
        
        increaseBtn.addEventListener('click', () => {
          let value = parseInt(guestsInput.value);
          const maxPeople = <?php echo $package['max_people']; ?>;
          if (value < maxPeople) {
            guestsInput.value = value + 1;
            updateGuestDetails();
          }
        });
        
        function updateGuestDetails() {
          const totalGuests = parseInt(guestsInput.value);
          const children = parseInt(childrenSelect.value);
          const adults = totalGuests - children;
          
          if (adults >= 1 && adults <= <?php echo $package['max_people']; ?>) {
            adultsSelect.value = adults;
          }
          updateBookingSummary();
        }
        
        adultsSelect.addEventListener('change', updateTotalGuests);
        childrenSelect.addEventListener('change', updateTotalGuests);
        
        function updateTotalGuests() {
          const adults = parseInt(adultsSelect.value);
          const children = parseInt(childrenSelect.value);
          guestsInput.value = adults + children;
          updateBookingSummary();
        }
        
        // Initialize animations
        setTimeout(() => {
          document.querySelectorAll('.highlight-item, .day-card, .gallery-item, .faq-item, .similar-card').forEach((item, index) => {
            setTimeout(() => {
              item.style.opacity = '1';
              item.style.transform = 'translateY(0)';
            }, index * 100);
          });
        }, 500);
        
        // Initialize date picker
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin_date').min = today;
        
        // Calculate initial booking summary
        updateBookingSummary();
      });
      
      // Smooth Scroll Functionality
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;
          
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            targetElement.scrollIntoView({
              behavior: 'smooth'
            });
          }
        });
      });
      
      // Calculate package booking widget
      const pricePerPerson = <?php echo $package['price_per_person']; ?>;
      const taxRate = 0.1; // 10% tax
      const childDiscount = 0.3; // 30% discount for children
      
      function updateBookingSummary() {
        const adults = parseInt(document.getElementById('number_of_adults').value);
        const children = parseInt(document.getElementById('number_of_children').value);
        const totalGuests = adults + children;
        
        // Calculate base prices
        const adultPrice = adults * pricePerPerson;
        const childPrice = children * pricePerPerson * (1 - childDiscount);
        const subtotal = adultPrice + childPrice;
        const taxes = subtotal * taxRate;
        const total = subtotal + taxes;
        
        // Calculate child discount amount
        const childFullPrice = children * pricePerPerson;
        const childDiscountAmount = childFullPrice - childPrice;
        
        // Update summary
        document.getElementById('summary-adults').textContent = adultPrice.toLocaleString('en-IN', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        
        document.getElementById('summary-discount').textContent = childDiscountAmount.toLocaleString('en-IN', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        
        document.getElementById('summary-taxes').textContent = taxes.toLocaleString('en-IN', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        
        document.getElementById('summary-total').textContent = total.toLocaleString('en-IN', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }
      
      // Auto-close booking message after 10 seconds
      setTimeout(() => {
        const bookingMessage = document.querySelector('.booking-message');
        if (bookingMessage) {
          bookingMessage.style.transition = 'all 0.5s ease';
          bookingMessage.style.opacity = '0';
          bookingMessage.style.transform = 'translateX(100%)';
          setTimeout(() => bookingMessage.remove(), 500);
        }
      }, 10000);
      
      // Form validation
      document.querySelector('.booking-form').addEventListener('submit', function(e) {
        const checkinDate = new Date(document.getElementById('checkin_date').value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (checkinDate < today) {
          e.preventDefault();
          alert('Check-in date cannot be in the past.');
          return false;
        }
        
        const maxPeople = <?php echo $package['max_people']; ?>;
        const adults = parseInt(document.getElementById('number_of_adults').value);
        const children = parseInt(document.getElementById('number_of_children').value);
        const totalGuests = adults + children;
        
        if (totalGuests > maxPeople) {
          e.preventDefault();
          alert(`Maximum ${maxPeople} people allowed for this package.`);
          return false;
        }
        
        if (adults < 1) {
          e.preventDefault();
          alert('At least one adult is required.');
          return false;
        }
        
        // Validate phone number
        const phone = document.getElementById('customer_phone').value;
        const phoneRegex = /^[0-9]{10}$/;
        if (!phoneRegex.test(phone.replace(/\D/g, ''))) {
          e.preventDefault();
          alert('Please enter a valid 10-digit phone number.');
          return false;
        }
        
        return true;
      });
    </script>
  </body>
</html>