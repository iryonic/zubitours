<?php
session_start();
require_once '../includes/connection.php'; // Use your existing connection


// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_image'])) {
        // Handle image upload
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $categories = isset($_POST['categories']) ? implode(' ', $_POST['categories']) : '';
        $display_order = intval($_POST['display_order']);
        $is_active = 1; // Default active
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../upload/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $fileName = time() . '_' . uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExt, $allowedTypes)) {
                // Check file size (5MB limit)
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $_SESSION['error'] = 'File size must be less than 5MB.';
                } else {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imagePath = 'upload/gallery/' . $fileName;
                        
                        // Insert into database
                        $sql = "INSERT INTO gallery (title, description, image_path, categories, display_order, is_active) 
                                VALUES ('$title', '$description', '$imagePath', '$categories', $display_order, $is_active)";
                        
                        if (mysqli_query($conn, $sql)) {
                            $_SESSION['success'] = 'Image added successfully!';
                            header('Location: manage-gallery.php');
                            exit();
                        } else {
                            $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
                            // Delete uploaded file if database insert fails
                            if (file_exists($targetPath)) {
                                unlink($targetPath);
                            }
                        }
                    } else {
                        $_SESSION['error'] = 'Failed to upload image.';
                    }
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP';
            }
        } else {
            $_SESSION['error'] = 'Please select an image file.';
        }
    } 
    elseif (isset($_POST['update_image'])) {
        $id = intval($_POST['id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $categories = isset($_POST['categories']) ? implode(' ', $_POST['categories']) : '';
        $display_order = intval($_POST['display_order']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Check if new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Handle new image upload
            $uploadDir = '../upload/gallery/';
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $fileName = time() . '_' . uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExt, $allowedTypes)) {
                // Check file size
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $_SESSION['error'] = 'File size must be less than 5MB.';
                    header('Location: manage-gallery.php');
                    exit();
                }
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = '../upload/gallery/' . $fileName;
                    
                    // Get old image path
                    $oldImageQuery = "SELECT image_path FROM gallery WHERE id = $id";
                    $oldImageResult = mysqli_query($conn, $oldImageQuery);
                    if ($oldImageRow = mysqli_fetch_assoc($oldImageResult)) {
                        // Delete old image file
                        if ($oldImageRow['image_path'] && file_exists('../' . $oldImageRow['image_path'])) {
                            unlink('../' . $oldImageRow['image_path']);
                        }
                    }
                    
                    // Update with new image
                    $sql = "UPDATE gallery SET 
                            title = '$title', 
                            description = '$description', 
                            image_path = '$imagePath', 
                            categories = '$categories', 
                            display_order = $display_order, 
                            is_active = $is_active 
                            WHERE id = $id";
                } else {
                    $_SESSION['error'] = 'Failed to upload new image.';
                    header('Location: manage-gallery.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP';
                header('Location: manage-gallery.php');
                exit();
            }
        } else {
            // Update without changing image
            $sql = "UPDATE gallery SET 
                    title = '$title', 
                    description = '$description', 
                    categories = '$categories', 
                    display_order = $display_order, 
                    is_active = $is_active 
                    WHERE id = $id";
        }
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = 'Image updated successfully!';
        } else {
            $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
        }
        
        header('Location: manage-gallery.php');
        exit();
    } 
    elseif (isset($_POST['delete_image'])) {
        $id = intval($_POST['id']);
        
        // Get image path before deleting
        $sql = "SELECT image_path FROM gallery WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $imagePath = $row['image_path'];
            
            // Delete from database
            $deleteSql = "DELETE FROM gallery WHERE id = $id";
            if (mysqli_query($conn, $deleteSql)) {
                // Delete file from server
                if ($imagePath && file_exists('../' . $imagePath)) {
                    unlink('../' . $imagePath);
                }
                $_SESSION['success'] = 'Image deleted successfully!';
            } else {
                $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
            }
        } else {
            $_SESSION['error'] = 'Image not found!';
        }
        
        header('Location: manage-gallery.php');
        exit();
    }
}

// Fetch all gallery images
$sql = "SELECT * FROM gallery ORDER BY display_order ASC, created_at DESC";
$result = mysqli_query($conn, $sql);
$galleryItems = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $galleryItems[] = $row;
    }
}

// Define categories
$allCategories = ['kashmir', 'ladakh', 'lakes', 'mountains', 'culture', 'adventure'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
     <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Include Header -->
        <?php include '../includes/header.php'; ?>

        <div class="content">
            <!-- Page Header -->
            <div class="page-header" style="margin-bottom: 30px;">
                <h1 class="page-title">Manage Gallery</h1>
                <p style="color: var(--text-secondary); margin-top: 10px;">Add, edit, or remove gallery images</p>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message message-success">
                    <i class="ri-checkbox-circle-line"></i> <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message message-error">
                    <i class="ri-error-warning-line"></i> <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Add New Image Form -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px; color: var(--text-primary); display: flex; align-items: center; gap: 10px;">
                    <i class="ri-add-circle-line"></i> Add New Image
                </h2>
                <form action="" method="POST" enctype="multipart/form-data" id="addForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title"><i class="ri-image-line"></i> Image Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required 
                                   placeholder="Enter image title">
                        </div>
                        <div class="form-group">
                            <label for="display_order"><i class="ri-list-ordered"></i> Display Order</label>
                            <input type="number" id="display_order" name="display_order" class="form-control" value="0" 
                                   placeholder="Order number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="ri-file-text-line"></i> Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" 
                                  placeholder="Optional description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="ri-price-tag-3-line"></i> Categories *</label>
                        <div class="checkbox-group">
                            <?php foreach ($allCategories as $category): ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="categories[]" value="<?= $category ?>">
                                    <span style="text-transform: capitalize;"><?= $category ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="ri-upload-line"></i> Image *</label>
                        <div class="file-input-wrapper">
                            <div class="file-input-custom">
                                <i class="ri-image-add-line"></i>
                                <span class="file-name" id="fileName">Choose an image file...</span>
                                <i class="ri-folder-open-line"></i>
                            </div>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" required
                                   onchange="updateFileName(this)">
                        </div>
                        
                        <div class="upload-info">
                            <i class="ri-information-line"></i>
                            <strong>Upload Requirements:</strong><br>
                            • Allowed formats: JPG, PNG, GIF, WEBP<br>
                            • Maximum file size: 5MB<br>
                            • Images will be saved to: <code>upload/gallery/</code><br>
                            • Recommended dimensions: 800x600px or larger
                        </div>
                        
                        <img id="preview" class="image-preview" style="display: none;">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_image" class="btn btn-primary">
                            <i class="ri-upload-line"></i> Upload Image
                        </button>
                        <button type="reset" class="btn btn-secondary" onclick="clearPreview()">
                            <i class="ri-refresh-line"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Gallery Images Grid -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--text-primary); display: flex; align-items: center; gap: 10px;">
                    <i class="ri-gallery-line"></i> Gallery Images <span style="font-size: 0.9em; color: var(--text-secondary);">(<?= count($galleryItems) ?>)</span>
                </h2>
                <?php if (!empty($galleryItems)): ?>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">
                        <i class="ri-folder-line"></i> Location: <code>upload/gallery/</code>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (empty($galleryItems)): ?>
                <div class="form-container" style="text-align: center; padding: 40px;">
                    <i class="ri-image-line" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-secondary); margin-bottom: 10px;">No Images Found</h3>
                    <p style="color: var(--text-secondary);">Upload your first image using the form above.</p>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 10px;">
                        <i class="ri-information-line"></i> Images will be saved to: <code>upload   /gallery/</code> folder
                    </p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($galleryItems as $item): 
                        $imagePath = '../' . $item['image_path'];
                        // Check if file exists
                        $fileExists = file_exists($imagePath);
                        $fileSize = $fileExists ? filesize($imagePath) : 0;
                        $fileSizeFormatted = $fileSize ? round($fileSize / 1024, 1) . ' KB' : 'N/A';
                    ?>
                    <div class="gallery-card">
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($imagePath) ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 class="gallery-image"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=Image+Not+Found'">
                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                #<?= $item['display_order'] ?>
                            </div>
                            <?php if (!$fileExists): ?>
                                <div style="position: absolute; top: 10px; left: 10px; background: rgba(239, 68, 68, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    <i class="ri-error-warning-line"></i> File Missing
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="gallery-info">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <span class="status-badge <?= $item['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $item['is_active'] ? '✓ Active' : '✗ Inactive' ?>
                                </span>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                    <i class="ri-file-info-line"></i> <?= $fileSizeFormatted ?>
                                </div>
                            </div>
                            
                            <h3 class="gallery-title"><?= htmlspecialchars($item['title']) ?></h3>
                            
                            <?php if ($item['description']): ?>
                                <p class="gallery-description"><?= htmlspecialchars($item['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="gallery-categories">
                                <?php 
                                $categories = explode(' ', $item['categories']);
                                foreach ($categories as $cat):
                                    if (trim($cat)): ?>
                                        <span class="category-tag"><?= htmlspecialchars(ucfirst($cat)) ?></span>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                            
                            <div class="gallery-meta" style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 15px;">
                                <div><i class="ri-calendar-line"></i> Added: <?= date('M d, Y', strtotime($item['created_at'])) ?></div>
                                <?php if ($item['updated_at'] != $item['created_at']): ?>
                                    <div><i class="ri-refresh-line"></i> Updated: <?= date('M d, Y', strtotime($item['updated_at'])) ?></div>
                                <?php endif; ?>
                                <div style="font-family: monospace; font-size: 0.8rem; margin-top: 5px; background: var(--bg-primary); padding: 4px 8px; border-radius: 4px;">
                                    <i class="ri-folder-line"></i> <?= htmlspecialchars($item['image_path']) ?>
                                </div>
                            </div>
                            
                            <div class="gallery-actions">
                                <button type="button" class="btn btn-primary btn-sm edit-btn" 
                                        data-id="<?= $item['id'] ?>"
                                        data-title="<?= htmlspecialchars($item['title']) ?>"
                                        data-description="<?= htmlspecialchars($item['description']) ?>"
                                        data-categories='<?= json_encode(explode(' ', $item['categories'])) ?>'
                                        data-display_order="<?= $item['display_order'] ?>"
                                        data-is_active="<?= $item['is_active'] ?>"
                                        data-image_path="<?= htmlspecialchars($item['image_path']) ?>">
                                    <i class="ri-edit-line"></i> Edit
                                </button>
                                
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="delete_image" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to delete this image? This will remove both the database entry and the file from upload/gallery/ folder.')">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="ri-edit-line"></i> Edit Image</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="update_image" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editTitle"><i class="ri-image-line"></i> Image Title *</label>
                        <input type="text" id="editTitle" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editDisplayOrder"><i class="ri-list-ordered"></i> Display Order</label>
                        <input type="number" id="editDisplayOrder" name="display_order" class="form-control" value="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editDescription"><i class="ri-file-text-line"></i> Description</label>
                    <textarea id="editDescription" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="ri-price-tag-3-line"></i> Categories *</label>
                    <div class="checkbox-group" id="editCategories">
                        <?php foreach ($allCategories as $category): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="categories[]" value="<?= $category ?>">
                                <span style="text-transform: capitalize;"><?= $category ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editIsActive"><i class="ri-toggle-line"></i> Status</label>
                        <select id="editIsActive" name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="ri-image-add-line"></i> New Image</label>
                        <div class="file-input-wrapper">
                            <div class="file-input-custom">
                                <i class="ri-image-add-line"></i>
                                <span class="file-name" id="editFileName">Keep current image or choose new...</span>
                                <i class="ri-folder-open-line"></i>
                            </div>
                            <input type="file" id="editImage" name="image" class="form-control" accept="image/*"
                                   onchange="updateEditFileName(this)">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="ri-image-line"></i> Current Image</label>
                    <div style="background: var(--bg-primary); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <img id="currentImage" class="image-preview" style="display: block; margin-bottom: 10px;">
                        <div style="font-size: 0.9rem; color: var(--text-secondary); font-family: monospace;">
                            <i class="ri-folder-line"></i> <span id="currentImagePath"></span>
                        </div>
                    </div>
                    <small style="color: var(--text-secondary); display: block; margin-top: 5px;">
                        <i class="ri-information-line"></i> Leave new image field empty to keep current image
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line"></i> Update Image
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="ri-close-line"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update file name display
            function updateFileName(input) {
                const fileName = input.files[0] ? input.files[0].name : 'Choose an image file...';
                document.getElementById('fileName').textContent = fileName;
                
                // Preview image
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            function updateEditFileName(input) {
                const fileName = input.files[0] ? input.files[0].name : 'Keep current image or choose new...';
                document.getElementById('editFileName').textContent = fileName;
            }
            
            window.updateFileName = updateFileName;
            window.updateEditFileName = updateEditFileName;
            
            // Clear preview
            function clearPreview() {
                const preview = document.getElementById('preview');
                preview.style.display = 'none';
                preview.src = '';
                document.getElementById('fileName').textContent = 'Choose an image file...';
            }
            window.clearPreview = clearPreview;
            
            // Edit button click handlers
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const title = this.dataset.title;
                    const description = this.dataset.description;
                    const categories = JSON.parse(this.dataset.categories || '[]');
                    const displayOrder = this.dataset.display_order;
                    const isActive = this.dataset.is_active;
                    const imagePath = this.dataset.image_path;
                    
                    // Set form values
                    document.getElementById('editId').value = id;
                    document.getElementById('editTitle').value = title;
                    document.getElementById('editDescription').value = description;
                    document.getElementById('editDisplayOrder').value = displayOrder;
                    document.getElementById('editIsActive').value = isActive;
                    
                    // Show current image
                    const currentImage = document.getElementById('currentImage');
                    const currentImagePath = document.getElementById('currentImagePath');
                    if (imagePath) {
                        currentImage.src = '../' + imagePath;
                        currentImage.style.display = 'block';
                        currentImagePath.textContent = imagePath;
                    }
                    
                    // Reset checkboxes
                    document.querySelectorAll('#editCategories input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Set category checkboxes
                    categories.forEach(category => {
                        if (category.trim()) {
                            const checkbox = document.querySelector(`#editCategories input[value="${category.trim()}"]`);
                            if (checkbox) checkbox.checked = true;
                        }
                    });
                    
                    // Reset file input
                    document.getElementById('editImage').value = '';
                    document.getElementById('editFileName').textContent = 'Keep current image or choose new...';
                    
                    // Show modal
                    document.getElementById('editModal').style.display = 'flex';
                });
            });
            
            // Close modal on outside click
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            // Form validation
            document.getElementById('addForm').addEventListener('submit', function(e) {
                // Check categories
                const checkboxes = this.querySelectorAll('input[name="categories[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one category.');
                    return false;
                }
                
                // Check file
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Please select an image file.');
                    return false;
                }
                
                // Check file size (5MB limit)
                const file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('File size must be less than 5MB.');
                    return false;
                }
            });
            
            document.getElementById('editForm').addEventListener('submit', function(e) {
                // Check categories
                const checkboxes = this.querySelectorAll('input[name="categories[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one category.');
                    return false;
                }
                
                // Check new file size if selected
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    if (file.size > 5 * 1024 * 1024) {
                        e.preventDefault();
                        alert('File size must be less than 5MB.');
                        return false;
                    }
                }
            });
            
            // Dark mode support
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');
                    const themeIcon = this.querySelector('i');
                    if (document.body.classList.contains('dark-mode')) {
                        themeIcon.classList.replace('ri-moon-line', 'ri-sun-line');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        themeIcon.classList.replace('ri-sun-line', 'ri-moon-line');
                        localStorage.setItem('theme', 'light');
                    }
                });
                
                // Check saved theme
                if (localStorage.getItem('theme') === 'dark') {
                    document.body.classList.add('dark-mode');
                    const themeIcon = themeToggle.querySelector('i');
                    if (themeIcon) themeIcon.classList.replace('ri-moon-line', 'ri-sun-line');
                }
            }
        });
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        window.closeModal = closeModal;
        
        // Handle image errors in gallery
        document.querySelectorAll('.gallery-image').forEach(img => {
            img.addEventListener('error', function() {
                this.src = 'https://via.placeholder.com/400x300?text=Image+Not+Found';
                this.style.border = '2px solid var(--error-color)';
            });
        });
    </script>
</body>
</html>