<?php
require_once 'c:/xampp/htdocs/zubitours/admin/includes/connection.php';

$query = "CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(255) NOT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `avatar_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($query)) {
    echo "Table 'testimonials' checked/created successfully.<br>";
    
    // Check if empty
    $res = $conn->query("SELECT COUNT(*) as count FROM testimonials");
    $count = $res->fetch_assoc()['count'];
    
    if ($count == 0) {
        $sample_data = "INSERT INTO `testimonials` (author_name, author_email, package_name, testimonial_text, rating, is_active) VALUES 
        ('Priya Sharma', 'priya@example.com', 'Kashmir Magic Tour', 'Zubi Tours made our Kashmir trip absolutely magical. Their attention to detail and knowledgeable guides made all the difference.', 5, 1),
        ('Sarah Johnson', 'sarah@example.com', 'Leh Ladakh Adventure', 'As a solo female traveler, I felt completely safe and well taken care of throughout my journey. The team at Zubi Tours became like family to me.', 5, 1),
        ('Raj Patel', 'raj@example.com', 'Srinagar Getaway', 'The customized package they created for our anniversary trip was perfect. Every accommodation, meal, and activity exceeded our expectations.', 5, 1)";
        
        if ($conn->query($sample_data)) {
            echo "Sample testimonials added.<br>";
        }
    }
} else {
    echo "Error: " . $conn->error;
}
?>
