/**
 * 3D Carousel for Featured Stories
 * Adapted from modern 3D carousel design
 */

(function() {
    'use strict';

    const carouselContainer = document.querySelector('.carousel-container-3d');
    if (!carouselContainer) return;

    const cards = document.querySelectorAll('.card-3d');
    const dots = document.querySelectorAll('.dot-3d');
    const postTitle = document.querySelector('.post-title-3d');
    const postCategory = document.querySelector('.post-category-3d');
    const leftArrow = document.querySelector('.nav-arrow-3d.left');
    const rightArrow = document.querySelector('.nav-arrow-3d.right');

    if (!cards.length || !leftArrow || !rightArrow) return;

    let currentIndex = 0;
    let isAnimating = false;

    // Get post data from cards
    const postData = Array.from(cards).map(card => {
        const titleEl = card.querySelector('.card__title a');
        const categoryEl = card.querySelector('.card__category a');
        return {
            title: titleEl ? titleEl.textContent.trim() : '',
            category: categoryEl ? categoryEl.textContent.trim() : ''
        };
    });

    function updateCarousel(newIndex) {
        if (isAnimating) return;
        isAnimating = true;

        currentIndex = (newIndex + cards.length) % cards.length;

        cards.forEach((card, i) => {
            const offset = (i - currentIndex + cards.length) % cards.length;

            card.classList.remove(
                'center-3d',
                'left-1-3d',
                'left-2-3d',
                'right-1-3d',
                'right-2-3d',
                'hidden-3d'
            );

            if (offset === 0) {
                card.classList.add('center-3d');
            } else if (offset === 1) {
                card.classList.add('right-1-3d');
            } else if (offset === 2) {
                card.classList.add('right-2-3d');
            } else if (offset === cards.length - 1) {
                card.classList.add('left-1-3d');
            } else if (offset === cards.length - 2) {
                card.classList.add('left-2-3d');
            } else {
                card.classList.add('hidden-3d');
            }
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });

        // Update post info with fade effect
        if (postTitle && postCategory) {
            postTitle.style.opacity = '0';
            postCategory.style.opacity = '0';

            setTimeout(() => {
                if (postData[currentIndex]) {
                    postTitle.textContent = postData[currentIndex].title;
                    postCategory.textContent = postData[currentIndex].category;
                }
                postTitle.style.opacity = '1';
                postCategory.style.opacity = '1';
            }, 300);
        }

        setTimeout(() => {
            isAnimating = false;
        }, 800);
    }

    // Event listeners
    if (leftArrow) {
        leftArrow.addEventListener('click', () => {
            updateCarousel(currentIndex - 1);
        });
    }

    if (rightArrow) {
        rightArrow.addEventListener('click', () => {
            updateCarousel(currentIndex + 1);
        });
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            updateCarousel(i);
        });
    });

    cards.forEach((card, i) => {
        card.addEventListener('click', () => {
            if (!card.classList.contains('center-3d')) {
                updateCarousel(i);
            }
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (carouselContainer.contains(document.activeElement) || 
            document.activeElement === document.body) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                updateCarousel(currentIndex - 1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                updateCarousel(currentIndex + 1);
            }
        }
    });

    // Touch/swipe support
    let touchStartX = 0;
    let touchEndX = 0;

    carouselContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    carouselContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                updateCarousel(currentIndex + 1);
            } else {
                updateCarousel(currentIndex - 1);
            }
        }
    }

    // Initialize carousel
    updateCarousel(0);
})();

