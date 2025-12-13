// assets/js/gsap.js

document.addEventListener('DOMContentLoaded', function() {
  // Initialize ScrollTrigger
  gsap.registerPlugin(ScrollTrigger);
  
  // Only run if GSAP and ScrollTrigger are available
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    
    // Fade up animation for elements
    gsap.utils.toArray('.fade-up').forEach(element => {
      gsap.fromTo(element, 
        { y: 50, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 85%',
            toggleActions: 'play none none reverse',
            stagger: 0.1,
          }
        }
      );
    });
    
    // Fade in animation
    gsap.utils.toArray('.fade-in').forEach(element => {
      gsap.fromTo(element, 
        { opacity: 0 },
        {
          opacity: 1,
          duration: 1.2,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 90%',
            toggleActions: 'play none none reverse'
          }
        }
      );
    });
    
    // Scale in animation
    gsap.utils.toArray('.scale-in').forEach(element => {
      gsap.fromTo(element, 
        { scale: 0.8, opacity: 0 },
        {
          scale: 1,
          opacity: 1,
          duration: 1,
          ease: 'back.out(1.7)',
          scrollTrigger: {
            trigger: element,
            start: 'top 85%',
            toggleActions: 'play none none reverse'
          }
        }
      );
    });
    
    // Slide in from left
    gsap.utils.toArray('.slide-left').forEach(element => {
      gsap.fromTo(element, 
        { x: -100, opacity: 0 },
        {
          x: 0,
          opacity: 1,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 90%',
            toggleActions: 'play none none reverse'
          }
        }
      );
    });
    
    // Slide in from right
    gsap.utils.toArray('.slide-right').forEach(element => {
      gsap.fromTo(element, 
        { x: 100, opacity: 0 },
        {
          x: 0,
          opacity: 1,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 90%',
            toggleActions: 'play none none reverse'
          }
        }
      );
    });
    
    // Stagger animations for cards and grid items
    gsap.utils.toArray('.stagger-container').forEach(container => {
      const items = container.querySelectorAll('.stagger-item');
      gsap.fromTo(items, 
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.6,
          stagger: 0.15,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: container,
            start: 'top 85%',
            toggleActions: 'play none none reverse'
          }
        }
      );
    });
    
    // Hero text animation (if not already animated)
    const heroText = document.querySelector('.hero-content');
    if (heroText && !heroText.classList.contains('animated')) {
      gsap.fromTo(heroText.querySelector('h1'), 
        { y: 50, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, delay: 0.3 }
      );
      gsap.fromTo(heroText.querySelector('p'), 
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, delay: 0.6 }
      );
      heroText.classList.add('animated');
    }
  }
});

// Parallax effect for elements with 'parallax' class
function initParallax() {
  gsap.utils.toArray('.parallax').forEach(layer => {
    const depth = layer.dataset.depth || 0.5;
    const movement = -(layer.offsetHeight * depth);
    
    gsap.to(layer, {
      y: movement,
      ease: 'none',
      scrollTrigger: {
        trigger: layer,
        start: 'top bottom',
        end: 'bottom top',
        scrub: true
      }
    });
  });
}

// Initialize when page loads
window.addEventListener('load', function() {
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    initParallax();
    
    // Refresh ScrollTrigger on page load to ensure proper calculations
    ScrollTrigger.refresh();
  }
});