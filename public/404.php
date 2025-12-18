<?php
// Send proper 404 header
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="robots" content="noindex, nofollow">
	<title>404 Not Found | Zubi Tours & Holidays</title>
	<link rel="icon" type="image/png" href="../assets/img/zubilogo.jpg" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
	<link rel="stylesheet" href="../assets/css/styles.css" />
	<style>
		.error-hero {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			text-align: center;
			min-height: 50vh;
			padding: 80px 20px;
			background: linear-gradient(180deg, rgba(15,23,42,0.03), transparent);
		}
		.error-hero h1 { font-size: clamp(2rem, 6vw, 4rem); margin: 10px 0; }
		.error-hero p { color: var(--text-secondary); margin-bottom: 18px; }
		.error-actions { display:flex; gap:12px; flex-wrap:wrap; justify-content:center; margin-top: 12px; }
		.error-card { max-width: 900px; margin: 30px auto; padding: 24px; border-radius: 12px; background: var(--bg-secondary); box-shadow: var(--card-shadow); }
		.search-inline { display:flex; gap:8px; max-width:560px; margin:0 auto; }
		.search-inline input { flex:1; padding:10px 12px; border-radius:8px; border:1px solid var(--border-color); }
	</style>
</head>
<body>

	<!-- Header / Navbar -->
	<?php include '../admin/includes/navbar.php'; ?>

	<section class="error-hero">
		<div class="section-header">
			<h1>404 — Page Not Found</h1>
			<p>We couldn't find the page you were looking for. It might have been moved, renamed, or never existed.</p>
		</div>

		<div class="error-card">
			<div style="margin-bottom:12px; text-align:center;">
				<form class="search-inline" action="./destinations.php" method="get" onsubmit="if(!this.q.value.trim()){event.preventDefault(); window.location='../index.php';}">
					<input type="search" name="q" placeholder="Search destinations, e.g., Gulmarg, Srinagar" aria-label="Search destinations">
					<button type="submit" class="btn btn-primary">Search</button>
				</form>
			</div>

			<div style="text-align:center;">
				<div class="error-actions">
					<a href="../index.php" class="btn btn-secondary"><i class="ri-home-3-line"></i> Back to Home</a>
					<a href="./destinations.php" class="btn btn-primary"><i class="ri-map-pin-line"></i> Browse Destinations</a>
					<a href="./packages.php" class="btn btn-outline"><i class="ri-briefcase-line"></i> View Packages</a>
					<a href="./contact.php" class="btn btn-warning"><i class="ri-mail-line"></i> Contact Support</a>
				</div>
			</div>
		</div>

		<p style="margin-top:18px; font-size:0.95rem; color:var(--text-secondary);">If you believe this is an error, <a href="./contact.php">let us know</a> and we'll help you find what you're looking for.</p>
	</section>

	<!-- Footer (copied from theme) -->
	<footer class="footer">
		<div class="footer-container">
			<div class="footer-col">
				<h3>Zubi Tours & Holidays</h3>
				<p>Creating unforgettable experiences in the paradise of Kashmir and the majestic landscapes of Ladakh.</p>
				<div class="social-links">
					<a href="#"><i class="ri-facebook-fill"></i></a>
					<a href="#"><i class="ri-instagram-line"></i></a>
					<a href="#"><i class="ri-twitter-fill"></i></a>
					<a href="#"><i class="ri-youtube-fill"></i></a>
				</div>
			</div>

			<div class="footer-col">
				<h4>Quick Links</h4>
				<ul>
					<li><a href="../index.php">Home</a></li>
					<li><a href="./about.php">About Us</a></li>
					<li><a href="./destinations.php">Destinations</a></li>
					<li><a href="./packages.php">Packages</a></li>
					<li><a href="./gallery.php">Gallery</a></li>
				</ul>
			</div>

			<div class="footer-col">
				<h4>Services</h4>
				<ul>
					<li><a href="./packages.php">Tour Packages</a></li>
					<li><a href="./car-rentals.php">Car Rentals</a></li>
					<li><a href="#">Hotel Booking</a></li>
					<li><a href="#">Adventure Activities</a></li>
					<li><a href="#">Pilgrimage Tours</a></li>
				</ul>
			</div>

			<div class="footer-col">
				<h4>Contact Info</h4>
				<div class="contact-info">
					<p><i class="ri-map-pin-line"></i> Srinagar, Jammu & Kashmir</p>
					<p><i class="ri-phone-line"></i> +91 7006296814</p>
					<p><i class="ri-mail-line"></i> info@zubitours.com</p>
					<p><i class="ri-time-line"></i> Mon-Sat: 9AM - 6PM</p>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<p>&copy; <span id="getYear"></span> Zubi Tours & Holidays. All rights reserved.</p>
			<p> Powered By <a href="https://irfanmanzoor.in">KRYON</a></p>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<script src="../assets/js/main.js"></script>
	<script>document.getElementById('getYear') && (document.getElementById('getYear').innerText = new Date().getFullYear());</script>
</body>
</html>

