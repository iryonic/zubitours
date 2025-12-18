<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

require_once '../includes/connection.php';




// Handle CRUD operations
$message = '';
$message_type = '';

// Add new car
if (isset($_POST['add_car'])) {
    $car_name = $_POST['car_name'];
    $car_type = $_POST['car_type'];
    $capacity = $_POST['capacity'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/cars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['car_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target_file)) {
            $image_path = 'cars/' . $file_name;
        } else {
            // Optional: capture error for debugging
            error_log('Failed to move uploaded car image to: ' . $target_file);
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO car_rentals (car_name, car_type, capacity, transmission, fuel_type, price_per_day, description, image_path, badge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssdss", $car_name, $car_type, $capacity, $transmission, $fuel_type, $price_per_day, $description, $image_path, $badge);
    
    if ($stmt->execute()) {
        $message = "Car added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding car: " . $conn->error;
        $message_type = "error";
    }
}

// Update car
if (isset($_POST['update_car'])) {
    $id = $_POST['car_id'];
    $car_name = $_POST['car_name'];
    $car_type = $_POST['car_type'];
    $capacity = $_POST['capacity'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle image upload if new image is provided
    $image_query = "";
    $params = [];
    // Types string for UPDATE without image: (car_name, car_type, capacity, transmission, fuel_type, price_per_day, description, badge, is_available, id)
    // s, s, i, s, s, d, s, s, i, i
    $types = "ssissdssii";
    
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        // fetch old image path so we can remove it after successful upload
        $oldRes = $conn->query("SELECT image_path FROM car_rentals WHERE id = $id");
        $oldImage = ($oldRes && $oldRes->num_rows) ? $oldRes->fetch_assoc()['image_path'] : '';

        $upload_dir = '../upload/cars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['car_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target_file)) {
            $image_path = 'cars/' . $file_name;
            $image_query = ", image_path = ?";
            // Types string for UPDATE with image: (car_name, car_type, capacity, transmission, fuel_type, price_per_day, description, badge, is_available, image_path, id)
            // s, s, i, s, s, d, s, s, i, s, i
            $types = "ssissdssisi";
            $params[] = $image_path;

            // remove old image file
            if (!empty($oldImage)) {
                $oldFile = '../upload/' . $oldImage;
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
        } else {
            error_log('Failed to move uploaded car image to: ' . $target_file);
        }
    }
    
    $stmt = $conn->prepare("UPDATE car_rentals SET car_name = ?, car_type = ?, capacity = ?, transmission = ?, fuel_type = ?, price_per_day = ?, description = ?, badge = ?, is_available = ? $image_query WHERE id = ?");
    
    if ($image_query) {
        $stmt->bind_param($types, $car_name, $car_type, $capacity, $transmission, $fuel_type, $price_per_day, $description, $badge, $is_available, $image_path, $id);
    } else {
        $stmt->bind_param($types, $car_name, $car_type, $capacity, $transmission, $fuel_type, $price_per_day, $description, $badge, $is_available, $id);
    }
    
    if ($stmt->execute()) {
        $message = "Car updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating car: " . $conn->error;
        $message_type = "error";
    }
}

// Delete car
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First, delete bookings for this car
    $conn->query("DELETE FROM car_bookings WHERE car_id = $id");
    
    // Remove image file if exists
    $imgRes = $conn->query("SELECT image_path FROM car_rentals WHERE id = $id");
    if ($imgRes && $imgRes->num_rows > 0) {
        $imgRow = $imgRes->fetch_assoc();
        if (!empty($imgRow['image_path'])) {
            $imgFile = '../../upload/' . $imgRow['image_path'];
            if (file_exists($imgFile)) {
                @unlink($imgFile);
            }
        }
    }

    // Then delete the car
    if ($conn->query("DELETE FROM car_rentals WHERE id = $id")) {
        $message = "Car deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting car: " . $conn->error;
        $message_type = "error";
    }
}

// Update booking status
if (isset($_POST['update_booking_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE car_bookings SET status = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $notes, $booking_id);
    
    if ($stmt->execute()) {
        $message = "Booking status updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating booking: " . $conn->error;
        $message_type = "error";
    }
}

// Fetch all cars
$cars = $conn->query("SELECT * FROM car_rentals ORDER BY created_at DESC");

// Fetch all bookings
$bookings = $conn->query("
    SELECT cb.*, cr.car_name 
    FROM car_bookings cb 
    JOIN car_rentals cr ON cb.car_id = cr.id 
    ORDER BY cb.booking_date DESC
");

// Get stats
$total_cars = $conn->query("SELECT COUNT(*) as count FROM car_rentals")->fetch_assoc()['count'];
$available_cars = $conn->query("SELECT COUNT(*) as count FROM car_rentals WHERE is_available = 1")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM car_bookings")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM car_bookings WHERE status = 'pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Car Rentals - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
     <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <!-- Sidebar (same as adminpannel.php) -->
   <?php
        include '../includes/sidebar.php';
   ?>

    <!-- Main Content -->
    <div class="main-content">
            <?php include '../includes/header.php'; ?>

        <div class="content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="ri-<?php echo $message_type == 'success' ? 'check' : 'close'; ?>-circle-fill"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <div class="section-header">
                <h1 class="section-title">Manage Car Rentals</h1>
                <button class="btn btn-primary" onclick="openAddCarModal()">
                    <i class="ri-add-line"></i> Add New Car
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon cars-icon">
                        <i class="ri-car-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_cars; ?></h3>
                        <p>Total Cars</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available-icon">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $available_cars; ?></h3>
                        <p>Available Cars</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pending_bookings; ?></h3>
                        <p>Pending Bookings</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('cars')">Cars List</div>
                <div class="tab" onclick="switchTab('bookings')">Bookings</div>
            </div>

            <!-- Cars List Tab -->
            <div id="cars-tab" class="tab-content active">
                <div class="cards-grid">
                    <?php while ($car = $cars->fetch_assoc()): ?>
                        <div class="card car-card">
                            <div class="car-image">
                                <img src="<?php echo !empty($car['image_path']) ? '../upload/' . $car['image_path'] : '../../assets/img/car1.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($car['car_name']); ?>"
                                     onerror="this.src='../../assets/img/car1.jpg'">
                                <?php if ($car['badge']): ?>
                                    <span class="car-badge"><?php echo $car['badge']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="car-info">
                                <h3><?php echo htmlspecialchars($car['car_name']); ?></h3>
                                <div class="car-specs">
                                    <span><i class="ri-user-line"></i> <?php echo $car['capacity']; ?> Seater</span>
                                    <span><i class="ri-settings-3-line"></i> <?php echo ucfirst($car['transmission']); ?></span>
                                    <span><i class="ri-gas-station-line"></i> <?php echo ucfirst($car['fuel_type']); ?></span>
                                </div>
                                <div class="car-price">₹<?php echo number_format($car['price_per_day'], 2); ?> <span>/day</span></div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 16px;">
                                    <?php echo htmlspecialchars(substr($car['description'], 0, 100)) . '...'; ?>
                                </p>
                                <div class="car-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editCar(<?php echo $car['id']; ?>)">
                                        <i class="ri-edit-line"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteCar(<?php echo $car['id']; ?>)">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Bookings Tab -->
            <div id="bookings-tab" class="tab-content">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Car</th>
                                <th>Customer</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($booking['car_name']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br>
                                        <small><?php echo $booking['customer_email']; ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?> - <br>
                                        <?php echo date('M d, Y', strtotime($booking['return_date'])); ?>
                                    </td>
                                    <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="updateBookingStatus(<?php echo $booking['id']; ?>)">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Car Modal -->
    <div class="modal" id="car-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add New Car</h2>
            <form id="car-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="car_id" name="car_id">
                <!-- form-action name will be set dynamically to either 'add_car' or 'update_car' -->
                <input type="hidden" id="form-action" name="add_car" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="car_name">Car Name *</label>
                        <input type="text" id="car_name" name="car_name" required>
                    </div>
                    <div class="form-group">
                        <label for="car_type">Car Type *</label>
                        <select id="car_type" name="car_type" required>
                            <option value="">Select Type</option>
                            <option value="suv">SUV</option>
                            <option value="sedan">Sedan</option>
                            <option value="luxury">Luxury</option>
                            <option value="economy">Economy</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="capacity">Capacity (Seaters) *</label>
                        <input type="number" id="capacity" name="capacity" min="1" max="50" required>
                    </div>
                    <div class="form-group">
                        <label for="transmission">Transmission *</label>
                        <select id="transmission" name="transmission" required>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fuel_type">Fuel Type *</label>
                        <select id="fuel_type" name="fuel_type" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price_per_day">Price per Day (₹) *</label>
                        <input type="number" id="price_per_day" name="price_per_day" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="badge">Badge (Optional)</label>
                        <select id="badge" name="badge">
                            <option value="">No Badge</option>
                            <option value="Popular">Popular</option>
                            <option value="Group">Group</option>
                            <option value="Luxury">Luxury</option>
                            <option value="Economy">Economy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="car_image">Car Image</label>
                        <input type="file" id="car_image" name="car_image" accept="image/*">
                        <small style="color: var(--text-secondary);">Leave empty to keep current image</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_available" name="is_available" value="1" checked>
                        <label for="is_available">Available for booking</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-save-line"></i> Save Car
                </button>
            </form>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal" id="booking-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBookingModal()">&times;</span>
            <h2>Booking Details</h2>
            <div id="booking-details"></div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeStatusModal()">&times;</span>
            <h2>Update Booking Status</h2>
            <form id="status-form" method="POST">
                <input type="hidden" id="booking_id" name="booking_id">
                <input type="hidden" name="update_booking_status" value="1">
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Add any notes about this booking..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-check-line"></i> Update Status
                </button>
            </form>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            // Update tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab
            document.querySelector(`.tab:nth-child(${tabName === 'cars' ? 1 : 2})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }

        // Modal functions
        function openAddCarModal() {
            document.getElementById('modal-title').textContent = 'Add New Car';
            // Ensure form-action indicates an add operation
            const fa = document.getElementById('form-action');
            fa.name = 'add_car';
            fa.value = '1';
            document.getElementById('car-form').reset();
            document.getElementById('car-form').action = '';
            document.getElementById('car_id').value = '';
            document.getElementById('car-modal').classList.add('active');
        }

        function editCar(carId) {
            // Fetch car details via AJAX
            fetch(`../logic/get_car.php?id=${carId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal-title').textContent = 'Edit Car';
                    // Ensure form-action indicates an update operation
                    const fa = document.getElementById('form-action');
                    fa.name = 'update_car';
                    fa.value = '1';
                    document.getElementById('car_id').value = data.id;
                    document.getElementById('car_name').value = data.car_name;
                    document.getElementById('car_type').value = data.car_type;
                    document.getElementById('capacity').value = data.capacity;
                    document.getElementById('transmission').value = data.transmission;
                    document.getElementById('fuel_type').value = data.fuel_type;
                    document.getElementById('price_per_day').value = data.price_per_day;
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('badge').value = data.badge || '';
                    document.getElementById('is_available').checked = data.is_available == 1;
                    
                    document.getElementById('car-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading car details');
                });
        }

        // function deleteCar(carId) {
        //     if (confirm('Are you sure you want to delete this car? All related bookings will also be deleted.')) {
        //         window.location.href = `?delete=${carId}`;
        //     }
        // }

        function viewBooking(bookingId) {
            fetch(`../logic/get_booking.php?id=${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    const details = `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Booking Information</h3>
                            <p><strong>Booking ID:</strong> #${String(data.id).padStart(6, '0')}</p>
                            <p><strong>Car:</strong> ${data.car_name}</p>
                            <p><strong>Customer:</strong> ${data.customer_name}</p>
                            <p><strong>Email:</strong> ${data.customer_email}</p>
                            <p><strong>Phone:</strong> ${data.customer_phone}</p>
                            <p><strong>Driving License:</strong> ${data.customer_driving_license || 'N/A'}</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Trip Details</h3>
                            <p><strong>Pickup Location:</strong> ${data.pickup_location}</p>
                            <p><strong>Pickup Date:</strong> ${new Date(data.pickup_date).toLocaleDateString('en-US', { 
                                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                            })}</p>
                            <p><strong>Return Date:</strong> ${new Date(data.return_date).toLocaleDateString('en-US', { 
                                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                            })}</p>
                            <p><strong>Total Days:</strong> ${data.total_days}</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Payment Details</h3>
                            <p><strong>Price per Day:</strong> ₹${parseFloat(data.total_amount / data.total_days).toFixed(2)}</p>
                            <p><strong>Total Amount:</strong> ₹${parseFloat(data.total_amount).toFixed(2)}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-${data.status}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></p>
                        </div>
                        
                        ${data.notes ? `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Notes</h3>
                            <p>${data.notes}</p>
                        </div>
                        ` : ''}
                        
                        <div style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 24px;">
                            <p><strong>Booking Date:</strong> ${new Date(data.booking_date).toLocaleString()}</p>
                        </div>
                    `;
                    
                    document.getElementById('booking-details').innerHTML = details;
                    document.getElementById('booking-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading booking details');
                });
        }

        function updateBookingStatus(bookingId) {
            document.getElementById('booking_id').value = bookingId;
            document.getElementById('status-modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('car-modal').classList.remove('active');
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').classList.remove('active');
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.remove('active');
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Form validation
        document.getElementById('car-form').addEventListener('submit', function(e) {
            const price = document.getElementById('price_per_day').value;
            if (price < 0) {
                e.preventDefault();
                alert('Price cannot be negative');
                return false;
            }
            
            const capacity = document.getElementById('capacity').value;
            if (capacity < 1 || capacity > 50) {
                e.preventDefault();
                alert('Capacity must be between 1 and 50');
                return false;
            }
            
            return true;
        });

        // Initialize date pickers
        document.addEventListener('DOMContentLoaded', function() {
            // Set min dates for date inputs
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.min = today;
            });
        });
    </script>
</body>
</html>