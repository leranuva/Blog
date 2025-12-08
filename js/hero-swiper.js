/**
 * Hero Vertical Slider with Swiper
 * Adapted from modern slider design
 */

(function() {
    'use strict';

    function initHeroSwiper() {
        const heroSwiper = document.querySelector('.heroSwiper');
        if (!heroSwiper) return;

        // Wait for Swiper to be available
        if (typeof Swiper !== 'undefined') {
            const mainSlider = new Swiper('.heroSwiper', {
                parallax: true,
                speed: 1200,
                effect: 'slide',
                direction: 'vertical',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.hero-button-next',
                    prevEl: '.hero-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    renderBullet: function(index, className) {
                        return '<span class="' + className + ' swiper-pagination-bullet--svg-animation"><svg width="28" height="28" viewBox="0 0 28 28"><circle class="svg__circle" cx="14" cy="14" r="10" fill="none" stroke-width="2"></circle><circle class="svg__circle-inner" cx="14" cy="14" r="2" stroke-width="3"></circle></svg></span>';
                    },
                },
                loop: true,
                keyboard: {
                    enabled: true,
                },
            });
        } else {
            // Retry after a short delay if Swiper is not yet loaded
            setTimeout(initHeroSwiper, 100);
        }
    }

    // Smooth scroll for page scroll button
    function initPageScroll() {
        const pageScrollBtn = document.querySelector('.hero-arrow-up');
        if (pageScrollBtn) {
            pageScrollBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector('#posts') || document.querySelector('.featured-carousel-section');
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initHeroSwiper();
            initPageScroll();
        });
    } else {
        initHeroSwiper();
        initPageScroll();
    }
})();

