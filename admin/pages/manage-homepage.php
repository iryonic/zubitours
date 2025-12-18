<?php
session_start();
require_once '../includes/connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Handle CRUD operations for homepage sections
$message = '';
$message_type = '';

// Restore flash messages from session (PRG)
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'] ?? '';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Update hero section
if (isset($_POST['update_hero'])) {
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $hero_description = $_POST['hero_description'];
    $hero_background = $_POST['hero_background'];
    
    // Handle image upload if provided
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/homepage/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['hero_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) {
            $hero_background = 'homepage/' . $file_name;
        } else {
            error_log("Failed to move hero image to $target_file. tmp_name: " . $_FILES['hero_image']['tmp_name'] . " error: " . $_FILES['hero_image']['error']);
            $message = "Error uploading hero image.";
            $message_type = "error";
        }
    }
    
    // Update or insert hero data
    $stmt = $conn->prepare("INSERT INTO homepage_sections (section_name, title, subtitle, description, background_image, updated_at) 
                           VALUES ('hero', ?, ?, ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           title = VALUES(title), 
                           subtitle = VALUES(subtitle), 
                           description = VALUES(description), 
                           background_image = VALUES(background_image),
                           updated_at = NOW()");
    $stmt->bind_param("ssss", $hero_title, $hero_subtitle, $hero_description, $hero_background);
    
    if ($stmt->execute()) {
        $message = "Hero section updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating hero section: " . $conn->error;
        $message_type = "error";
    }
}

// Update destinations section
if (isset($_POST['update_destinations'])) {
    $destinations_title = $_POST['destinations_title'];
    $destinations_subtitle = $_POST['destinations_subtitle'];
    $destinations_description = $_POST['destinations_description'];
    
    $stmt = $conn->prepare("INSERT INTO homepage_sections (section_name, title, subtitle, description, updated_at) 
                           VALUES ('destinations', ?, ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           title = VALUES(title), 
                           subtitle = VALUES(subtitle), 
                           description = VALUES(description),
                           updated_at = NOW()");
    $stmt->bind_param("sss", $destinations_title, $destinations_subtitle, $destinations_description);
    
    if ($stmt->execute()) {
        $message = "Destinations section updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating destinations section: " . $conn->error;
        $message_type = "error";
    }
}

// Update packages section
if (isset($_POST['update_packages'])) {
    $packages_title = $_POST['packages_title'];
    $packages_description = $_POST['packages_description'];
    
    $stmt = $conn->prepare("INSERT INTO homepage_sections (section_name, title, description, updated_at) 
                           VALUES ('packages', ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           title = VALUES(title), 
                           description = VALUES(description),
                           updated_at = NOW()");
    $stmt->bind_param("ss", $packages_title, $packages_description);
    
    if ($stmt->execute()) {
        $message = "Packages section updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating packages section: " . $conn->error;
        $message_type = "error";
    }
}

// Update gallery section
if (isset($_POST['update_gallery'])) {
    $gallery_title = $_POST['gallery_title'];
    $gallery_description = $_POST['gallery_description'];
    
    $stmt = $conn->prepare("INSERT INTO homepage_sections (section_name, title, description, updated_at) 
                           VALUES ('gallery', ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           title = VALUES(title), 
                           description = VALUES(description),
                           updated_at = NOW()");
    $stmt->bind_param("ss", $gallery_title, $gallery_description);
    
    if ($stmt->execute()) {
        $message = "Gallery section updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating gallery section: " . $conn->error;
        $message_type = "error";
    }
}

// Update brands section
if (isset($_POST['update_brands'])) {
    $brands_title = $_POST['brands_title'];
    $brands_description = $_POST['brands_description'];
    
    $stmt = $conn->prepare("INSERT INTO homepage_sections (section_name, title, description, updated_at) 
                           VALUES ('brands', ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           title = VALUES(title), 
                           description = VALUES(description),
                           updated_at = NOW()");
    $stmt->bind_param("ss", $brands_title, $brands_description);
    
    if ($stmt->execute()) {
        $message = "Brands section updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating brands section: " . $conn->error;
        $message_type = "error";
    }
}

// Update CTA section
if (isset($_POST['update_cta'])) {
    $cta_title = $_POST['cta_title'];
    $cta_description = $_POST['cta_description'];
    $cta_primary_text = $_POST['cta_primary_text'];
    $cta_primary_link = $_POST['cta_primary_link'];
    $cta_secondary_text = $_POST['cta_secondary_text'];
    $cta_secondary_link = $_POST['cta_secondary_link'];
    
    $meta_data = json_encode([
        'primary_text' => $cta_primary_text,
        'primary_link' => $cta_primary_link,
        'secondary_text' => $cta_secondary_text,
        'secondary_link' => $cta_secondary_link
    ]);
    
    $stmt = $conn->prepare("INSERT INTO homepage_sections (section_name, title, description, meta_data, updated_at) 
                           VALUES ('cta', ?, ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           title = VALUES(title), 
                           description = VALUES(description),
                           meta_data = VALUES(meta_data),
                           updated_at = NOW()");
    $stmt->bind_param("sss", $cta_title, $cta_description, $meta_data);
    
    if ($stmt->execute()) {
        $message = "CTA section updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating CTA section: " . $conn->error;
        $message_type = "error";
    }
}

// Manage homepage images (gallery)
if (isset($_POST['add_gallery_image'])) {
    $image_title = $_POST['image_title'];
    $image_location = $_POST['image_location'];
    $image_category = $_POST['image_category'];
    
    if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/homepage/gallery/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['gallery_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $target_file)) {
            $image_path = 'homepage/gallery/' . $file_name;
            
            $stmt = $conn->prepare("INSERT INTO homepage_gallery (image_path, title, location, category, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $image_path, $image_title, $image_location, $image_category);
            
            if ($stmt->execute()) {
                $_SESSION['flash_message'] = "Gallery image added successfully!";
                $_SESSION['flash_type'] = "success";
                header('Location: manage-homepage.php');
                exit();
            } else {
                $_SESSION['flash_message'] = "Error adding gallery image: " . $conn->error;
                $_SESSION['flash_type'] = "error";
                header('Location: manage-homepage.php');
                exit();
            }
        } else {
            error_log("Failed to move gallery image to $target_file. tmp_name: " . $_FILES['gallery_image']['tmp_name'] . " error: " . $_FILES['gallery_image']['error']);
            $_SESSION['flash_message'] = "Error uploading gallery image.";
            $_SESSION['flash_type'] = "error";
            header('Location: manage-homepage.php');
            exit();
        }
    }
}

// Delete gallery image
if (isset($_GET['delete_gallery_image'])) {
    $image_id = $_GET['delete_gallery_image'];
    
    // Get image path to delete file
    $image_result = $conn->query("SELECT image_path FROM homepage_gallery WHERE id = $image_id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc();
        $file_path = '../upload/' . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    if ($conn->query("DELETE FROM homepage_gallery WHERE id = $image_id")) {
        $_SESSION['flash_message'] = "Gallery image deleted successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-homepage.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error deleting gallery image: " . $conn->error;
        $_SESSION['flash_type'] = "error";
        header('Location: manage-homepage.php');
        exit();
    }
}

// Manage brands
if (isset($_POST['add_brand'])) {
    $brand_name = $_POST['brand_name'];
    $brand_url = $_POST['brand_url'] ?? '';
    
    if (isset($_FILES['brand_logo']) && $_FILES['brand_logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/homepage/brands/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['brand_logo']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['brand_logo']['tmp_name'], $target_file)) {
            $logo_path = 'homepage/brands/' . $file_name;
            
            $stmt = $conn->prepare("INSERT INTO homepage_brands (logo_path, brand_name, brand_url, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $logo_path, $brand_name, $brand_url);
            
            if ($stmt->execute()) {
                $_SESSION['flash_message'] = "Brand added successfully!";
                $_SESSION['flash_type'] = "success";
                header('Location: manage-homepage.php');
                exit();
            } else {
                $_SESSION['flash_message'] = "Error adding brand: " . $conn->error;
                $_SESSION['flash_type'] = "error";
                header('Location: manage-homepage.php');
                exit();
            }
        } else {
            error_log("Failed to move brand logo to $target_file. tmp_name: " . $_FILES['brand_logo']['tmp_name'] . " error: " . $_FILES['brand_logo']['error']);
            $_SESSION['flash_message'] = "Error uploading brand logo.";
            $_SESSION['flash_type'] = "error";
            header('Location: manage-homepage.php');
            exit();
        }
    }
}

// Delete brand
if (isset($_GET['delete_brand'])) {
    $brand_id = $_GET['delete_brand'];
    
    // Get logo path to delete file
    $brand_result = $conn->query("SELECT logo_path FROM homepage_brands WHERE id = $brand_id");
    if ($brand_result->num_rows > 0) {
        $brand = $brand_result->fetch_assoc();
        $file_path = '../upload/' . $brand['logo_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    if ($conn->query("DELETE FROM homepage_brands WHERE id = $brand_id")) {
        $_SESSION['flash_message'] = "Brand deleted successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-homepage.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error deleting brand: " . $conn->error;
        $_SESSION['flash_type'] = "error";
        header('Location: manage-homepage.php');
        exit();
    }
}

// Fetch all homepage sections
$sections = $conn->query("SELECT * FROM homepage_sections");
$homepage_data = [];
while ($section = $sections->fetch_assoc()) {
    $homepage_data[$section['section_name']] = $section;
}

// Fetch gallery images
$gallery_images = $conn->query("SELECT * FROM homepage_gallery ORDER BY created_at DESC");

// Fetch brands
$brands = $conn->query("SELECT * FROM homepage_brands ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Homepage - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

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
                <h1 class="section-title">Manage Homepage</h1>
                <div>
                    
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon cars-icon">
                        <i class="ri-image-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $gallery_images->num_rows; ?></h3>
                        <p>Gallery Images</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-building-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $brands->num_rows; ?></h3>
                        <p>Trusted Brands</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon revenue-icon">
                        <i class="ri-layout-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>6</h3>
                        <p>Page Sections</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available-icon">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($homepage_data); ?></h3>
                        <p>Sections Configured</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('hero')">
                    Hero Section
                </div>
                <div class="tab" onclick="switchTab('destinations')">
                    Destinations
                </div>
                <div class="tab" onclick="switchTab('packages')">
                    Packages
                </div>
                <div class="tab" onclick="switchTab('gallery')">
                    Gallery
                </div>
                <div class="tab" onclick="switchTab('brands')">
                    Trusted Brands
                </div>
                <div class="tab" onclick="switchTab('cta')">
                    CTA Section
                </div>
            </div>

            <!-- Hero Section Tab -->
            <div id="hero-tab" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h3>Hero Section Configuration</h3>
                        <p>Manage the main banner section of your homepage</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="hero_subtitle">Hero Subtitle *</label>
                                <input type="text" id="hero_subtitle" name="hero_subtitle" 
                                       value="<?php echo htmlspecialchars($homepage_data['hero']['subtitle'] ?? 'Welcome to Paradise'); ?>" 
                                       required>
                                <small>This appears above the main title (e.g., "Welcome to Paradise")</small>
                            </div>

                            <div class="form-group">
                                <label for="hero_title">Hero Title *</label>
                                <input type="text" id="hero_title" name="hero_title" 
                                       value="<?php echo htmlspecialchars($homepage_data['hero']['title'] ?? 'Discover Kashmir & Ladakh'); ?>" 
                                       required>
                                <small>The main headline of your homepage</small>
                            </div>

                            <div class="form-group">
                                <label for="hero_description">Hero Description *</label>
                                <textarea id="hero_description" name="hero_description" rows="3" required><?php echo htmlspecialchars($homepage_data['hero']['description'] ?? 'Experience the breathtaking landscapes, rich culture, and adventure activities in these stunning regions of India.'); ?></textarea>
                                <small>A brief description that appears below the title</small>
                            </div>

                            <div class="form-group">
                                <label for="hero_background">Current Background Image</label>
                                <div style="margin: 10px 0;">
                                    <?php if (!empty($homepage_data['hero']['background_image'])): ?>
                                        <img src="../upload/<?php echo $homepage_data['hero']['background_image']; ?>" 
                                             alt="Current Background" 
                                             style="max-width: 300px; border-radius: 8px;">
                                    <?php else: ?>
                                        <p>No background image set</p>
                                    <?php endif; ?>
                                </div>
                                
                                <label for="hero_image">Upload New Background Image</label>
                                <input type="file" id="hero_image" name="hero_image" accept="image/*">
                                <small>Recommended: 1920x1080px or larger. Leave empty to keep current image.</small>
                                <input type="hidden" name="hero_background" value="<?php echo $homepage_data['hero']['background_image'] ?? ''; ?>">
                            </div>

                            <div class="form-group">
                                <button type="submit" name="update_hero" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Hero Section
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Destinations Section Tab -->
            <div id="destinations-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Destinations Section</h3>
                        <p>Configure the destinations section that appears on homepage</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="destinations_subtitle">Section Subtitle *</label>
                                <input type="text" id="destinations_subtitle" name="destinations_subtitle" 
                                       value="<?php echo htmlspecialchars($homepage_data['destinations']['subtitle'] ?? 'Explore'); ?>" 
                                       required>
                                <small>Small text that appears above the title (e.g., "Explore")</small>
                            </div>

                            <div class="form-group">
                                <label for="destinations_title">Section Title *</label>
                                <input type="text" id="destinations_title" name="destinations_title" 
                                       value="<?php echo htmlspecialchars($homepage_data['destinations']['title'] ?? 'Popular Destinations'); ?>" 
                                       required>
                                <small>The main title of the destinations section</small>
                            </div>

                            <div class="form-group">
                                <label for="destinations_description">Section Description *</label>
                                <textarea id="destinations_description" name="destinations_description" rows="3" required><?php echo htmlspecialchars($homepage_data['destinations']['description'] ?? 'Discover the most breathtaking locations in Kashmir and Ladakh that will leave you with unforgettable memories.'); ?></textarea>
                                <small>Description that appears below the title</small>
                            </div>

                            <div class="form-group">
                                <p><strong>Note:</strong> Destination cards are managed separately in the Destinations section. Only 3 latest destinations will appear on homepage.</p>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="update_destinations" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Destinations Section
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Packages Section Tab -->
            <div id="packages-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Packages Section</h3>
                        <p>Configure the packages section that appears on homepage</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="packages_title">Section Title *</label>
                                <input type="text" id="packages_title" name="packages_title" 
                                       value="<?php echo htmlspecialchars($homepage_data['packages']['title'] ?? 'Popular Packages'); ?>" 
                                       required>
                                <small>The title of the packages section</small>
                            </div>

                            <div class="form-group">
                                <label for="packages_description">Section Description *</label>
                                <textarea id="packages_description" name="packages_description" rows="3" required><?php echo htmlspecialchars($homepage_data['packages']['description'] ?? 'Carefully crafted itineraries for unforgettable experiences in Kashmir and Ladakh'); ?></textarea>
                                <small>Description that appears below the title</small>
                            </div>

                            <div class="form-group">
                                <p><strong>Note:</strong> Package cards are managed separately in the Packages section. Featured packages will appear here.</p>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="update_packages" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Packages Section
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Gallery Section Tab -->
            <div id="gallery-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3>Gallery Management</h3>
                                <p>Manage images displayed in the homepage gallery section</p>
                            </div>
                            <button class="btn btn-primary" onclick="openAddGalleryModal()">
                                <i class="ri-add-line"></i> Add Image
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Gallery Configuration -->
                        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                            <h4 style="margin-bottom: 15px;">Gallery Section Configuration</h4>
                            <form method="POST">
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label for="gallery_title">Section Title *</label>
                                        <input type="text" id="gallery_title" name="gallery_title" 
                                               value="<?php echo htmlspecialchars($homepage_data['gallery']['title'] ?? 'Photo Gallery'); ?>" 
                                               required>
                                    </div>
                                    <div class="form-group" style="flex: 2;">
                                        <label for="gallery_description">Section Description *</label>
                                        <input type="text" id="gallery_description" name="gallery_description" 
                                               value="<?php echo htmlspecialchars($homepage_data['gallery']['description'] ?? 'Carefully crafted itineraries for unforgettable experiences in Kashmir and Ladakh'); ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="update_gallery" class="btn btn-primary">
                                        <i class="ri-save-line"></i> Save Gallery Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Gallery Images Grid -->
                        <h4 style="margin-bottom: 20px;">Gallery Images (<?php echo $gallery_images->num_rows; ?>)</h4>
                        <?php if ($gallery_images->num_rows > 0): ?>
                            <div class="image-preview-grid">
                                <?php while ($image = $gallery_images->fetch_assoc()): ?>
                                    <div class="image-preview-item">
                                        <img src="../upload/<?php echo $image['image_path']; ?>" 
                                             alt="<?php echo htmlspecialchars($image['title']); ?>"
                                             onerror="this.src='../assets/img/bg1.jpg'">
                                        <div class="image-overlay">
                                            <div style="text-align: center; color: white;">
                                                <div style="font-weight: 600; margin-bottom: 5px;"><?php echo htmlspecialchars($image['title']); ?></div>
                                                <div style="font-size: 0.8rem; margin-bottom: 10px;"><?php echo htmlspecialchars($image['location']); ?></div>
                                                <div style="display: flex; justify-content: center; gap: 10px;">
                                                    <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px; font-size: 0.7rem;">
                                                        <?php echo $image['category']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div style="position: absolute; bottom: 10px; right: 10px;">
                                                <button class="btn btn-sm btn-danger" onclick="deleteGalleryImage(<?php echo $image['id']; ?>)">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                <i class="ri-image-line" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h3>No gallery images found</h3>
                                <p>Click "Add Image" to add your first gallery image.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Brands Section Tab -->
            <div id="brands-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3>Trusted Brands Management</h3>
                                <p>Manage brands/partners displayed in the homepage carousel</p>
                            </div>
                            <button class="btn btn-primary" onclick="openAddBrandModal()">
                                <i class="ri-add-line"></i> Add Brand
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Brands Configuration -->
                        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                            <h4 style="margin-bottom: 15px;">Brands Section Configuration</h4>
                            <form method="POST">
                                <div class="form-row">
                                    <div class="form-group" style="flex: 1;">
                                        <label for="brands_title">Section Title *</label>
                                        <input type="text" id="brands_title" name="brands_title" 
                                               value="<?php echo htmlspecialchars($homepage_data['brands']['title'] ?? 'Trusted By'); ?>" 
                                               required>
                                    </div>
                                    <div class="form-group" style="flex: 2;">
                                        <label for="brands_description">Section Description *</label>
                                        <input type="text" id="brands_description" name="brands_description" 
                                               value="<?php echo htmlspecialchars($homepage_data['brands']['description'] ?? 'Our partners and clients who trust us for their travel needs'); ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="update_brands" class="btn btn-primary">
                                        <i class="ri-save-line"></i> Save Brands Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Brands Grid -->
                        <h4 style="margin-bottom: 20px;">Brand Logos (<?php echo $brands->num_rows; ?>)</h4>
                        <?php if ($brands->num_rows > 0): 
                            // Reset pointer since we already fetched brands
                            $brands->data_seek(0);
                        ?>
                            <div class="image-preview-grid">
                                <?php while ($brand = $brands->fetch_assoc()): ?>
                                    <div class="image-preview-item">
                                        <img src="../upload/<?php echo $brand['logo_path']; ?>" 
                                             alt="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                                             onerror="this.src='../assets/img/bg1.jpg'"
                                             style="object-fit: contain; background: white;">
                                        <div class="image-overlay">
                                            <div style="text-align: center; color: white;">
                                                <div style="font-weight: 600; margin-bottom: 5px;"><?php echo htmlspecialchars($brand['brand_name']); ?></div>
                                                <?php if ($brand['brand_url']): ?>
                                                    <div style="font-size: 0.8rem; margin-bottom: 10px;">
                                                        <a href="<?php echo htmlspecialchars($brand['brand_url']); ?>" 
                                                           target="_blank" 
                                                           style="color: white; text-decoration: underline;">
                                                            Visit Website
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="position: absolute; bottom: 10px; right: 10px;">
                                                <button class="btn btn-sm btn-danger" onclick="deleteBrand(<?php echo $brand['id']; ?>)">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                <i class="ri-building-line" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h3>No brands found</h3>
                                <p>Click "Add Brand" to add your first brand logo.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CTA Section Tab -->
            <div id="cta-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Call-to-Action Section</h3>
                        <p>Configure the final CTA section on homepage</p>
                    </div>
                    <div class="card-body">
                        <?php
                        $cta_meta = !empty($homepage_data['cta']['meta_data']) ? 
                                    json_decode($homepage_data['cta']['meta_data'], true) : 
                                    [
                                        'primary_text' => 'Get in Touch',
                                        'primary_link' => '/public/contact.html',
                                        'secondary_text' => 'View Packages',
                                        'secondary_link' => '/public/packages.html'
                                    ];
                        ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="cta_title">CTA Title *</label>
                                <input type="text" id="cta_title" name="cta_title" 
                                       value="<?php echo htmlspecialchars($homepage_data['cta']['title'] ?? 'Ready to Explore Kashmir & Ladakh?'); ?>" 
                                       required>
                                <small>The main headline of the CTA section</small>
                            </div>

                            <div class="form-group">
                                <label for="cta_description">CTA Description *</label>
                                <textarea id="cta_description" name="cta_description" rows="2" required><?php echo htmlspecialchars($homepage_data['cta']['description'] ?? 'Contact us now to plan your dream vacation with our expert guides'); ?></textarea>
                                <small>Supporting text below the title</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="cta_primary_text">Primary Button Text *</label>
                                    <input type="text" id="cta_primary_text" name="cta_primary_text" 
                                           value="<?php echo htmlspecialchars($cta_meta['primary_text'] ?? 'Get in Touch'); ?>" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <label for="cta_primary_link">Primary Button Link *</label>
                                    <input type="text" id="cta_primary_link" name="cta_primary_link" 
                                           value="<?php echo htmlspecialchars($cta_meta['primary_link'] ?? '/public/contact.html'); ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="cta_secondary_text">Secondary Button Text *</label>
                                    <input type="text" id="cta_secondary_text" name="cta_secondary_text" 
                                           value="<?php echo htmlspecialchars($cta_meta['secondary_text'] ?? 'View Packages'); ?>" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <label for="cta_secondary_link">Secondary Button Link *</label>
                                    <input type="text" id="cta_secondary_link" name="cta_secondary_link" 
                                           value="<?php echo htmlspecialchars($cta_meta['secondary_link'] ?? '/public/packages.html'); ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="update_cta" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save CTA Section
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Gallery Image Modal -->
    <div class="modal" id="gallery-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeGalleryModal()">&times;</span>
            <h2>Add Gallery Image</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_gallery_image" value="1">
                
                <div class="form-group">
                    <label for="gallery_image">Image *</label>
                    <input type="file" id="gallery_image" name="gallery_image" accept="image/*" required>
                    <small>Recommended: High-quality landscape images (min 800x600px)</small>
                </div>

                <div class="form-group">
                    <label for="image_title">Image Title *</label>
                    <input type="text" id="image_title" name="image_title" required>
                    <small>Name that will appear on hover (e.g., "Dal Lake")</small>
                </div>

                <div class="form-group">
                    <label for="image_location">Location *</label>
                    <input type="text" id="image_location" name="image_location" required>
                    <small>Location description (e.g., "Srinagar, Kashmir")</small>
                </div>

                <div class="form-group">
                    <label for="image_category">Category *</label>
                    <select id="image_category" name="image_category" required>
                        <option value="">Select Category</option>
                        <option value="kashmir">Kashmir</option>
                        <option value="ladakh">Ladakh</option>
                        <option value="mountains">Mountains</option>
                        <option value="lakes">Lakes</option>
                        <option value="culture">Culture</option>
                        <option value="adventure">Adventure</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-upload-line"></i> Upload Image
                </button>
            </form>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal" id="brand-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBrandModal()">&times;</span>
            <h2>Add Brand/Partner</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_brand" value="1">
                
                <div class="form-group">
                    <label for="brand_logo">Logo *</label>
                    <input type="file" id="brand_logo" name="brand_logo" accept="image/*" required>
                    <small>Recommended: Transparent PNG, SVG, or white background (min 200x100px)</small>
                </div>

                <div class="form-group">
                    <label for="brand_name">Brand Name *</label>
                    <input type="text" id="brand_name" name="brand_name" required>
                    <small>Name of the company/organization</small>
                </div>

                <div class="form-group">
                    <label for="brand_url">Website URL (Optional)</label>
                    <input type="url" id="brand_url" name="brand_url" placeholder="https://example.com">
                    <small>Link to the brand's website</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-upload-line"></i> Add Brand
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
            const tabIndex = ['hero', 'destinations', 'packages', 'gallery', 'brands', 'cta'].indexOf(tabName) + 1;
            document.querySelector(`.tab:nth-child(${tabIndex})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }
        
        // Gallery modal functions
        function openAddGalleryModal() {
            document.getElementById('gallery-modal').classList.add('active');
        }
        
        function closeGalleryModal() {
            document.getElementById('gallery-modal').classList.remove('active');
        }
        
        // Brand modal functions
        function openAddBrandModal() {
            document.getElementById('brand-modal').classList.add('active');
        }
        
        function closeBrandModal() {
            document.getElementById('brand-modal').classList.remove('active');
        }
        
        // Delete functions
        function deleteGalleryImage(imageId) {
            if (confirm('Are you sure you want to delete this gallery image?')) {
                window.location.href = `?delete_gallery_image=${imageId}`;
            }
        }
        
        function deleteBrand(brandId) {
            if (confirm('Are you sure you want to delete this brand?')) {
                window.location.href = `?delete_brand=${brandId}`;
            }
        }
        
        // Preview homepage
        function previewHomepage() {
            window.open('../index.php', '_blank');
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
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview for hero background
            const heroImageInput = document.getElementById('hero_image');
            if (heroImageInput) {
                heroImageInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // You could add a preview here if needed
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            }
        });
    </script>
</body>
</html>