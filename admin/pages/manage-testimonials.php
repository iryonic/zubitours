<?php
session_start();
require_once '../includes/connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$message_type = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message'], $_SESSION['message_type']);
}

// Handle Testimonial Actions (Add/Edit)
if (isset($_POST['save_testimonial'])) {
    $id = $_POST['testimonial_id'] ?? null;
    $author_name = $_POST['author_name'];
    $author_email = $_POST['author_email'];
    $package_name = $_POST['package_name'];
    $testimonial_text = $_POST['testimonial_text'];
    $rating = intval($_POST['rating']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = intval($_POST['display_order']);

    $avatar_path = $_POST['current_avatar'] ?? null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/testimonials/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = uniqid() . '_' . basename($_FILES['avatar']['name']);
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_dir . $file_name)) {
            $avatar_path = 'testimonials/' . $file_name;
        }
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE testimonials SET author_name = ?, author_email = ?, package_name = ?, testimonial_text = ?, rating = ?, avatar_path = ?, is_active = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("ssssisiii", $author_name, $author_email, $package_name, $testimonial_text, $rating, $avatar_path, $is_active, $display_order, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO testimonials (author_name, author_email, package_name, testimonial_text, rating, avatar_path, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisii", $author_name, $author_email, $package_name, $testimonial_text, $rating, $avatar_path, $is_active, $display_order);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = $id ? "Testimonial updated!" : "Testimonial added!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    header("Location: manage-testimonials.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM testimonials WHERE id = $id");
    $_SESSION['message'] = "Testimonial deleted!";
    $_SESSION['message_type'] = "success";
    header("Location: manage-testimonials.php");
    exit();
}

// Handle Toggle Status
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $conn->query("UPDATE testimonials SET is_active = 1 - is_active WHERE id = $id");
    header("Location: manage-testimonials.php");
    exit();
}

// Fetch Testimonials
$testimonials = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC");
$packages = $conn->query("SELECT package_name FROM packages WHERE is_active = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
        .testimonial-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 20px;
            position: relative;
        }
        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            background: #f1f5f9;
        }
        .testimonial-info {
            flex: 1;
        }
        .testimonial-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .rating-stars {
            color: #f59e0b;
        }
        .package-tag {
            font-size: 0.8rem;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
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
                <h1 class="section-title">Manage Testimonials</h1>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="ri-add-line"></i> Add Testimonial
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Author</th>
                            <th>Package</th>
                            <th>Testimonial</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($t = $testimonials->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="../upload/<?php echo $t['avatar_path'] ?: 'bg1.jpg'; ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" onerror="this.src='../../assets/img/user-avatar.png'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($t['author_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($t['author_email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="package-tag"><?php echo htmlspecialchars($t['package_name'] ?: 'General'); ?></span></td>
                            <td style="max-width: 300px;">
                                <div style="font-size: 0.9rem; color: #64748b;">
                                    <?php echo htmlspecialchars(substr($t['testimonial_text'], 0, 100)); ?>...
                                </div>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    <?php for($i=0; $i<$t['rating']; $i++): ?><i class="ri-star-fill"></i><?php endfor; ?>
                                </div>
                            </td>
                            <td>
                                <a href="?toggle_status=<?php echo $t['id']; ?>" class="status-badge <?php echo $t['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $t['is_active'] ? 'Active' : 'Inactive'; ?>
                                </a>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn btn-sm btn-primary" onclick='editTestimonial(<?php echo json_encode($t); ?>)'>
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <a href="?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this testimonial?')">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="testimonialModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Testimonial</h2>
            <form id="testimonialForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="save_testimonial" value="1">
                <input type="hidden" name="testimonial_id" id="testimonial_id">
                <input type="hidden" name="current_avatar" id="current_avatar">

                <div class="form-row">
                    <div class="form-group">
                        <label>Author Name *</label>
                        <input type="text" name="author_name" id="author_name" required>
                    </div>
                    <div class="form-group">
                        <label>Author Email</label>
                        <input type="email" name="author_email" id="author_email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Related Package</label>
                        <select name="package_name" id="package_name">
                            <option value="">General / None</option>
                            <?php 
                            mysqli_data_seek($packages, 0);
                            while($pk = $packages->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($pk['package_name']); ?>"><?php echo htmlspecialchars($pk['package_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rating *</label>
                        <select name="rating" id="rating" required>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Testimonial Text *</label>
                    <textarea name="testimonial_text" id="testimonial_text" rows="4" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Author Avatar</label>
                        <input type="file" name="avatar" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="display_order" value="0">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="is_active" id="is_active" checked>
                        Show on website
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Testimonial</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add Testimonial';
            document.getElementById('testimonialForm').reset();
            document.getElementById('testimonial_id').value = '';
            document.getElementById('testimonialModal').classList.add('active');
        }

        function editTestimonial(data) {
            document.getElementById('modalTitle').innerText = 'Edit Testimonial';
            document.getElementById('testimonial_id').value = data.id;
            document.getElementById('author_name').value = data.author_name;
            document.getElementById('author_email').value = data.author_email;
            document.getElementById('package_name').value = data.package_name;
            document.getElementById('rating').value = data.rating;
            document.getElementById('testimonial_text').value = data.testimonial_text;
            document.getElementById('display_order').value = data.display_order;
            document.getElementById('is_active').checked = data.is_active == 1;
            document.getElementById('current_avatar').value = data.avatar_path;
            
            document.getElementById('testimonialModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('testimonialModal').classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('testimonialModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
