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

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="../assets/css/styles.css" />c

    <title>Kashmir Valley Explorer - Zubi Tours</title>
    <style>
      /* Package Detail Styles */
      .package-detail-hero {
        position: relative;
        height: 80vh;
        min-height: 600px;
        max-height: 900px;
        display: flex;
        align-items: flex-end;
        color: white;
        overflow: hidden;
        padding: 0 20px 80px;
      }
      
      .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.3) 100%), url("");
        background-size: cover;
        background-position: center;
        z-index: -2;
      }
      
      .hero-content {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        z-index: 1;
      }
      
      .package-badge {
        display: inline-block;
        background: var(--first-color);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 20px;
      }
      
      .hero-content h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        font-weight: 800;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        line-height: 1.2;
        max-width: 800px;
      }
      
      .hero-content p {
        font-size: 1.4rem;
        margin-bottom: 30px;
        max-width: 700px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        line-height: 1.6;
      }
      
      .package-meta {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
      }
      
      .meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      
      .meta-item i {
        font-size: 1.5rem;
        color: var(--first-color);
      }
      
      .meta-item span {
        font-weight: 500;
        font-size: 1.1rem;
      }
      
      /* Package Detail Content */
      .package-detail-container {
        max-width: 1400px;
        margin: 0px auto;
        padding: 0 40px;
        display: flex;
        flex-direction: column;
        gap: 60px;
      }
      
      /* Overview Section */
      .overview-section {
        margin-bottom: 60px;
      }
      
      .section-title {
        font-size: 2.2rem;
        color: var(--title-color);
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
        font-weight: 700;
      }
      
      .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 5px;
        background: var(--first-color);
        border-radius: 3px;
      }
      
      .overview-content p {
        line-height: 1.8;
        margin-bottom: 25px;
        color: var(--text-color);
        font-size: 1.1rem;
      }
      
      .highlights-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin: 35px 0;
      }
      
      .highlight-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .highlight-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      }
      
      .highlight-icon {
        width: 60px;
        height: 60px;
        background: rgba(115, 155, 249, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--first-color);
        font-size: 1.5rem;
        flex-shrink: 0;
      }
      
      .highlight-item h4 {
        font-size: 1.2rem;
        margin-bottom: 5px;
        color: var(--title-color);
      }
      
      .highlight-item p {
        margin: 0;
        font-size: 1rem;
        color: var(--text-color);
      }
      
      /* Itinerary Section */
      .itinerary-section {
        margin-bottom: 60px;
      }
      
      .itinerary-tabs {
    display: grid;
     grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
        gap: 10px;
        margin-bottom: 35px;
        border-bottom: 2px solid #e2e8f0;
        flex-wrap: wrap;
      }
      
      .itinerary-tab {
        padding: 15px 30px;
        background: #f8fafc;
        border: none;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
      }
      
      .itinerary-tab:hover {
        background: #e2e8f0;
      }
      
      .itinerary-tab.active {
        background: var(--first-color);
        color: white;
      }
      
      .itinerary-content {
        display: none;
      }
      
      .itinerary-content.active {
        display: block;
      }
      
      .day-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-left: 5px solid var(--first-color);
      }
      
      .day-title {
        font-size: 1.5rem;
        color: var(--title-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
      }
      
      .day-title i {
        color: var(--first-color);
        font-size: 1.8rem;
      }
      
      .activities-list {
        list-style: none;
      }
      
      .activity-item {
        display: flex;
        gap: 20px;
        padding: 20px 0;
        border-bottom: 1px solid #f1f1f1;
        align-items: flex-start;
      }
      
      .activity-item:last-child {
        border-bottom: none;
      }
      
      .activity-time {
        min-width: 120px;
        font-weight: 700;
        color: var(--first-color);
        font-size: 1.1rem;
        padding: 8px 15px;
        background: rgba(115, 155, 249, 0.1);
        border-radius: 8px;
        text-align: center;
      }
      
      .activity-details h4 {
        margin-bottom: 10px;
        color: var(--title-color);
        font-size: 1.2rem;
      }
      
      .activity-details p {
        color: var(--text-color);
        margin: 0;
        line-height: 1.7;
        font-size: 1.1rem;
      }
      
      /* Inclusions Section */
      .inclusions-section {
        margin-bottom: 60px;
      }
      
      .inclusions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
      }
      
      .inclusion-category {
        margin-bottom: 35px;
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      }
      
      .inclusion-category h4 {
        font-size: 1.4rem;
        color: var(--title-color);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f1f1;
      }
      
      .inclusion-list {
        list-style: none;
      }
      
      .inclusion-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }
      
      .inclusion-item i {
        color: #22c55e;
        font-size: 1.3rem;
      }
      
      .exclusion-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }
      
      .exclusion-item i {
        color: #ef4444;
        font-size: 1.3rem;
      }
      
      /* Booking Widget */
      .booking-widget {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        position: sticky;
        top: 120px;
        height: fit-content;
      }
      
      .price-section {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f1f1f1;
      }
      
      .price-amount {
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--first-color);
        line-height: 1;
        margin-bottom: 10px;
      }
      
      .price-note {
        color: var(--text-color-light);
        font-size: 1.1rem;
        margin-bottom: 20px;
      }
      
      .discount-badge {
        display: inline-block;
        background: #ffedd5;
        color: #ea580c;
        padding: 8px 18px;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 700;
      }
      
      .booking-form .form-group {
        margin-bottom: 25px;
      }
      
      .booking-form label {
        display: block;
        margin-bottom: 12px;
        font-weight: 600;
        color: var(--title-color);
        font-size: 1.1rem;
      }
      
      .booking-form input,
      .booking-form select {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
      }
      
      .booking-form input:focus,
      .booking-form select:focus {
        border-color: var(--first-color);
        outline: none;
        box-shadow: 0 0 0 4px rgba(42, 61, 232, 0.1);
      }
      
      .form-row {
        display: flex;
        gap: 20px;
      }
      
      .form-row .form-group {
        flex: 1;
      }
      
      .guest-counter {
        display: flex;
        align-items: center;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
      }
      
      .counter-btn {
        width: 50px;
        height: 50px;
        background: #f8fafc;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      
      .counter-btn:hover {
        background: #e2e8f0;
      }
      
      .guest-counter input {
        width: 60px;
        border: none;
        text-align: center;
        padding: 0;
        font-weight: 700;
        font-size: 1.2rem;
      }
      
      .booking-summary {
        background: #f8fafc;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
      }
      
      .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }
      
      .summary-item:last-child {
        margin-bottom: 0;
        padding-top: 15px;
        border-top: 2px solid #e2e8f0;
        font-weight: 800;
        font-size: 1.3rem;
        color: var(--title-color);
      }
      
      .book-now-btn {
        width: 100%;
        padding: 20px;
        background: var(--first-color);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.2rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(42, 61, 232, 0.3);
      }
      
     
      
      /* Gallery Section */
      .gallery-section {
        margin-bottom: 60px;
      }
      
      .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
      }
      
      .gallery-item {
        border-radius: 15px;
        overflow: hidden;
        height: 250px;
        position: relative;
        cursor: pointer;
      }
      
      .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
      }
      
      .gallery-item:hover img {
        transform: scale(1.08);
      }
      
      .gallery-item:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      
      .gallery-item:hover:after {
        opacity: 1;
      }
      
      /* FAQ Section */
      .faq-section {
        margin-bottom: 60px;
      }
      
      .faq-item {
        background: white;
        border-radius: 15px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
      }
      
      .faq-item:hover {
        transform: translateY(-3px);
      }
      
      .faq-question {
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 700;
        color: var(--title-color);
        font-size: 1.2rem;
      }
      
      .faq-answer {
        padding: 0 25px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.4s ease;
        color: var(--text-color);
        line-height: 1.7;
        font-size: 1.1rem;
      }
      
      .faq-item.active .faq-answer {
        padding: 0 25px 25px;
        max-height: 500px;
      }
      
      .faq-item.active .faq-question i {
        transform: rotate(180deg);
      }
      
      /* Responsive Adjustments */
      @media (max-width: 1440px) {
        .package-detail-container {
          max-width: 1200px;
          grid-template-columns: 1fr 400px;
          gap: 50px;
        }
        
        .hero-content h1 {
          font-size: 3rem;
        }
      }
      
      @media (max-width: 1200px) {
        .package-detail-container {
          grid-template-columns: 1fr 380px;
          gap: 40px;
          padding: 0 30px;
        }
        
        .hero-content h1 {
          font-size: 2.8rem;
        }
        
        .hero-content p {
          font-size: 1.2rem;
        }
        
        .section-title {
          font-size: 2rem;
        }
      }
      
      @media (max-width: 1024px) {
        .package-detail-container {
          grid-template-columns: 1fr;
          gap: 40px;
        }
        
        .booking-widget {
          position: static;
          max-width: 600px;
          margin: 0 auto;
        }
        
        .gallery-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        
        .package-detail-hero {
          height: 70vh;
          padding: 0 20px 60px;
        }
      }
      
      @media (max-width: 768px) {
        .package-detail-hero {
          height: 60vh;
          padding: 0 15px 40px;
        }
        
        .hero-content h1 {
          font-size: 2.2rem;
        }
        
        .hero-content p {
          font-size: 1.1rem;
        }
        
        .package-meta {
          flex-direction: column;
          gap: 15px;
        }
        
        .highlights-grid,
        .inclusions-grid {
          grid-template-columns: 1fr;
        }
        
        .itinerary-tabs {
          flex-wrap: wrap;
        }
        
        .form-row {
          flex-direction: column;
          gap: 0;
        }
        
        .gallery-grid {
          grid-template-columns: 1fr;
        }
        
        .package-detail-container {
          padding: 0 20px;
          margin: 40px auto;
        }
      }
      
      @media (max-width: 480px) {
        .package-detail-hero {
          height: 50vh;
          min-height: 500px;
        }
        
        .hero-content h1 {
          font-size: 1.8rem;
        }
        
        .hero-content p {
          font-size: 1rem;
        }
        
        .section-title {
          font-size: 1.7rem;
        }
        
        .price-amount {
          font-size: 2.2rem;
        }
        
        .activity-item {
          flex-direction: column;
          gap: 10px;
        }
        
        .activity-time {
          min-width: auto;
          align-self: flex-start;
        }
      }
      
      /* Large screen optimizations */
      @media (min-width: 1600px) {
        .package-detail-container {
          max-width: 1500px;
          grid-template-columns: 1fr 500px;
        }
        
        .hero-content h1 {
          font-size: 4rem;
        }
        
        .hero-content p {
          font-size: 1.5rem;
        }
        
        .section-title {
          font-size: 2.5rem;
        }
        
        .overview-content p {
          font-size: 1.2rem;
        }
        
        .highlight-item {
          padding: 25px;
        }
        
        .highlight-icon {
          width: 70px;
          height: 70px;
          font-size: 1.8rem;
        }
        
        .highlight-item h4 {
          font-size: 1.4rem;
        }
        
        .highlight-item p {
          font-size: 1.1rem;
        }
      }
      
      @media (min-width: 2000px) {
        .package-detail-container {
          max-width: 1800px;
        }
        
        .hero-content {
          max-width: 1600px;
        }
      }
    </style>
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

    <!-- Package Hero Section -->
    <section class="package-detail-hero">
      <div class="hero-background"></div>
      <div class="hero-content">
        <span class="package-badge">Bestseller</span>
        <h1>Kashmir Valley Explorer</h1>
        <p>Complete Kashmir experience including Dal Lake, Gulmarg, Pahalgam, and Sonamarg with cultural immersion.</p>
        
        <div class="package-meta">
          <div class="meta-item">
            <i class="ri-calendar-event-line"></i>
            <span>7 Days / 6 Nights</span>
          </div>
          <div class="meta-item">
            <i class="ri-user-line"></i>
            <span>Max 6 People</span>
          </div>
          <div class="meta-item">
            <i class="ri-map-pin-line"></i>
            <span>Srinagar, Gulmarg, Pahalgam</span>
          </div>
          <div class="meta-item">
            <i class="ri-star-fill"></i>
            <span>4.9 (128 Reviews)</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Package Detail Content -->
    <div class="package-detail-container">
      <!-- Main Content -->
      <div class="package-main-content">
        <!-- Overview Section -->
        <section class="overview-section">
          <h2 class="section-title">Overview</h2>
          <div class="overview-content">
            <p>Experience the breathtaking beauty of Kashmir with our carefully crafted 7-day tour. This comprehensive package takes you through the most iconic destinations in the valley, from the serene Dal Lake to the majestic snow-capped mountains of Gulmarg.</p>
            
            <p>Our Kashmir Valley Explorer is designed to provide an authentic experience of Kashmiri culture, nature, and hospitality. You'll stay in traditional houseboats, enjoy shikara rides, explore Mughal gardens, and experience the warm hospitality of the local people.</p>
            
            <div class="highlights-grid">
              <div class="highlight-item">
                <div class="highlight-icon">
                  <i class="ri-hotel-line"></i>
                </div>
                <div>
                  <h4>4-Star Accommodation</h4>
                  <p>Comfortable stays with modern amenities</p>
                </div>
              </div>
              
              <div class="highlight-item">
                <div class="highlight-icon">
                  <i class="ri-restaurant-line"></i>
                </div>
                <div>
                  <h4>All Meals Included</h4>
                  <p>Authentic Kashmiri cuisine</p>
                </div>
              </div>
              
              <div class="highlight-item">
                <div class="highlight-icon">
                  <i class="ri-car-line"></i>
                </div>
                <div>
                  <h4>Private Transportation</h4>
                  <p>Comfortable AC vehicles</p>
                </div>
              </div>
              
              <div class="highlight-item">
                <div class="highlight-icon">
                  <i class="ri-guide-line"></i>
                </div>
                <div>
                  <h4>Expert Guide</h4>
                  <p>Knowledgeable local guide</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Itinerary Section -->
        <section class="itinerary-section">
          <h2 class="section-title">Itinerary</h2>
          
          <div class="itinerary-tabs">
            <button class="itinerary-tab active" data-day="day1">Day 1</button>
            <button class="itinerary-tab" data-day="day2">Day 2</button>
            <button class="itinerary-tab" data-day="day3">Day 3</button>
            <button class="itinerary-tab" data-day="day4">Day 4</button>
            <button class="itinerary-tab" data-day="day5">Day 5</button>
            <button class="itinerary-tab" data-day="day6">Day 6</button>
            <button class="itinerary-tab" data-day="day7">Day 7</button>
          </div>
          
          <div class="itinerary-content active" id="day1">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Arrival in Srinagar
              </h3>
              
              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Airport Pickup</h4>
                    <p>Meet and greet at Srinagar International Airport, transfer to hotel</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">2:00 PM</span>
                  <div class="activity-details">
                    <h4>Lunch</h4>
                    <p>Welcome lunch with authentic Kashmiri cuisine</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">4:00 PM</span>
                  <div class="activity-details">
                    <h4>Shikara Ride</h4>
                    <p>Evening shikara ride on Dal Lake, witness floating gardens</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">7:00 PM</span>
                  <div class="activity-details">
                    <h4>Dinner</h4>
                    <p>Dinner at hotel with traditional Kashmiri music performance</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          

          <!-- day 2 itenary -->
         <div class="itinerary-content active" id="day2">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Arrival in Srinagar
              </h3>
              
              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Airport Pickup</h4>
                    <p>Meet and greet at Srinagar International Airport, transfer to hotel</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">2:00 PM</span>
                  <div class="activity-details">
                    <h4>Lunch</h4>
                    <p>Welcome lunch with authentic Kashmiri cuisine</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">4:00 PM</span>
                  <div class="activity-details">
                    <h4>Shikara Ride</h4>
                    <p>Evening shikara ride on Dal Lake, witness floating gardens</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">7:00 PM</span>
                  <div class="activity-details">
                    <h4>Dinner</h4>
                    <p>featuring local delicacies</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>


          <!-- day 3 itinerary -->
          <div class="itinerary-content active" id="day3">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Arrival in Srinagar
              </h3>
              
              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Visit Mughal Gardens</h4>
                    <p>Explore the beautiful Mughal Gardens of Srinagar</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">2:00 PM</span>
                  <div class="activity-details">
                    <h4>Lunch</h4>
                    <p>Welcome lunch with authentic Kashmiri cuisine</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">4:00 PM</span>
                  <div class="activity-details">
                    <h4>Shikara Ride</h4>
                    <p>Evening shikara ride on Dal Lake, witness floating gardens</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">7:00 PM</span>
                  <div class="activity-details">
                    <h4>Dinner</h4>
                    <p>Dinner at hotel with traditional Kashmiri music performance</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- day 4 itinerary -->
          <div class="itinerary-content active" id="day4">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Departure from Srinagar
              </h3>
              
              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Airport Pickup</h4>
                    <p>Meet and greet at Srinagar International Airport, transfer to hotel</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">2:00 PM</span>
                  <div class="activity-details">
                    <h4>Lunch</h4>
                    <p>Welcome lunch with authentic Kashmiri cuisine</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">4:00 PM</span>
                  <div class="activity-details">
                    <h4>Shikara Ride</h4>
                    <p>Evening shikara ride on Dal Lake, witness floating gardens</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">7:00 PM</span>
                  <div class="activity-details">
                    <h4>Dinner</h4>
                    <p>Dinner at hotel with traditional Kashmiri music performance</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- day 5 itinerary -->
          <div class="itinerary-content active" id="day5">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Arrival in Srinagar
              </h3>
              
              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Airport Pickup</h4>
                    <p>Meet and greet at Srinagar International Airport, transfer to hotel</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">2:00 PM</span>
                  <div class="activity-details">
                    <h4>Lunch</h4>
                    <p>Welcome lunch with authentic Kashmiri cuisine</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">4:00 PM</span>
                  <div class="activity-details">
                    <h4>Shikara Ride</h4>
                    <p>Evening shikara ride on Dal Lake, witness floating gardens</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">7:00 PM</span>
                  <div class="activity-details">
                    <h4>Dinner</h4>
                    <p>Dinner at hotel with traditional Kashmiri music performance</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- day 6 itinerary -->
          <div class="itinerary-content active" id="day6">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Arrival in Srinagar
              </h3>
              
              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Airport Pickup</h4>
                    <p>Meet and greet at Srinagar International Airport, transfer to hotel</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">2:00 PM</span>
                  <div class="activity-details">
                    <h4>Lunch</h4>
                    <p>Welcome lunch with authentic Kashmiri cuisine</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">4:00 PM</span>
                  <div class="activity-details">
                    <h4>Shikara Ride</h4>
                    <p>Evening shikara ride on Dal Lake, witness floating gardens</p>
                  </div>
                </li>
                
                <li class="activity-item">
                  <span class="activity-time">7:00 PM</span>
                  <div class="activity-details">
                    <h4>Dinner</h4>
                    <p>Dinner at hotel with traditional Kashmiri music performance</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- day 7 itinerary -->
          <div class="itinerary-content active" id="day7">
            <div class="day-card">
              <h3 class="day-title">
                <i class="ri-map-pin-line"></i>
                Departure from Srinagar
              </h3>

              <ul class="activities-list">
                <li class="activity-item">
                  <span class="activity-time">12:00 PM</span>
                  <div class="activity-details">
                    <h4>Airport Drop</h4>
                    <p>Transfer to Srinagar International Airport for departure</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Additional days would be here -->
        </section>

        <!-- Inclusions Section -->
        <section class="inclusions-section">
          <h2 class="section-title">Inclusions & Exclusions</h2>
          
          <div class="inclusions-grid">
            <div class="inclusion-category">
              <h4>What's Included</h4>
              <ul class="inclusion-list">
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>Accommodation in 4-star hotels</span>
                </li>
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>Daily breakfast, lunch, and dinner</span>
                </li>
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>Private transportation throughout</span>
                </li>
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>Expert English-speaking guide</span>
                </li>
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>All entrance fees to monuments</span>
                </li>
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>Shikara ride on Dal Lake</span>
                </li>
                <li class="inclusion-item">
                  <i class="ri-checkbox-circle-fill"></i>
                  <span>Gondola ride in Gulmarg (Phase 1)</span>
                </li>
              </ul>
            </div>
            
            <div class="inclusion-category">
              <h4>What's Not Included</h4>
              <ul class="inclusion-list">
                <li class="exclusion-item">
                  <i class="ri-close-circle-fill"></i>
                  <span>Airfare to/from Srinagar</span>
                </li>
                <li class="exclusion-item">
                  <i class="ri-close-circle-fill"></i>
                  <span>Travel insurance</span>
                </li>
                <li class="exclusion-item">
                  <i class="ri-close-circle-fill"></i>
                  <span>Personal expenses</span>
                </li>
                <li class="exclusion-item">
                  <i class="ri-close-circle-fill"></i>
                  <span>Optional activities</span>
                </li>
                <li class="exclusion-item">
                  <i class="ri-close-circle-fill"></i>
                  <span>Tips for guides and drivers</span>
                </li>
              </ul>
            </div>
          </div>
        </section>

        <!-- Gallery Section -->
        <section class="gallery-section">
          <h2 class="section-title">Package Gallery</h2>
          
          <div class="gallery-grid">
            <div class="gallery-item">
              <img src="../assets/img/bg1.jpg" alt="Dal Lake">
            </div>
            <div class="gallery-item">
              <img src="../assets/img/bg1.jpg" alt="Gulmarg">
            </div>
            <div class="gallery-item">
              <img src="../assets/img/bg1.jpg" alt="Shikara Ride">
            </div>
            <div class="gallery-item">
              <img src="../assets/img/bg1.jpg" alt="Pahalgam Valley">
            </div>
            <div class="gallery-item">
              <img src="../assets/img/bg1.jpg" alt="Kashmiri Cuisine">
            </div>
            <div class="gallery-item">
              <img src="../assets/img/bg1.jpg" alt="Houseboat Stay">
            </div>
          </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
          <h2 class="section-title">Frequently Asked Questions</h2>
          
          <div class="faq-item">
            <div class="faq-question">
              What is the best time to take this tour?
              <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="faq-answer">
              <p>The Kashmir Valley Explorer is best enjoyed from April to October when the weather is pleasant and all destinations are accessible. Spring (April-May) offers blooming flowers, while autumn (September-October) provides clear skies and stunning foliage.</p>
            </div>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              What should I pack for this trip?
              <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="faq-answer">
              <p>We recommend packing layered clothing as temperatures can vary. Include warm clothes for evenings, comfortable walking shoes, sunscreen, sunglasses, and a hat. Don't forget your camera to capture the beautiful landscapes!</p>
            </div>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              Is this tour suitable for children and elderly?
              <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="faq-answer">
              <p>Yes, this tour is suitable for all age groups. We can customize the pace and activities based on your group's needs. Please inform us in advance if you have any specific requirements.</p>
            </div>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              What is the cancellation policy?
              <i class="ri-arrow-down-s-line"></i>
            </div>
            <div class="faq-answer">
              <p>Cancellations made 30 days before departure receive a full refund. Between 15-30 days, we offer a 70% refund. Cancellations within 15 days of departure are eligible for a 50% refund or the option to reschedule.</p>
            </div>
          </div>
        </section>
      </div>

      <!-- Booking Widget -->
      <div class="booking-widget">
        <div class="price-section">
          <div class="price-amount">₹25,999</div>
          <div class="price-note">per person (double occupancy)</div>
          <span class="discount-badge">Save 15% if booked 60 days in advance</span>
        </div>
        
        <form class="booking-form">
          <div class="form-group">
            <label for="checkin">Check-in Date</label>
            <input type="date" id="checkin" required>
          </div>
          
          <div class="form-group">
            <label for="guests">Number of Guests</label>
            <div class="guest-counter">
              <button type="button" class="counter-btn" id="decrease-guests">-</button>
              <input type="number" id="guests" value="2" min="1" max="6" readonly>
              <button type="button" class="counter-btn" id="increase-guests">+</button>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="adults">Adults</label>
              <select id="adults" required>
                <option value="1">1 Adult</option>
                <option value="2" selected>2 Adults</option>
                <option value="3">3 Adults</option>
                <option value="4">4 Adults</option>
                <option value="5">5 Adults</option>
                <option value="6">6 Adults</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="children">Children</label>
              <select id="children">
                <option value="0" selected>No Children</option>
                <option value="1">1 Child</option>
                <option value="2">2 Children</option>
                <option value="3">3 Children</option>
              </select>
            </div>
          </div>
          
          <div class="booking-summary">
            <div class="summary-item">
              <span>2 Adults x ₹25,999</span>
              <span>₹51,998</span>
            </div>
            <div class="summary-item">
              <span>Taxes & Fees</span>
              <span>₹2,600</span>
            </div>
            <div class="summary-item">
              <span>Total</span>
              <span>₹54,598</span>
            </div>
          </div>
          
          <button type="submit" class="book-now-btn">Book Now</button>
        </form>
      </div>
    </div>

    <!--=============== MAIN JS ===============-->
    <script src="../assets/js/main.js"></script>
    
    <script>
      // Itinerary Tab Functionality
      document.addEventListener('DOMContentLoaded', function() {
        const itineraryTabs = document.querySelectorAll('.itinerary-tab');
        const itineraryContents = document.querySelectorAll('.itinerary-content');
        
        itineraryTabs.forEach(tab => {
          tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            itineraryTabs.forEach(t => t.classList.remove('active'));
            itineraryContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const day = tab.getAttribute('data-day');
            document.getElementById(day).classList.add('active');
          });
        });
        
        // FAQ Accordion Functionality
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
          question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            faqItem.classList.toggle('active');
          });
        });
        
        // Guest Counter Functionality
        const decreaseBtn = document.getElementById('decrease-guests');
        const increaseBtn = document.getElementById('increase-guests');
        const guestsInput = document.getElementById('guests');
        const adultsSelect = document.getElementById('adults');
        const childrenSelect = document.getElementById('children');
        
        decreaseBtn.addEventListener('click', () => {
          let value = parseInt(guestsInput.value);
          if (value > 1) {
            guestsInput.value = value - 1;
            updateGuestDetails();
          }
        });
        
        increaseBtn.addEventListener('click', () => {
          let value = parseInt(guestsInput.value);
          if (value < 6) {
            guestsInput.value = value + 1;
            updateGuestDetails();
          }
        });
        
        function updateGuestDetails() {
          const totalGuests = parseInt(guestsInput.value);
          const children = parseInt(childrenSelect.value);
          const adults = totalGuests - children;
          
          if (adults >= 1) {
            adultsSelect.value = adults;
          }
        }
        
        adultsSelect.addEventListener('change', updateTotalGuests);
        childrenSelect.addEventListener('change', updateTotalGuests);
        
        function updateTotalGuests() {
          const adults = parseInt(adultsSelect.value);
          const children = parseInt(childrenSelect.value);
          guestsInput.value = adults + children;
        }
        
        // Initialize animations
        setTimeout(() => {
          document.querySelectorAll('.highlight-item, .day-card, .gallery-item, .faq-item').forEach((item, index) => {
            setTimeout(() => {
              item.style.opacity = '1';
              item.style.transform = 'translateY(0)';
            }, index * 100);
          });
        }, 500);
      });
    </script>
    <script>
      // Smooth Scroll Functionality
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();

          document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
          });
        });
      });
    </script>
    <script>
      //calculate package booking widget
      document.addEventListener('DOMContentLoaded', function() {
        const pricePerPerson = 25999;
        const taxAndFees = 2600;
        const guestsInput = document.getElementById('guests');
        const adultsSelect = document.getElementById('adults');
        const childrenSelect = document.getElementById('children');
        const summaryItems = document.querySelectorAll('.booking-summary .summary-item');
        const priceAmount = document.querySelector('.price-amount');

        function updateBookingSummary() {
          const adults = parseInt(adultsSelect.value);
          const children = parseInt(childrenSelect.value);
          const totalGuests = adults + children;
          guestsInput.value = totalGuests;

          // Calculate base price (children under 12 get 30% discount, if needed you can adjust)
          let childrenDiscount = 0.7; // 30% off
          let total = (adults * pricePerPerson) + (children * pricePerPerson * childrenDiscount);
          let totalWithTax = total + taxAndFees;

          // Update summary
          summaryItems[0].children[0].textContent = `${adults} Adult${adults > 1 ? 's' : ''}${children > 0 ? ` + ${children} Child${children > 1 ? 'ren' : ''}` : ''} x ₹${pricePerPerson}`;
          summaryItems[0].children[1].textContent = `₹${total.toLocaleString()}`;
          summaryItems[2].children[1].textContent = `₹${totalWithTax.toLocaleString()}`;
        }

        // Update on input changes
        adultsSelect.addEventListener('change', updateBookingSummary);
        childrenSelect.addEventListener('change', updateBookingSummary);

        // Also update on plus/minus buttons
        document.getElementById('increase-guests').addEventListener('click', updateBookingSummary);
        document.getElementById('decrease-guests').addEventListener('click', updateBookingSummary);

        // Initial update
        updateBookingSummary();
      });
    </script>
  </body>
</html>