// assets/js/gsap.js

document.addEventListener('DOMContentLoaded', function() {
  // Initialize ScrollTrigger
  gsap.registerPlugin(ScrollTrigger);
  
  // Only run if GSAP and ScrollTrigger are available
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    
    // Performance optimization: Hardware acceleration
    gsap.set('.fade-up, .fade-in, .scale-in, .slide-left, .slide-right', { willChange: 'transform, opacity' });

    // Fade up animation for elements (Faster: 0.6s)
    gsap.utils.toArray('.fade-up').forEach(element => {
      gsap.fromTo(element, 
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.6,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 92%',
            toggleActions: 'play none none none',
            once: true // Performance: only animate once
          }
        }
      );
    });
    
    // Fade in animation (Snappier)
    gsap.utils.toArray('.fade-in').forEach(element => {
      gsap.fromTo(element, 
        { opacity: 0 },
        {
          opacity: 1,
          duration: 0.5,
          ease: 'power2.inOut',
          scrollTrigger: {
            trigger: element,
            start: 'top 95%',
            toggleActions: 'play none none none',
            once: true
          }
        }
      );
    });
    
    // Scale in animation (Enhanced)
    gsap.utils.toArray('.scale-in').forEach(element => {
      gsap.fromTo(element, 
        { scale: 0.9, opacity: 0 },
        {
          scale: 1,
          opacity: 1,
          duration: 0.6,
          ease: 'back.out(1.2)',
          scrollTrigger: {
            trigger: element,
            start: 'top 90%',
            once: true
          }
        }
      );
    });
    
    // Slide in from left (Snappy)
    gsap.utils.toArray('.slide-left').forEach(element => {
      gsap.fromTo(element, 
        { x: -50, opacity: 0 },
        {
          x: 0,
          opacity: 1,
          duration: 0.6,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 90%',
            once: true
          }
        }
      );
    });
    
    // Slide in from right (Snappy)
    gsap.utils.toArray('.slide-right').forEach(element => {
      gsap.fromTo(element, 
        { x: 50, opacity: 0 },
        {
          x: 0,
          opacity: 1,
          duration: 0.6,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 90%',
            once: true
          }
        }
      );
    });
    
    // Stagger animations (Faster stagger)
    gsap.utils.toArray('.stagger-container').forEach(container => {
      const items = container.querySelectorAll('.stagger-item');
      gsap.fromTo(items, 
        { y: 20, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.5,
          stagger: 0.08,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: container,
            start: 'top 90%',
            once: true
          }
        }
      );
    });
    
    // Hero text animation (Immediate and snappy)
    const heroText = document.querySelector('.hero-content');
    if (heroText && !heroText.classList.contains('animated')) {
      gsap.fromTo(heroText.querySelector('h1'), 
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.7, delay: 0.2, ease: 'power4.out' }
      );
      gsap.fromTo(heroText.querySelector('p'), 
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.7, delay: 0.4, ease: 'power4.out' }
      );
      heroText.classList.add('animated');
    }
  }
});

// Parallax effect (Only if not mobile to save battery/performance)
function initParallax() {
  if (window.innerWidth > 768) {
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
}

// Initialize when page loads
window.addEventListener('load', function() {
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    initParallax();
    ScrollTrigger.refresh();
  }
});
