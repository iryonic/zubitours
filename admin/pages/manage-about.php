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

// Handle General Content Updates
if (isset($_POST['update_general'])) {
    foreach ($_POST['sections'] as $key => $data) {
        $title = $data['title'] ?? '';
        $subtitle = $data['subtitle'] ?? '';
        $content = $data['content'] ?? '';
        
        $stmt = $conn->prepare("UPDATE about_page SET title = ?, subtitle = ?, content = ? WHERE section_key = ?");
        $stmt->bind_param("ssss", $title, $subtitle, $content, $key);
        $stmt->execute();
    }
    
    // Handle Story Image
    if (isset($_FILES['story_image']) && $_FILES['story_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/';
        $file_name = uniqid() . '_' . basename($_FILES['story_image']['name']);
        if (move_uploaded_file($_FILES['story_image']['tmp_name'], $upload_dir . $file_name)) {
            $conn->query("UPDATE about_page SET image_path = '$file_name' WHERE section_key = 'story'");
        }
    }
    
    $_SESSION['message'] = "General content updated successfully!";
    $_SESSION['message_type'] = "success";
    header("Location: manage-about.php");
    exit();
}

// Handle Values (Add/Update/Delete)
if (isset($_POST['save_value'])) {
    $id = $_POST['value_id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];
    $display_order = $_POST['display_order'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE about_values SET title = ?, description = ?, icon = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("sssi i", $title, $description, $icon, $display_order, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO about_values (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $icon, $display_order);
    }
    
    if ($stmt->execute()) {
        $_SESSION['message'] = $id ? "Value updated!" : "Value added!";
        $_SESSION['message_type'] = "success";
    }
    header("Location: manage-about.php?tab=values");
    exit();
}

if (isset($_GET['delete_value'])) {
    $id = intval($_GET['delete_value']);
    $conn->query("DELETE FROM about_values WHERE id = $id");
    $_SESSION['message'] = "Value deleted!";
    $_SESSION['message_type'] = "success";
    header("Location: manage-about.php?tab=values");
    exit();
}

// Handle Team (Add/Update/Delete)
if (isset($_POST['save_member'])) {
    $id = $_POST['member_id'] ?? null;
    $name = $_POST['name'];
    $role = $_POST['role'];
    $description = $_POST['description'];
    $display_order = $_POST['display_order'];
    $image_path = $_POST['current_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/';
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name)) {
            $image_path = $file_name;
        }
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE about_team SET name = ?, role = ?, description = ?, image_path = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $name, $role, $description, $image_path, $display_order, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO about_team (name, role, description, image_path, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $role, $description, $image_path, $display_order);
    }
    
    if ($stmt->execute()) {
        $_SESSION['message'] = $id ? "Member updated!" : "Member added!";
        $_SESSION['message_type'] = "success";
    }
    header("Location: manage-about.php?tab=team");
    exit();
}

if (isset($_GET['delete_member'])) {
    $id = intval($_GET['delete_member']);
    $conn->query("DELETE FROM about_team WHERE id = $id");
    $_SESSION['message'] = "Member deleted!";
    $_SESSION['message_type'] = "success";
    header("Location: manage-about.php?tab=team");
    exit();
}

// Handle Stats
if (isset($_POST['update_stats'])) {
    foreach ($_POST['stats'] as $id => $data) {
        $label = $data['label'];
        $value = intval($data['value']);
        $stmt = $conn->prepare("UPDATE about_stats SET label = ?, value = ? WHERE id = ?");
        $stmt->bind_param("sii", $label, $value, $id);
        $stmt->execute();
    }
    $_SESSION['message'] = "Statistics updated!";
    $_SESSION['message_type'] = "success";
    header("Location: manage-about.php?tab=stats");
    exit();
}

// Fetch Data
$about_sections = [];
$res = $conn->query("SELECT * FROM about_page");
while($row = $res->fetch_assoc()) $about_sections[$row['section_key']] = $row;

$values = $conn->query("SELECT * FROM about_values ORDER BY display_order");
$team = $conn->query("SELECT * FROM about_team ORDER BY display_order");
$stats = $conn->query("SELECT * FROM about_stats ORDER BY display_order");

$current_tab = $_GET['tab'] ?? 'general';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Page - Zubi Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); }
        .tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .tab.active { color: var(--primary-color); border-bottom-color: var(--primary-color); font-weight: 600; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        .image-preview { width: 150px; height: 100px; object-fit: cover; border-radius: 8px; margin-top: 10px; border: 1px solid var(--border-color); }
        .icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; }
        .btn-sm { padding: 5px 10px; font-size: 0.85rem; }
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

            <h1 class="section-title">Manage About Us Page</h1>

            <div class="tabs">
                <div class="tab <?php echo $current_tab == 'general' ? 'active' : ''; ?>" onclick="location.href='?tab=general'">General Content</div>
                <div class="tab <?php echo $current_tab == 'values' ? 'active' : ''; ?>" onclick="location.href='?tab=values'">Our Values</div>
                <div class="tab <?php echo $current_tab == 'team' ? 'active' : ''; ?>" onclick="location.href='?tab=team'">Meet Our Team</div>
                <div class="tab <?php echo $current_tab == 'stats' ? 'active' : ''; ?>" onclick="location.href='?tab=stats'">Statistics</div>
            </div>

            <!-- General Content Tab -->
            <div id="general" class="tab-content <?php echo $current_tab == 'general' ? 'active' : ''; ?>">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_general" value="1">
                    
                    <div class="card">
                        <h3>Hero Section</h3>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="sections[hero][title]" value="<?php echo htmlspecialchars($about_sections['hero']['title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Subtitle</label>
                            <textarea name="sections[hero][subtitle]" rows="2"><?php echo htmlspecialchars($about_sections['hero']['subtitle']); ?></textarea>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Our Story Section</h3>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="sections[story][title]" value="<?php echo htmlspecialchars($about_sections['story']['title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="sections[story][content]" rows="6"><?php echo htmlspecialchars($about_sections['story']['content']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Story Image</label>
                            <input type="file" name="story_image" accept="image/*">
                            <?php if ($about_sections['story']['image_path']): 
                                $story_img = $about_sections['story']['image_path'];
                                $story_src = file_exists('../upload/' . $story_img) ? '../upload/' . $story_img : '../../assets/img/' . $story_img;
                            ?>
                                <img src="<?php echo $story_src; ?>" class="image-preview" alt="Story">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Call to Action (CTA) Section</h3>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="sections[cta][title]" value="<?php echo htmlspecialchars($about_sections['cta']['title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Subtitle</label>
                            <textarea name="sections[cta][subtitle]" rows="2"><?php echo htmlspecialchars($about_sections['cta']['subtitle']); ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Save Changes</button>
                </form>
            </div>

            <!-- Values Tab -->
            <div id="values" class="tab-content <?php echo $current_tab == 'values' ? 'active' : ''; ?>">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Core Values</h3>
                    <button class="btn btn-primary btn-sm" onclick="openValueModal()"><i class="ri-add-line"></i> Add Value</button>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($v = $values->fetch_assoc()): ?>
                            <tr>
                                <td><i class="<?php echo $v['icon']; ?>" style="font-size: 1.5rem;"></i></td>
                                <td><strong><?php echo htmlspecialchars($v['title']); ?></strong></td>
                                <td style="max-width: 300px;"><?php echo htmlspecialchars(substr($v['description'], 0, 100)) . '...'; ?></td>
                                <td><?php echo $v['display_order']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='editValue(<?php echo json_encode($v); ?>)'><i class="ri-edit-line"></i></button>
                                    <a href="?delete_value=<?php echo $v['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this value?')"><i class="ri-delete-bin-line"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Team Tab -->
            <div id="team" class="tab-content <?php echo $current_tab == 'team' ? 'active' : ''; ?>">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Team Members</h3>
                    <button class="btn btn-primary btn-sm" onclick="openMemberModal()"><i class="ri-add-line"></i> Add Member</button>
                </div>
                <div class="icon-grid">
                    <?php while($m = $team->fetch_assoc()): ?>
                    <div class="card" style="text-align: center;">
                        <?php 
                            $m_img = $m['image_path'] ?: 'bg1.jpg';
                            $m_src = file_exists('../upload/' . $m_img) ? '../upload/' . $m_img : '../../assets/img/' . $m_img;
                        ?>
                        <img src="<?php echo $m_src; ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;" alt="">
                        <h4><?php echo htmlspecialchars($m['name']); ?></h4>
                        <p style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($m['role']); ?></p>
                        <div style="margin-top: 15px; display: flex; justify-content: center; gap: 10px;">
                            <button class="btn btn-sm btn-primary" onclick='editMember(<?php echo json_encode($m); ?>)'><i class="ri-edit-line"></i></button>
                            <a href="?delete_member=<?php echo $m['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this member?')"><i class="ri-delete-bin-line"></i></a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Stats Tab -->
            <div id="stats" class="tab-content <?php echo $current_tab == 'stats' ? 'active' : ''; ?>">
                <form method="POST">
                    <input type="hidden" name="update_stats" value="1">
                    <div class="stats-grid">
                        <?php while($s = $stats->fetch_assoc()): ?>
                        <div class="card">
                            <div class="form-group">
                                <label>Label</label>
                                <input type="text" name="stats[<?php echo $s['id']; ?>][label]" value="<?php echo htmlspecialchars($s['label']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Value (Numeric)</label>
                                <input type="number" name="stats[<?php echo $s['id']; ?>][value]" value="<?php echo $s['value']; ?>">
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Update Stats</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Value Modal -->
    <div id="valueModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('valueModal')">&times;</span>
            <h2 id="valueModalTitle">Add Value</h2>
            <form method="POST">
                <input type="hidden" name="save_value" value="1">
                <input type="hidden" name="value_id" id="value_id">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="value_title" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="value_description" rows="3" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Icon (RemixIcon Class)</label>
                        <input type="text" name="icon" id="value_icon" placeholder="ri-heart-line" required>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="value_order" value="1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Value</button>
            </form>
        </div>
    </div>

    <!-- Team Modal -->
    <div id="memberModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('memberModal')">&times;</span>
            <h2 id="memberModalTitle">Add Team Member</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="save_member" value="1">
                <input type="hidden" name="member_id" id="member_id">
                <input type="hidden" name="current_image" id="current_image">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="member_name" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" name="role" id="member_role" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="member_description" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Member Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="member_order" value="1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Member</button>
            </form>
        </div>
    </div>

    <script>
        function openValueModal() {
            document.getElementById('valueModalTitle').innerText = 'Add Value';
            document.getElementById('value_id').value = '';
            document.getElementById('value_title').value = '';
            document.getElementById('value_description').value = '';
            document.getElementById('value_icon').value = '';
            document.getElementById('value_order').value = '1';
            document.getElementById('valueModal').classList.add('active');
        }

        function editValue(data) {
            document.getElementById('valueModalTitle').innerText = 'Edit Value';
            document.getElementById('value_id').value = data.id;
            document.getElementById('value_title').value = data.title;
            document.getElementById('value_description').value = data.description;
            document.getElementById('value_icon').value = data.icon;
            document.getElementById('value_order').value = data.display_order;
            document.getElementById('valueModal').classList.add('active');
        }

        function openMemberModal() {
            document.getElementById('memberModalTitle').innerText = 'Add Team Member';
            document.getElementById('member_id').value = '';
            document.getElementById('member_name').value = '';
            document.getElementById('member_role').value = '';
            document.getElementById('member_description').value = '';
            document.getElementById('member_order').value = '1';
            document.getElementById('current_image').value = '';
            document.getElementById('memberModal').classList.add('active');
        }

        function editMember(data) {
            document.getElementById('memberModalTitle').innerText = 'Edit Team Member';
            document.getElementById('member_id').value = data.id;
            document.getElementById('member_name').value = data.name;
            document.getElementById('member_role').value = data.role;
            document.getElementById('member_description').value = data.description;
            document.getElementById('member_order').value = data.display_order;
            document.getElementById('current_image').value = data.image_path;
            document.getElementById('memberModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }
    </script>
</body>
</html>
