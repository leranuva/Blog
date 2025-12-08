/**
 * Modern JavaScript for Blog
 * ES6+ with Fetch API
 * 2025
 */

// Posts Grid Navigation
function initPostsGridNavigation() {
    const postsGridList = document.querySelector('.posts-grid-list');
    const prevButton = document.querySelector('.posts-grid-buttons .prevx');
    const nextButton = document.querySelector('.posts-grid-buttons .nextx');
    
    if (!postsGridList || !prevButton || !nextButton) return;
    
    // Previous button - move last item to first
    prevButton.addEventListener('click', function(e) {
        e.preventDefault();
        const firstItem = postsGridList.querySelector('li:first-child');
        if (firstItem) {
            postsGridList.insertBefore(postsGridList.querySelector('li:last-child'), firstItem);
        }
    });
    
    // Next button - move first item to last
    nextButton.addEventListener('click', function(e) {
        e.preventDefault();
        const lastItem = postsGridList.querySelector('li:last-child');
        const firstItem = postsGridList.querySelector('li:first-child');
        if (firstItem && lastItem) {
            postsGridList.insertBefore(firstItem, lastItem.nextSibling);
        }
    });
    
    // Auto-play functionality (optional)
    let autoplayInterval;
    const startAutoplay = () => {
        autoplayInterval = setInterval(() => {
            nextButton.click();
        }, 10000); // Change every 10 seconds
    };
    
    const stopAutoplay = () => {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
        }
    };
    
    // Start autoplay
    startAutoplay();
    
    // Pause on hover
    postsGridList.addEventListener('mouseenter', stopAutoplay);
    postsGridList.addEventListener('mouseleave', startAutoplay);
}

// Newsletter Form Handler
document.addEventListener('DOMContentLoaded', function() {
    // Initialize posts grid navigation
    initPostsGridNavigation();
    // Newsletter forms
    const newsletterForms = document.querySelectorAll('#newsletter-form, #newsletter-form-sidebar');
    
    newsletterForms.forEach(form => {
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                // Disable button
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showNotification(data.message, 'success');
                        this.reset();
                    } else {
                        showNotification(data.message, 'error');
                    }
                } catch (error) {
                    showNotification('An error occurred. Please try again.', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
        }
    });

    // Comment Form Handler
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Disable button
            submitButton.disabled = true;
            submitButton.innerHTML = 'Submitting...';
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    this.reset();
                    // Reload comments
                    const postId = document.querySelector('input[name="post_id"]')?.value;
                    if (postId) {
                        loadComments(postId);
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                showNotification('An error occurred. Please try again.', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    }

    // Load comments if on post page
    const commentsContainer = document.getElementById('comments-container');
    if (commentsContainer) {
        const postId = commentsContainer.dataset.postId;
        if (postId) {
            loadComments(postId);
        }
    }
});

/**
 * Load comments for a post
 */
async function loadComments(postId) {
    const commentsContainer = document.getElementById('comments-container');
    if (!commentsContainer) return;
    
    if (!postId) {
        postId = commentsContainer.dataset.postId || 
                 document.querySelector('input[name="post_id"]')?.value;
    }
    
    if (!postId) return;
    
    try {
        const response = await fetch(`/api/comments.php?post_id=${postId}`);
        const data = await response.json();
        
        if (data.success && data.comments) {
            if (data.comments.length === 0) {
                commentsContainer.innerHTML = '<p>No comments yet. Be the first to comment!</p>';
            } else {
                commentsContainer.innerHTML = data.comments.map(comment => `
                    <div class="comment-item">
                        <div class="comment-author">
                            <strong>${escapeHtml(comment.author_name)}</strong>
                            <span class="comment-date">${comment.created_at_formatted}</span>
                        </div>
                        <div class="comment-content">
                            ${escapeHtml(comment.content)}
                        </div>
                    </div>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${escapeHtml(message)}</span>
        <button class="notification-close">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        hideNotification(notification);
    });
}

/**
 * Hide notification
 */
function hideNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        notification.remove();
    }, 300);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Smooth scroll
 */
function smoothScrollTo(element) {
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                smoothScrollTo(target);
            }
        }
    });
});

