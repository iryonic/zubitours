<?php
// Start session and database connection
session_start();
require_once '../admin/includes/connection.php';

// Check if user is logged in (optional, depending on your needs)
$user_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';

// Handle car booking form submission
$booking_message = '';
$booking_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_car'])) {
  try {
    // Get form data
    $car_id = intval($_POST['car_id']);
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $customer_driving_license = trim($_POST['customer_driving_license'] ?? '');
    $pickup_location = trim($_POST['pickup_location']);
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];

    // Validate dates
    $pickup_timestamp = strtotime($pickup_date);
    $return_timestamp = strtotime($return_date);

    if ($return_timestamp <= $pickup_timestamp) {
      throw new Exception("Return date must be after pickup date.");
    }

    // Calculate total days and amount
    $total_days = ceil(($return_timestamp - $pickup_timestamp) / (60 * 60 * 24));

    // Get car price
    $stmt = $conn->prepare("SELECT price_per_day FROM car_rentals WHERE id = ? AND is_available = 1");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      throw new Exception("Selected car is not available.");
    }

    $car = $result->fetch_assoc();
    $total_amount = $car['price_per_day'] * $total_days;

    // Insert booking
    $stmt = $conn->prepare("
            INSERT INTO car_bookings 
            (car_id, customer_name, customer_email, customer_phone, customer_driving_license, 
             pickup_location, pickup_date, return_date, total_days, total_amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

    $stmt->bind_param(
      "isssssssid",
      $car_id,
      $customer_name,
      $customer_email,
      $customer_phone,
      $customer_driving_license,
      $pickup_location,
      $pickup_date,
      $return_date,
      $total_days,
      $total_amount
    );

    if ($stmt->execute()) {
      $booking_id = $conn->insert_id;
      $booking_message = "Booking successful! Your booking ID is #" . str_pad($booking_id, 6, '0', STR_PAD_LEFT) .
        ". We will contact you shortly to confirm.";
      $booking_success = true;

      // Optionally send email notification here

      // Clear form data on success
      $_POST = array();
    } else {
      throw new Exception("Error saving booking. Please try again.");
    }
  } catch (Exception $e) {
    $booking_message = $e->getMessage();
    $booking_success = false;
  }
}

// Fetch available cars from database
$cars_query = "SELECT * FROM car_rentals WHERE is_available = 1 ORDER BY price_per_day ASC";
$cars_result = $conn->query($cars_query);

// Get car types for filter
$car_types_query = "SELECT DISTINCT car_type FROM car_rentals WHERE is_available = 1";
$car_types_result = $conn->query($car_types_query);
$car_types = [];
while ($row = $car_types_result->fetch_assoc()) {
  $car_types[] = $row['car_type'];
}

// Get unique capacities
$capacities_query = "SELECT DISTINCT capacity FROM car_rentals WHERE is_available = 1 ORDER BY capacity ASC";
$capacities_result = $conn->query($capacities_query);
$capacities = [];
while ($row = $capacities_result->fetch_assoc()) {
  $capacities[] = $row['capacity'];
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


  <!--=============== REMIXICONS ===============-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!--=============== CSS ===============-->
  <link rel="stylesheet" href="../assets/css/styles.css" />
<title>Kashmir Taxi & Car Rental Service | Zubi Tours & Holidays</title>

<meta name="description" content="Book reliable Kashmir taxi and car rental services for airport transfers, sightseeing and full tour packages at best prices.">

<meta name="keywords" content="
Kashmir taxi service,
Srinagar cab service,
Kashmir car rental,
tourist taxi Kashmir,
Kashmir airport taxi
">


  <style>
    /* Additional styles for booking notifications */
    .booking-notification {
      position: fixed;
      top: 100px;
      right: 20px;
      z-index: 1000;
      padding: 20px;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      animation: slideInRight 0.3s ease, fadeOut 0.3s ease 4.7s forwards;
      max-width: 400px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .booking-notification.success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border-left: 4px solid #34d399;
    }

    .booking-notification.error {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      border-left: 4px solid #f87171;
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

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateX(100%);
      }
    }

    .car-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: #ef4444;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      z-index: 2;
    }

    .car-badge.popular {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .car-badge.group {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .car-badge.luxury {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .car-specs {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      margin: 12px 0;
      color: #64748b;
    }

    .car-specs span {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.9rem;
    }

    .car-price {
      display: flex;
      align-items: baseline;
      gap: 8px;
      margin-bottom: 16px;
    }

    .price {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2563eb;
    }

    .price span {
      font-size: 1rem;
      color: #64748b;
      font-weight: 500;
    }

    .includes {
      font-size: 0.85rem;
      color: #10b981;
      font-weight: 600;
    }

    /* Modal enhancements */
    .modal-content {
      background: white;
      border-radius: 16px;
      padding: 32px;
      max-width: 500px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
    }

    .close-modal {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 1.5rem;
      cursor: pointer;
      color: #64748b;
      transition: all 0.3s ease;
    }

    .close-modal:hover {
      color: #ef4444;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #1e293b;
    }

    .form-group input {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    @media (max-width: 768px) {
      .form-row {
        grid-template-columns: 1fr;
      }

      .modal-content {
        padding: 24px;
        width: 95%;
      }
    }

    /* Pre-fill user info if logged in */
    .user-info-prefill {
      background: #f0f9ff;
      padding: 12px 16px;
      border-radius: 12px;
      margin-bottom: 20px;
      border-left: 4px solid #2563eb;
      font-size: 0.9rem;
      color: #1e293b;
    }
  </style>
</head>

<body>
 

  <!-- Booking Notification -->
  <?php if ($booking_message): ?>
    <div class="booking-notification <?php echo $booking_success ? 'success' : 'error'; ?>">
      <i class="ri-<?php echo $booking_success ? 'check' : 'close'; ?>-circle-fill"></i>
      <span><?php echo htmlspecialchars($booking_message); ?></span>
    </div>
  <?php endif; ?>

  <!--==================== HEADER ====================-->
  <?php include '../admin/includes/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="section-header">
      <h2>Car Rentals</h2>
      <p>Comfortable vehicles for your journey through Kashmir and Ladakh</p>
    </div>

    <div class="filters">
      <select id="car-type">
        <option value="all">All Types</option>
        <?php foreach ($car_types as $type): ?>
          <option value="<?php echo $type; ?>">
            <?php echo ucfirst($type); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <select id="car-capacity">
        <option value="all">All Capacities</option>
        <?php foreach ($capacities as $capacity): ?>
          <option value="<?php echo $capacity; ?>">
            <?php echo $capacity; ?> Seaters
          </option>
        <?php endforeach; ?>
      </select>
      <button onclick="resetCarFilters()">Reset</button>
    </div>

    <div class="car-grid" id="cars-container">
      <?php if ($cars_result->num_rows > 0): ?>
        <?php while ($car = $cars_result->fetch_assoc()): ?>
          <div class="car-card"
            data-type="<?php echo $car['car_type']; ?>"
            data-capacity="<?php echo $car['capacity']; ?>"
            data-price="<?php echo $car['price_per_day']; ?>">
            <div class="car-image">
              <?php
              $image_path = !empty($car['image_path']) ? '../admin/upload/' . $car['image_path'] : '../assets/img/car1.jpg';
              ?>
              <img loading="lazy"
                src="<?php echo $image_path; ?>"
                alt="<?php echo htmlspecialchars($car['car_name']); ?>"
                onerror="this.src='../assets/img/car1.jpg'">
              <?php if ($car['badge']): ?>
                <span class="car-badge <?php echo strtolower($car['badge']); ?>">
                  <?php echo $car['badge']; ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="car-details">
              <h3><?php echo htmlspecialchars($car['car_name']); ?></h3>
              <div class="car-specs">
                <span><i class="ri-user-line"></i> <?php echo $car['capacity']; ?> Seater</span>
                <span><i class="ri-settings-3-line"></i> <?php echo ucfirst($car['transmission']); ?></span>
                <span><i class="ri-gas-station-line"></i> <?php echo ucfirst($car['fuel_type']); ?></span>
              </div>
              <div class="car-price">
                <p class="price">₹<?php echo number_format($car['price_per_day'], 2); ?> <span>/day</span></p>
                <p class="includes">Incl. Driver & Fuel</p>
              </div>
              <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
                <?php echo htmlspecialchars(substr($car['description'], 0, 100)) . '...'; ?>
              </p>
              <button class="book-btn"
                data-car-id="<?php echo $car['id']; ?>"
                data-car-name="<?php echo htmlspecialchars($car['car_name']); ?>"
                data-car-price="<?php echo $car['price_per_day']; ?>"
                onclick="openBookingModal(this)">
                Book Now
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #64748b;">
          <i class="ri-car-line" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
          <h3>No cars available at the moment</h3>
          <p>Please check back later or contact us for special arrangements.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Booking Modal -->
  <div class="modal" id="booking-modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeBookingModal()">&times;</span>
      <h2>Book Your Car</h2>
      <form id="booking-form" method="POST">
        <input type="hidden" id="car_id" name="car_id">
        <input type="hidden" name="book_car" value="1">

        <!-- Display selected car info -->
        <div id="selected-car-info" style="margin-bottom: 24px; padding: 16px; background: #f8fafc; border-radius: 12px;">
          <h4 id="selected-car-name" style="margin-bottom: 8px;"></h4>
          <div id="selected-car-price" style="color: #2563eb; font-weight: 600;"></div>
        </div>

        <?php if ($user_logged_in): ?>
          <div class="user-info-prefill">
            <i class="ri-user-fill"></i>
            Welcome back, <?php echo htmlspecialchars($user_name); ?>! Your information will be pre-filled.
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label for="pickup-location">Pickup Location *</label>
          <input type="text" id="pickup-location" name="pickup_location" required
            placeholder="Enter pickup location (e.g., Srinagar Airport)"
            value="<?php echo isset($_POST['pickup_location']) ? htmlspecialchars($_POST['pickup_location']) : ''; ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="pickup-date">Pickup Date *</label>
            <input type="date" id="pickup-date" name="pickup_date" required
              value="<?php echo isset($_POST['pickup_date']) ? $_POST['pickup_date'] : ''; ?>">
          </div>

          <div class="form-group">
            <label for="return-date">Return Date *</label>
            <input type="date" id="return-date" name="return_date" required
              value="<?php echo isset($_POST['return_date']) ? $_POST['return_date'] : ''; ?>">
          </div>
        </div>

        <!-- Dynamic price calculation -->
        <div id="price-calculation" style="margin-bottom: 20px; padding: 16px; background: #f0f9ff; border-radius: 12px; display: none;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>Rental Period:</span>
            <span id="rental-days">0 days</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>Price per Day:</span>
            <span id="price-per-day">₹0</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-weight: 600; color: #2563eb;">
            <span>Total Amount:</span>
            <span id="total-amount">₹0</span>
          </div>
        </div>

        <div class="form-group">
          <label for="customer-driving-license">Your Driving License (Optional)</label>
          <input type="text" id="customer-driving-license" name="customer_driving_license"
            placeholder="Enter your driving license number"
            value="<?php echo isset($_POST['customer_driving_license']) ? htmlspecialchars($_POST['customer_driving_license']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="customer-name">Your Name *</label>
          <input type="text" id="customer-name" name="customer_name" required
            placeholder="Enter your full name"
            value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ($user_logged_in ? htmlspecialchars($user_name) : ''); ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="customer-email">Email *</label>
            <input type="email" id="customer-email" name="customer_email" required
              placeholder="Enter your email"
              value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>">
          </div>

          <div class="form-group">
            <label for="customer-phone">Phone Number *</label>
            <input type="tel" id="customer-phone" name="customer_phone" required
              placeholder="Enter your phone number"
              value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>">
          </div>
        </div>

        <div style="margin-top: 24px;">
          <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 16px;">
            <i class="ri-information-line"></i>
            We'll contact you within 24 hours to confirm your booking.
          </p>
          <button type="submit" class="submit-booking" style="width: 100%;">
            <i class="ri-calendar-check-line"></i> Confirm Booking
          </button>
        </div>
      </form>
    </div>
  </div>

   <!-- FOOTER -->
<?php include '../admin/includes/footer.php'; ?>

  <!-- Linking Swiper script -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <!--=============== MAIN JS ===============-->
  <script src="../assets/js/main.js"></script>

  <script>
    // Car filtering
    const carFilters = {
      type: "all",
      capacity: "all"
    };

    function filterCars() {
      const cars = document.querySelectorAll(".car-card");
      let visibleCount = 0;

      cars.forEach(car => {
        const type = car.getAttribute("data-type");
        const capacity = car.getAttribute("data-capacity");

        const match =
          (carFilters.type === "all" || carFilters.type === type) &&
          (carFilters.capacity === "all" || parseInt(carFilters.capacity) === parseInt(capacity));

        if (match) {
          car.style.display = "flex";
          visibleCount++;
        } else {
          car.style.display = "none";
        }
      });

      // Show message if no cars match filters
      const carsContainer = document.getElementById("cars-container");
      let noResultsMsg = carsContainer.querySelector('.no-results-message');

      if (visibleCount === 0) {
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('div');
          noResultsMsg.className = 'no-results-message';
          noResultsMsg.style.cssText = 'grid-column: 1 / -1; text-align: center; padding: 60px; color: #64748b;';
          noResultsMsg.innerHTML = `
                        <i class="ri-search-line" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                        <h3>No cars match your filters</h3>
                        <p>Try adjusting your search criteria or <button onclick="resetCarFilters()" style="background: none; border: none; color: #2563eb; cursor: pointer; text-decoration: underline;">reset filters</button>.</p>
                    `;
          carsContainer.appendChild(noResultsMsg);
        }
      } else if (noResultsMsg) {
        noResultsMsg.remove();
      }
    }

    document.getElementById("car-type").addEventListener("change", (e) => {
      carFilters.type = e.target.value;
      filterCars();
    });

    document.getElementById("car-capacity").addEventListener("change", (e) => {
      carFilters.capacity = e.target.value;
      filterCars();
    });

    function resetCarFilters() {
      carFilters.type = "all";
      carFilters.capacity = "all";
      document.getElementById("car-type").value = "all";
      document.getElementById("car-capacity").value = "all";
      filterCars();
    }

    // Booking modal functionality
    const modal = document.getElementById("booking-modal");
    const bookButtons = document.querySelectorAll(".book-btn");
    const pickupDate = document.getElementById("pickup-date");
    const returnDate = document.getElementById("return-date");
    const bookingForm = document.getElementById("booking-form");

    let selectedCarPrice = 0;

    // Set minimum date as today
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    const formatDate = (date) => {
      return date.toISOString().split('T')[0];
    };

    pickupDate.min = formatDate(today);
    returnDate.min = formatDate(tomorrow);

    // Initialize pickup and return dates
    pickupDate.value = formatDate(tomorrow);
    returnDate.value = formatDate(new Date(tomorrow.getTime() + 86400000)); // +2 days

    // Open modal when book button is clicked
    function openBookingModal(button) {
      const carId = button.getAttribute('data-car-id');
      const carName = button.getAttribute('data-car-name');
      const carPrice = button.getAttribute('data-car-price');

      selectedCarPrice = parseFloat(carPrice);

      document.getElementById('car_id').value = carId;
      document.getElementById('selected-car-name').textContent = carName;
      document.getElementById('selected-car-price').textContent = `₹${selectedCarPrice.toFixed(2)} per day`;
      document.getElementById('price-per-day').textContent = `₹${selectedCarPrice.toFixed(2)}`;

      // Update price calculation
      updatePriceCalculation();

      modal.style.display = "flex";
      document.body.style.overflow = "hidden";
    }

    // Close modal
    function closeBookingModal() {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
      bookingForm.reset();

      // Reset dates to default
      pickupDate.value = formatDate(tomorrow);
      returnDate.value = formatDate(new Date(tomorrow.getTime() + 86400000));
    }

    // Close modal if clicked outside
    window.addEventListener('click', function(event) {
      if (event.target == modal) {
        closeBookingModal();
      }
    });

    // Date change handlers for price calculation
    function updatePriceCalculation() {
      const pickup = new Date(pickupDate.value);
      const returnD = new Date(returnDate.value);

      if (pickup && returnD && returnD > pickup && selectedCarPrice > 0) {
        const timeDiff = returnD.getTime() - pickup.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

        document.getElementById('rental-days').textContent = `${daysDiff} day${daysDiff !== 1 ? 's' : ''}`;
        document.getElementById('total-amount').textContent = `₹${(selectedCarPrice * daysDiff).toFixed(2)}`;

        document.getElementById('price-calculation').style.display = 'block';
      } else {
        document.getElementById('price-calculation').style.display = 'none';
      }
    }

    pickupDate.addEventListener('change', function() {
      // Set return date minimum to pickup date + 1 day
      const minReturnDate = new Date(this.value);
      minReturnDate.setDate(minReturnDate.getDate() + 1);
      returnDate.min = formatDate(minReturnDate);

      // If current return date is before new minimum, update it
      if (new Date(returnDate.value) < minReturnDate) {
        returnDate.value = formatDate(minReturnDate);
      }

      updatePriceCalculation();
    });

    returnDate.addEventListener('change', updatePriceCalculation);

    // Form validation
    bookingForm.addEventListener('submit', function(e) {
      // Basic validation
      const pickup = new Date(pickupDate.value);
      const returnD = new Date(returnDate.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (pickup < today) {
        e.preventDefault();
        alert('Pickup date cannot be in the past.');
        pickupDate.focus();
        return false;
      }

      if (returnD <= pickup) {
        e.preventDefault();
        alert('Return date must be after pickup date.');
        returnDate.focus();
        return false;
      }

      const phone = document.getElementById('customer-phone').value;
      const phoneRegex = /^[0-9]{10}$/;
      if (!phoneRegex.test(phone)) {
        e.preventDefault();
        alert('Please enter a valid 10-digit phone number.');
        document.getElementById('customer-phone').focus();
        return false;
      }

      // Show loading state
      const submitBtn = this.querySelector('.submit-booking');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Processing...';
      submitBtn.disabled = true;

      // Re-enable button after 5 seconds if form doesn't submit
      setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }, 5000);

      return true;
    });

    // Auto-hide notification after 5 seconds
    setTimeout(() => {
      const notification = document.querySelector('.booking-notification');
      if (notification) {
        notification.style.display = 'none';
      }
    }, 5000);

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Filter cars on page load
      filterCars();

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
    });

    // Add CSS for spinner
    const style = document.createElement('style');
    style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
    document.head.appendChild(style);
  </script>
</body>

</html>