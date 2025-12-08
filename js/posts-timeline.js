/**
 * Timeline Slider for Posts
 * Modern Blog System 2025
 */

// Wait for Swiper to load
function initTimelineSlider() {
    // Check if Swiper is available
    if (typeof Swiper === 'undefined') {
        console.warn('Swiper library is not loaded, retrying...');
        setTimeout(initTimelineSlider, 100);
        return;
    }

    // Check if elements exist
    const thumbSlider = document.querySelector(".mySwiper");
    const mainSliderEl = document.querySelector(".mySwiper2");
    
    if (!thumbSlider || !mainSliderEl) {
        console.warn('Timeline slider elements not found');
        return;
    }

    try {
        // Initialize thumbnail slider first
        var sliderThumbs = new Swiper(".mySwiper", {
            direction: "horizontal",
            speed: 400,
            touchRatio: 0.2,
            slideToClickedSlide: true,
            loop: true,
            loopedSlides: 4,
            spaceBetween: 0,
            navigation: {
                nextEl: ".upk-button-next",
                prevEl: ".upk-button-prev",
            },
            breakpoints: {
                0: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 3,
                },
            }
        });

        // Initialize main slider
        var mainSlider = new Swiper(".mySwiper2", {
            parallax: true,
            effect: 'fade',
            speed: 400,
            loop: true,
            loopedSlides: 4,
            fadeEffect: {
                crossFade: true
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });

        // Link the sliders
        if (mainSlider && sliderThumbs) {
            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;
            console.log('Timeline sliders initialized and linked successfully');
        }
    } catch (error) {
        console.error('Error initializing timeline sliders:', error);
    }
}

// Initialize when DOM is ready and Swiper is loaded
document.addEventListener("DOMContentLoaded", function() {
    // Wait a bit for Swiper to be fully loaded
    setTimeout(initTimelineSlider, 200);
});

// Also try if DOM is already loaded
if (document.readyState !== 'loading') {
    setTimeout(initTimelineSlider, 200);
}

