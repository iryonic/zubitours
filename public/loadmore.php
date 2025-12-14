<?php
require_once '../admin/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM gallery WHERE is_active = 1 
        ORDER BY display_order ASC, created_at DESC 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

$images = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
}

foreach ($images as $item): 
    $itemCategories = explode(' ', $item['categories']);
    $categoryClass = implode(' ', $itemCategories);
?>
<div class="masonry-item" data-category="<?php echo $categoryClass; ?>" data-id="<?php echo $item['id']; ?>">
    <a href="../<?php echo htmlspecialchars($item['image_path']); ?>" 
       data-fancybox="gallery" 
       data-caption="<strong><?php echo htmlspecialchars($item['title']); ?></strong><br><?php echo htmlspecialchars($item['description']); ?>">
        <img loading="lazy" 
             src="../<?php echo htmlspecialchars($item['image_path']); ?>" 
             alt="<?php echo htmlspecialchars($item['title']); ?>"
             onerror="this.src='https://images.unsplash.com/photo-1552733407-5d5c46c3bb3b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'">
        <div class="image-overlay">
            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
            <?php if ($item['description']): ?>
                <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?><?php echo strlen($item['description']) > 100 ? '...' : ''; ?></p>
            <?php endif; ?>
        </div>
    </a>
</div>
<?php endforeach; ?>