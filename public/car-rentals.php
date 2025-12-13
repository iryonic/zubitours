<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!--=============== REMIXICONS ===============-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />

   

    <title>Zubi tours & Holiday - Car Rentals</title>
  </head>
  <body>
        <!-- Loader -->
<div id="loader">
  <div class="travel-loader">
    <span class="path"></span>
    <i class="ri-flight-takeoff-line plane"></i>
  </div>
  <h2 class="brand-name">Zubi Tours & Holiday</h2>
</div>
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
          <option value="suv">SUV</option>
          <option value="sedan">Sedan</option>
          <option value="luxury">Luxury</option>
          <option value="economy">Economy</option>
        </select>
        <select id="car-capacity">
          <option value="all">All Capacities</option>
          <option value="4">4 Seaters</option>
          <option value="6">6 Seaters</option>
          <option value="8">8+ Seaters</option>
        </select>
        <button onclick="resetCarFilters()">Reset</button>
      </div>

      <div class="car-grid" id="cars-container">
        <!-- Car 1 -->
        <div class="car-card" data-type="economy" data-capacity="7">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/innova-crysta.avif" alt="Toyota Innova">
            <span class="car-badge">Popular</span>
          </div>
          <div class="car-details">
            <h3>Toyota Innova Crysta</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 7 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Diesel</span>
            </div>
            <div class="car-price">
              <p class="price">₹2,800 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Toyota Innova Crysta" data-price="2800">Book Now</button>
          </div>
        </div>

        <!-- Car 2 -->
        <div class="car-card" data-type="suv" data-capacity="4">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/traveller.jpeg" alt="Tempo Traveller">
            <span class="car-badge">Group</span>
          </div>
          <div class="car-details">
            <h3>Tempo Traveller</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 17 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Diesel</span>
            </div>
            <div class="car-price">
              <p class="price">₹4,200<span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Tempo Traveller" data-price="4200">Book Now</button>
          </div>
        </div>

          <!-- Car 8-->
        <div class="car-card" data-type="Economy" data-capacity="8">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/volvo_bus.jpg" alt="Volvo Bus">
            <span class="car-badge">Group</span>
          </div>
          <div class="car-details">
            <h3>Volvo Bus</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 27-40 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Diesel</span>
            </div>
            <div class="car-price">
              <p class="price">₹7,500 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Volvo Bus" data-price="4000">Book Now</button>
          </div>
        </div>
        
        <!-- Car 3 -->
        <div class="car-card" data-type="luxury" data-capacity="4">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/rumian.webp" alt="Rumian">
            <span class="car-badge">Popular</span>
          </div>
          <div class="car-details">
            <h3>Toyota Rumian</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 6 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Petrol</span>
            </div>
            <div class="car-price">
              <p class="price">₹2,500 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Toyota Rumian" data-price="2500">Book Now</button>
          </div>
        </div>

        <!-- Car 4 -->
        <div class="car-card" data-type="economy" data-capacity="4">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/swift.avif" alt="Maruti Swift Dzire">
          </div>
          <div class="car-details">
            <h3>Maruti Swift Dzire</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 4 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Petrol</span>
            </div>
            <div class="car-price">
              <p class="price">₹2,200 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Maruti Swift Dzire" data-price="2200">Book Now</button>
          </div>
        </div>

        <!-- Car 5 -->
        <div class="car-card" data-type="suv" data-capacity="8">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/innova.jpeg" alt="Toyota Innova">
            <span class="car-badge">Group</span>
          </div>
          <div class="car-details">
            <h3>Toyota Innova</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 7 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Diesel</span>
            </div>
            <div class="car-price">
              <p class="price">₹2,500 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Toyota Innova" data-price="2500">Book Now</button>
          </div>
        </div>

        <!-- Car 6 -->
        <div class="car-card" data-type="suv" data-capacity="8">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/traveller14.jpeg" alt="Tempo Traveller">
            <span class="car-badge">Group</span>
          </div>
          <div class="car-details">
            <h3>Tempo Traveller</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 14 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Diesel</span>
            </div>
            <div class="car-price">
              <p class="price">₹4,000 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Tempo Traveller" data-price="4000">Book Now</button>
          </div>
        </div>


        <!-- Car 7 -->
        <div class="car-card" data-type="suv" data-capacity="8">
          <div class="car-image">
            <img loading="lazy" src="../assets/img/urbania.webp" alt="Urbania">
            <span class="car-badge">Group</span>
          </div>
          <div class="car-details">
            <h3>Urbania</h3>
            <div class="car-specs">
              <span><i class="ri-user-line"></i> 14 Seater</span>
              <span><i class="ri-settings-3-line"></i> Manual</span>
              <span><i class="ri-gas-station-line"></i> Diesel</span>
            </div>
            <div class="car-price">
              <p class="price">₹5,500 <span>/day</span></p>
              <p class="includes">Incl. Driver & Fuel</p>
            </div>
            <button class="book-btn" data-car="Urbania" data-price="5500">Book Now</button>
          </div>
        </div>

      
      </div>
    </section>

    <!-- Booking Modal -->
    <div class="modal" id="booking-modal">
      <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Book Your Car</h2>
        <form id="booking-form">
          <input type="hidden" id="selected-car">
          <input type="hidden" id="car-price">
          
          <div class="form-group">
            <label for="pickup-location">Pickup Location</label>
            <input type="text" id="pickup-location" required placeholder="Enter pickup location">
          </div>
          
       
          
          <div class="form-row">
            <div class="form-group">
              <label for="pickup-date">Pickup Date</label>
              <input type="date" id="pickup-date" required>
            </div>
            
            <div class="form-group">
              <label for="drop-date">Return Date</label>
              <input type="date" id="drop-date" required>
            </div>
          </div>
          
         
           
          <div class="form-group">
            <label for="customer-driving-license">Your Driving License</label>
            <input type="text" id="customer-driving-license" required placeholder="Enter your driving license number">
          </div>
        
          
          <div class="form-group">
            <label for="customer-name">Your Name</label>
            <input type="text" id="customer-name" required placeholder="Enter your full name">
          </div>
          
          <div class="form-group">
            <label for="customer-email">Email</label>
            <input type="email" id="customer-email" required placeholder="Enter your email">
          </div>
          
          <div class="form-group">
            <label for="customer-phone">Phone Number</label>
            <input type="tel" id="customer-phone" required placeholder="Enter your phone number">
          </div>
          
          <button type="submit" class="submit-booking">Confirm Booking</button>
        </form>
      </div>
    </div>



       <!-- FOOTER -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3>Zubi Tours & Holidays</h3>
      <p>Creating unforgettable experiences in the paradise of Kashmir and the majestic landscapes of Ladakh.</p>
      <div class="social-links">
        <a href="./package-details.html"><i class="ri-facebook-fill"></i></a>
        <a href="./package-details.html"><i class="ri-instagram-line"></i></a>
        <a href="./package-details.html"><i class="ri-twitter-fill"></i></a>
        <a href="./package-details.html"><i class="ri-youtube-fill"></i></a>
      </div>
    </div>
    
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="../index.html">Home</a></li>
        <li><a href="/public/about.html">About Us</a></li>
        <li><a href="/public/destinations.html">Destinations</a></li>
        <li><a href="/public/packages.html">Packages</a></li>
        <li><a href="/public/gallery.html">Gallery</a></li>
      </ul>
    </div>
    
    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="/public/packages.html">Tour Packages</a></li>
        <li><a href="/public/car-rentals.html">Car Rentals</a></li>
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
    <p> Powered By <a href="https://irfanmanzoor.in">EXORA</a></p>
  </div>
</footer>



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
        cars.forEach(car => {
          const type = car.getAttribute("data-type");
          const capacity = car.getAttribute("data-capacity");

          const match = 
            (carFilters.type === "all" || carFilters.type === type) &&
            (carFilters.capacity === "all" || carFilters.capacity === capacity);

          car.style.display = match ? "flex" : "none";
        });
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
      const closeModal = document.querySelector(".close-modal");
      const pickupDate = document.getElementById("pickup-date");
      const dropDate = document.getElementById("drop-date");
      const rentalDays = document.getElementById("rental-days");
      const totalAmount = document.getElementById("total-amount");
      const bookingForm = document.getElementById("booking-form");

      // Set minimum date as today
      const today = new Date().toISOString().split('T')[0];
      pickupDate.min = today;
      dropDate.min = today;

      // Open modal when book button is clicked
      bookButtons.forEach(button => {
        button.addEventListener('click', function() {
          const carName = this.getAttribute('data-car');
          const pricePerDay = this.getAttribute('data-price');
          
          document.getElementById('selected-car').value = carName;
          document.getElementById('car-price').value = pricePerDay;
          
          
          modal.style.display = "block";
        });
      });

      // Close modal
      closeModal.addEventListener('click', function() {
        modal.style.display = "none";
      });

      // Close modal if clicked outside
      window.addEventListener('click', function(event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      });


    

      // Form submission
      bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Here you would typically send the data to your server
        alert('Booking request submitted successfully! We will contact you shortly to confirm.');
        modal.style.display = "none";
        bookingForm.reset();
      });
    </script>
  </body>
</html>