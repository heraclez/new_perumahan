/**
 * Spring Motion & Lenis Scroll Engine for CodeIgniter 4
 * Total replica of Next.js @react-spring/web & spring-text-engine behavior in Vanilla JS.
 */
(function() {
  'use strict';

  // --------------------------------------------------------------------------
  // 1. LENIS SMOOTH SCROLL INITIALIZATION
  // --------------------------------------------------------------------------
  let lenisInstance = null;
  if (typeof Lenis !== 'undefined') {
    lenisInstance = new Lenis({
      duration: 1.2,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Exponential smooth deceleration
      orientation: 'vertical',
      gestureOrientation: 'vertical',
      smoothWheel: true,
      wheelMultiplier: 1,
      touchMultiplier: 2,
      infinite: false,
    });

    function raf(time) {
      lenisInstance.raf(time);
      requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // Synchronize with GSAP ScrollTrigger if available
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      lenisInstance.on('scroll', ScrollTrigger.update);
      gsap.ticker.add((time) => {
        lenisInstance.raf(time * 1000);
      });
      gsap.ticker.lagSmoothing(0);
    }
  }

  // --------------------------------------------------------------------------
  // 2. SPRING TEXT ENGINE (Replica of spring-text-engine & TextEngine)
  // --------------------------------------------------------------------------
  function initSpringText() {
    const textElements = document.querySelectorAll('[data-spring-text]');
    
    textElements.forEach((el) => {
      const mode = el.getAttribute('data-spring-mode') || 'forward';
      const delay = parseFloat(el.getAttribute('data-spring-delay') || '0.1');
      const stagger = parseFloat(el.getAttribute('data-spring-stagger') || '0.035');
      const originalText = el.textContent.trim();

      // Split words and characters preserving spaces
      const words = originalText.split(' ');
      el.innerHTML = '';
      el.style.opacity = '1';

      const charSpans = [];

      words.forEach((word, wordIndex) => {
        const wordSpan = document.createElement('span');
        wordSpan.className = 'spring-word';

        const chars = word.split('');
        chars.forEach((char) => {
          const charSpan = document.createElement('span');
          charSpan.className = 'spring-char';
          charSpan.textContent = char;
          charSpan.style.opacity = '0';
          charSpan.style.transform = 'translateY(120%) rotate(4deg)';
          wordSpan.appendChild(charSpan);
          charSpans.push(charSpan);
        });

        el.appendChild(wordSpan);

        // Add space after word if not the last word
        if (wordIndex < words.length - 1) {
          const space = document.createTextNode(' ');
          el.appendChild(space);
        }
      });

      // Dynamic spring physics based on active theme preset
      const currentPreset = document.documentElement.getAttribute('data-theme-preset') || 'industrialist';
      const textEase = currentPreset === 'brutalist' ? 'elastic.out(0.85, 0.9)' : 'elastic.out(1.25, 0.55)';
      const textDuration = currentPreset === 'brutalist' ? 1.4 : 1.1;

      // Animate with GSAP spring physics
      if (typeof gsap !== 'undefined') {
        const triggerOptions = {
          trigger: el,
          start: 'top 88%',
          once: mode === 'once' || mode === 'forward',
        };

        gsap.to(charSpans, {
          scrollTrigger: typeof ScrollTrigger !== 'undefined' ? triggerOptions : null,
          opacity: 1,
          y: 0,
          rotate: 0,
          duration: textDuration,
          delay: delay,
          stagger: stagger,
          ease: textEase, // Theme-aware spring physics
        });
      } else {
        // Fallback CSS reveal
        charSpans.forEach((span, idx) => {
          setTimeout(() => {
            span.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            span.style.opacity = '1';
            span.style.transform = 'translateY(0) rotate(0)';
          }, (delay * 1000) + (idx * (stagger * 1000)));
        });
      }
    });
  }

  // --------------------------------------------------------------------------
  // 3. IN-VIEW SPRING REVEAL (Replica of InView Spring component)
  // --------------------------------------------------------------------------
  function initInViewSprings() {
    const inViewElements = document.querySelectorAll('[data-spring-inview]');
    const currentPreset = document.documentElement.getAttribute('data-theme-preset') || 'industrialist';
    const inviewEase = currentPreset === 'brutalist' ? 'elastic.out(0.85, 0.95)' : 'elastic.out(1.2, 0.65)';
    const inviewY = currentPreset === 'brutalist' ? 70 : 45;

    inViewElements.forEach((el, index) => {
      const delay = parseFloat(el.getAttribute('data-spring-delay') || '0');
      const staggerIndex = el.getAttribute('data-spring-stagger') ? parseFloat(el.getAttribute('data-spring-stagger')) : 0;
      
      if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.fromTo(el, 
          {
            opacity: 0,
            y: inviewY,
            scale: currentPreset === 'brutalist' ? 1 : 0.96
          },
          {
            scrollTrigger: {
              trigger: el,
              start: 'top 85%',
              toggleActions: 'play none none none'
            },
            opacity: 1,
            y: 0,
            scale: 1,
            duration: currentPreset === 'brutalist' ? 1.5 : 1.2,
            delay: delay + (staggerIndex * 0.12),
            ease: inviewEase
          }
        );
      } else {
        // Fallback Intersection Observer
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.15 });
        observer.observe(el);
      }
    });
  }

  // --------------------------------------------------------------------------
  // 4. INTERACTIVE SPRING HOVER PHYSICS (Replica of Hover spring)
  // --------------------------------------------------------------------------
  function initSpringHover() {
    const hoverElements = document.querySelectorAll('.spring-hover-target, .spring-card, .spring-btn-primary');
    const currentPreset = document.documentElement.getAttribute('data-theme-preset') || 'industrialist';

    hoverElements.forEach((el) => {
      if (typeof gsap === 'undefined') return;

      if (currentPreset === 'brutalist') {
        el.addEventListener('mouseenter', () => {
          gsap.to(el, {
            x: -3,
            y: -3,
            duration: 0.15,
            ease: 'power2.out',
            overwrite: 'auto'
          });
        });

        el.addEventListener('mouseleave', () => {
          gsap.to(el, {
            x: 0,
            y: 0,
            duration: 0.2,
            ease: 'power2.out',
            overwrite: 'auto'
          });
        });
      } else {
        el.addEventListener('mouseenter', () => {
          gsap.to(el, {
            scale: 1.025,
            y: -5,
            duration: 0.5,
            ease: 'elastic.out(1.4, 0.5)',
            overwrite: 'auto'
          });
        });

        el.addEventListener('mouseleave', () => {
          gsap.to(el, {
            scale: 1,
            y: 0,
            duration: 0.6,
            ease: 'elastic.out(1.2, 0.4)',
            overwrite: 'auto'
          });
        });
      }
    });
  }

  // --------------------------------------------------------------------------
  // 5. THEME TOGGLE (Dark / Light) - STRICT LIGHT DEFAULT
  // --------------------------------------------------------------------------
  window.toggleTheme = function() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('site-theme', newTheme);

    const icon = document.getElementById('theme-icon');
    if (icon) {
      icon.className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }
  };

  // Restore saved theme ONLY if explicitly set to 'dark' by user
  const savedTheme = localStorage.getItem('site-theme');
  if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.setAttribute('data-theme', 'light');
  }

  // --------------------------------------------------------------------------
  // INITIALIZATION ON DOM READY
  // --------------------------------------------------------------------------
  document.addEventListener('DOMContentLoaded', () => {
    initSpringText();
    initInViewSprings();
    initSpringHover();
    
    // Update theme icon on load
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const icon = document.getElementById('theme-icon');
    if (icon) {
      icon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }
  });

})();
