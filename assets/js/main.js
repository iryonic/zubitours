// Loader

 window.addEventListener("load", () => {
    const loader = document.getElementById("loader");
    setTimeout(() => {
      loader.classList.add("hidden");
    }, 1200); // smooth fade
  });








/*=============== SHOW MENU ===============*/
const navMenu = document.getElementById('nav-menu'),
      navToggle = document.getElementById('nav-toggle'),
      navClose = document.getElementById('nav-close'),
      navLink = document.querySelectorAll('.nav__link')

/* Menu show */
navToggle.addEventListener('click', () =>{
   navMenu.classList.add('show-menu')
   

})

/* Menu hidden */
navClose.addEventListener('click', () =>{
   navMenu.classList.remove('show-menu')
})

/*=============== SEARCH ===============*/
const search = document.getElementById('search'),
      searchBtn = document.getElementById('search-btn'),
      searchClose = document.getElementById('search-close')


      //get full year
      const currentYear = new Date().getFullYear();
      document.getElementById("getYear").textContent = currentYear;


//Swiper JS
//////// swiper //////
  new Swiper(".wrapper", {
        loop: true,
        spaceBetween: 30,
        // Autoplay
      //   autoplay: {
      //     delay: 5000,
      //     disableOnInteraction: false,
      //     pauseOnMouseEnter: true,
      //   },
        // Pagination bullets
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
          dynamicBullets: true,
        },
        // Navigation arrows
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        // Responsive breakpoints
        breakpoints: {
          0: {
            slidesPerView: 1,
          },
          550: {
            slidesPerView: 2,
          },
           1024: {
            slidesPerView: 3,
          },
        
        },
      });


   // enhanced
   // Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Initialize navigation menu
  initNavMenu();
  
  // Initialize destination filtering if on destinations page
  if (document.getElementById('destinations-container')) {
    initDestinationFilters();
  }
  
  // Initialize package filtering if on packages page
  if (document.getElementById('packages-container')) {
    initPackageFilters();
  }
  
  // Initialize gallery if on gallery page
  if (document.querySelector('.masonry-grid')) {
    initGallery();
  }
  
  // Initialize car rentals if on car rentals page
  if (document.getElementById('cars-container')) {
    initCarRentals();
  }
  
  // Initialize brands carousel if on homepage
  if (document.querySelector('.brands-track')) {
    initBrandsCarousel();
  }
});

// Initialize navigation menu
function initNavMenu() {
  const navMenu = document.getElementById('nav-menu');
  const navToggle = document.getElementById('nav-toggle');
  const navClose = document.getElementById('nav-close');
  
  if (navToggle) {
    navToggle.addEventListener('click', () => {
      navMenu.classList.add('show-menu');
    });
  }
  
  if (navClose) {
    navClose.addEventListener('click', () => {
      navMenu.classList.remove('show-menu');
    });
  }
  
  // Close menu when clicking on nav links
  const navLinks = document.querySelectorAll('.nav__link');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('show-menu');
    });
  });
}

// Initialize destination filters
function initDestinationFilters() {
  const filters = {
    region: "all",
    category: "all"
  };

  function filterDestinations() {
    const destinations = document.querySelectorAll(".destination-card");
    destinations.forEach(destination => {
      const region = destination.getAttribute("data-region");
      const category = destination.getAttribute("data-category");

      const match = 
        (filters.region === "all" || filters.region === region) &&
        (filters.category === "all" || filters.category === category);

      destination.style.display = match ? "block" : "none";
    });
  }

  document.getElementById("destination-region").addEventListener("change", (e) => {
    filters.region = e.target.value;
    filterDestinations();
  });

  document.getElementById("destination-category").addEventListener("change", (e) => {
    filters.category = e.target.value;
    filterDestinations();
  });

  window.resetDestinationFilters = function() {
    filters.region = "all";
    filters.category = "all";
    document.getElementById("destination-region").value = "all";
    document.getElementById("destination-category").value = "all";
    filterDestinations();
  };
}

// Initialize package filters
function initPackageFilters() {
  const filters = {
    duration: "all",
    price: "all"
  };

  function filterPackages() {
    const packages = document.querySelectorAll(".package-card");
    packages.forEach(pkg => {
      const duration = pkg.getAttribute("data-duration");
      const priceRange = pkg.getAttribute("data-price");

      const match = 
        (filters.duration === "all" || filters.duration === duration) &&
        (filters.price === "all" || filters.price === priceRange);

      pkg.style.display = match ? "flex" : "none";
    });
  }

  document.getElementById("package-duration").addEventListener("change", (e) => {
    filters.duration = e.target.value;
    filterPackages();
  });

  document.getElementById("package-price").addEventListener("change", (e) => {
    filters.price = e.target.value;
    filterPackages();
  });

  window.resetPackageFilters = function() {
    filters.duration = "all";
    filters.price = "all";
    document.getElementById("package-duration").value = "all";
    document.getElementById("package-price").value = "all";
    filterPackages();
  };
}

// Initialize gallery
function initGallery() {
  // This will be handled by the inline script in gallery.html
  console.log("Gallery initialized");
}

// Initialize car rentals
function initCarRentals() {
  // This will be handled by the inline script in car-rentals.html
  console.log("Car rentals initialized");
}

// Initialize brands carousel
function initBrandsCarousel() {
  // The animation is handled by CSS, but we can add interactivity here
  const brandItems = document.querySelectorAll('.brand-item');
  
  brandItems.forEach(item => {
    item.addEventListener('mouseenter', () => {
      item.querySelector('img').style.transform = 'scale(1.1)';
    });
    
    item.addEventListener('mouseleave', () => {
      item.querySelector('img').style.transform = 'scale(1)';
    });
  });
}

// Scroll to top function
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}








