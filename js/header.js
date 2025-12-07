/**
 * Modern Header JavaScript 2025
 * Elegant, Professional & Responsive
 */

(function() {
    'use strict';

    class ModernHeader {
        constructor() {
            this.nav = document.querySelector('.nav');
            this.toggleBtn = document.querySelector('.toggle-collapse');
            this.navItems = document.querySelector('.nav-items');
            this.navLinks = document.querySelectorAll('.nav-link a');
            this.lastScrollY = window.scrollY;
            this.isScrolling = false;
            
            this.init();
        }

        init() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.setup());
            } else {
                this.setup();
            }
        }

        setup() {
            this.handleScroll();
            this.handleMobileMenu();
            this.handleActiveLinks();
            this.handleSmoothScroll();
            this.handleResize();
            this.handleKeyboardNavigation();
            
            // Add scroll event listener with throttling
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        this.handleScroll();
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }

        /**
         * Handle scroll effects
         */
        handleScroll() {
            const currentScrollY = window.scrollY;
            
            // Add scrolled class when scrolling down
            if (currentScrollY > 50) {
                this.nav?.classList.add('scrolled');
            } else {
                this.nav?.classList.remove('scrolled');
            }

            // Hide/show navbar on scroll (optional - can be enabled)
            // this.handleNavbarVisibility(currentScrollY);
            
            this.lastScrollY = currentScrollY;
        }

        /**
         * Handle navbar visibility on scroll (optional feature)
         */
        handleNavbarVisibility(currentScrollY) {
            if (!this.nav) return;

            const scrollDifference = currentScrollY - this.lastScrollY;
            
            if (scrollDifference > 5 && currentScrollY > 100) {
                // Scrolling down - hide navbar
                this.nav.style.transform = 'translateY(-100%)';
            } else if (scrollDifference < -5) {
                // Scrolling up - show navbar
                this.nav.style.transform = 'translateY(0)';
            }
        }

        /**
         * Handle mobile menu toggle
         */
        handleMobileMenu() {
            if (!this.toggleBtn || !this.navItems) return;

            this.toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isActive = this.nav?.classList.contains('collapse');
                
                // Toggle classes
                this.nav?.classList.toggle('collapse');
                this.toggleBtn.classList.toggle('active');
                this.navItems.classList.toggle('active');
                
                // Prevent body scroll when menu is open
                if (!isActive) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
                
                // Close menu when clicking outside
                if (!isActive) {
                    setTimeout(() => {
                        document.addEventListener('click', this.closeMenuOnOutsideClick.bind(this), { once: true });
                    }, 100);
                }
            });

            // Close menu when clicking on a link
            this.navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        this.closeMobileMenu();
                    }
                });
            });
        }

        /**
         * Close mobile menu
         */
        closeMobileMenu() {
            this.nav?.classList.remove('collapse');
            this.toggleBtn?.classList.remove('active');
            this.navItems?.classList.remove('active');
            document.body.style.overflow = '';
        }

        /**
         * Close menu when clicking outside
         */
        closeMenuOnOutsideClick(e) {
            if (!this.nav?.contains(e.target) && !this.toggleBtn?.contains(e.target)) {
                this.closeMobileMenu();
            }
        }

        /**
         * Handle active navigation links
         */
        handleActiveLinks() {
            const currentPath = window.location.pathname;
            const currentPage = currentPath.split('/').pop() || 'index.php';
            
            this.navLinks.forEach(link => {
                const linkPath = new URL(link.href).pathname;
                const linkPage = linkPath.split('/').pop() || 'index.php';
                
                // Remove active class from all links
                link.classList.remove('active');
                
                // Add active class to current page
                if (linkPage === currentPage || 
                    (currentPage === '' && linkPage === 'index.php') ||
                    (currentPage === 'index.php' && linkPage === 'index.php')) {
                    link.classList.add('active');
                }
            });
        }

        /**
         * Handle smooth scroll for anchor links
         */
        handleSmoothScroll() {
            this.navLinks.forEach(link => {
                const href = link.getAttribute('href');
                
                if (href && href.startsWith('#')) {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const targetId = href.substring(1);
                        const targetElement = document.getElementById(targetId);
                        
                        if (targetElement) {
                            const headerHeight = this.nav?.offsetHeight || 80;
                            const targetPosition = targetElement.offsetTop - headerHeight - 20;
                            
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                            
                            // Close mobile menu if open
                            if (window.innerWidth <= 768) {
                                this.closeMobileMenu();
                            }
                        }
                    });
                }
            });
        }

        /**
         * Handle window resize
         */
        handleResize() {
            let resizeTimer;
            
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                
                resizeTimer = setTimeout(() => {
                    // Close mobile menu on resize to desktop
                    if (window.innerWidth > 768) {
                        this.closeMobileMenu();
                    }
                    
                    // Recalculate active links
                    this.handleActiveLinks();
                }, 250);
            });
        }

        /**
         * Handle keyboard navigation
         */
        handleKeyboardNavigation() {
            // ESC key closes mobile menu
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.nav?.classList.contains('collapse')) {
                    this.closeMobileMenu();
                }
            });

            // Tab navigation enhancement
            this.navLinks.forEach((link, index) => {
                link.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                        e.preventDefault();
                        const nextLink = this.navLinks[index + 1] || this.navLinks[0];
                        nextLink?.focus();
                    } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                        e.preventDefault();
                        const prevLink = this.navLinks[index - 1] || this.navLinks[this.navLinks.length - 1];
                        prevLink?.focus();
                    }
                });
            });
        }

        /**
         * Update active link on scroll (for single page apps)
         */
        updateActiveLinkOnScroll() {
            const sections = document.querySelectorAll('section[id]');
            const scrollPosition = window.scrollY + 100;

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');

                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    this.navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }
    }

    // Initialize header when DOM is ready
    const header = new ModernHeader();

    // Export for global access if needed
    window.ModernHeader = ModernHeader;
    window.blogHeader = header;

})();

/**
 * Hero Parallax Effects
 * Scroll-based parallax and fade effects for hero section
 */
(function() {
    'use strict';

    class HeroEffects {
        constructor() {
            this.bg = document.querySelector('.heroEffects .bg');
            this.shade = document.querySelector('.heroEffects .shade');
            this.text = document.querySelector('.heroEffects .text');
            this.arrow = document.querySelector('.heroEffects .arrow');
            
            if (this.bg && this.shade && this.text) {
                this.init();
            }
        }

        init() {
            // Use jQuery if available, otherwise vanilla JS
            if (typeof jQuery !== 'undefined') {
                this.initWithJQuery();
            } else {
                this.initWithVanilla();
            }

            // Arrow click to scroll
            if (this.arrow) {
                this.arrow.addEventListener('click', () => {
                    const postsSection = document.getElementById('posts');
                    if (postsSection) {
                        const headerHeight = document.querySelector('.nav')?.offsetHeight || 80;
                        const targetPosition = postsSection.offsetTop - headerHeight - 20;
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            }
        }

        initWithJQuery() {
            const self = this;
            const heroHeight = self.bg ? self.bg.offsetHeight : window.innerHeight;
            
            jQuery(window).on('scroll', function() {
                const scrollTop = jQuery(window).scrollTop();
                
                // Subtle parallax effects only
                if (scrollTop <= heroHeight) {
                    const progress = scrollTop / heroHeight;
                    
                    // Very subtle shade increase
                    if (self.shade) {
                        self.shade.style.opacity = Math.min(progress * 0.4, 0.3);
                    }

                    // Gentle background zoom
                    if (self.bg) {
                        const scale = 1 + (scrollTop * 0.0002);
                        self.bg.style.transform = `scale(${Math.min(scale, 1.1)})`;
                    }

                    // Subtle text fade
                    if (self.text) {
                        self.text.style.opacity = Math.max(1 - progress * 0.3, 0.7);
                    }
                }

                // Hide arrow smoothly
                if (self.arrow) {
                    if (scrollTop > 100) {
                        self.arrow.style.opacity = '0';
                        self.arrow.style.pointerEvents = 'none';
                    } else {
                        const arrowOpacity = Math.max(1 - (scrollTop / 100), 0);
                        self.arrow.style.opacity = arrowOpacity.toString();
                        self.arrow.style.pointerEvents = arrowOpacity > 0.3 ? 'auto' : 'none';
                    }
                }
            });
        }

        initWithVanilla() {
            let ticking = false;
            const self = this;
            const heroHeight = self.bg ? self.bg.offsetHeight : window.innerHeight;

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        
                        // Subtle parallax effects only
                        if (scrollTop <= heroHeight) {
                            const progress = scrollTop / heroHeight;
                            
                            // Very subtle shade increase
                            if (self.shade) {
                                self.shade.style.opacity = Math.min(progress * 0.4, 0.3);
                            }

                            // Gentle background zoom
                            if (self.bg) {
                                const scale = 1 + (scrollTop * 0.0002);
                                self.bg.style.transform = `scale(${Math.min(scale, 1.1)})`;
                            }

                            // Subtle text fade
                            if (self.text) {
                                self.text.style.opacity = Math.max(1 - progress * 0.3, 0.7);
                            }
                        }

                        // Hide arrow smoothly
                        if (self.arrow) {
                            if (scrollTop > 100) {
                                self.arrow.style.opacity = '0';
                                self.arrow.style.pointerEvents = 'none';
                            } else {
                                const arrowOpacity = Math.max(1 - (scrollTop / 100), 0);
                                self.arrow.style.opacity = arrowOpacity.toString();
                                self.arrow.style.pointerEvents = arrowOpacity > 0.3 ? 'auto' : 'none';
                            }
                        }

                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new HeroEffects();
        });
    } else {
        new HeroEffects();
    }

    // Export for global access
    window.HeroEffects = HeroEffects;

})();

