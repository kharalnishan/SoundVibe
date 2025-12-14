// ===========================
// SoundVibe Music Streaming App
// Client-Side JavaScript
// ===========================

// ===========================
// DOM Elements
// ===========================

const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');
const contactForm = document.getElementById('contactForm');
const imageModal = document.getElementById('imageModal');
const modalImage = document.getElementById('modalImage');
const closeBtn = document.getElementById('closeBtn');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const galleryGrid = document.getElementById('galleryGrid');
const audioPlayer = document.getElementById('audioPlayer');

// ===========================
// NAVIGATION - Hamburger Menu
// ===========================

if (hamburger) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close menu when a link is clicked
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });
}

// ===========================
// FORM VALIDATION - Contact Form
// ===========================

if (contactForm) {
    // Validation rules
    const validationRules = {
        fullName: {
            validate: (value) => value.trim().length >= 2,
            errorMsg: 'Full name must be at least 2 characters'
        },
        email: {
            validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            errorMsg: 'Please enter a valid email address'
        },
        subject: {
            validate: (value) => value.trim().length >= 3,
            errorMsg: 'Subject must be at least 3 characters'
        },
        message: {
            validate: (value) => value.trim().length >= 10,
            errorMsg: 'Message must be at least 10 characters'
        }
    };

    // Real-time validation on input
    Object.keys(validationRules).forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('blur', () => validateField(fieldName));
            field.addEventListener('input', () => {
                if (field.classList.contains('input-error')) {
                    validateField(fieldName);
                }
            });
        }
    });

    // Validate individual field
    function validateField(fieldName) {
        const field = document.getElementById(fieldName);
        const errorEl = document.getElementById(`${fieldName}Error`);
        const rule = validationRules[fieldName];

        if (!rule) return true;

        const isValid = rule.validate(field.value);

        if (!isValid) {
            field.classList.add('input-error');
            errorEl.textContent = rule.errorMsg;
            errorEl.classList.add('show');
            return false;
        } else {
            field.classList.remove('input-error');
            errorEl.textContent = '';
            errorEl.classList.remove('show');
            return true;
        }
    }

    // Form submission
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // Validate all fields
        let isFormValid = true;
        Object.keys(validationRules).forEach(fieldName => {
            if (!validateField(fieldName)) {
                isFormValid = false;
            }
        });

        if (isFormValid) {
            // Show success message
            showFormFeedback('Thank you for your message! We\'ll get back to you soon.', 'success');

            // Reset form
            contactForm.reset();

            // Clear error states
            document.querySelectorAll('.input-error').forEach(el => {
                el.classList.remove('input-error');
            });

            // Hide success message after 5 seconds
            setTimeout(() => {
                document.getElementById('formFeedback').style.display = 'none';
            }, 5000);
        } else {
            showFormFeedback('Please correct the errors above and try again.', 'error');
        }
    });

    function showFormFeedback(message, type) {
        const feedback = document.getElementById('formFeedback');
        feedback.textContent = message;
        feedback.className = `form-feedback ${type}`;
    }
}

// ===========================
// GALLERY - Interactive Image Modal
// ===========================

let currentImageIndex = 0;
let galleryImages = [];

if (galleryGrid) {
    // Initialize gallery
    galleryImages = Array.from(galleryGrid.querySelectorAll('.gallery-thumbnail'));

    galleryImages.forEach((img, index) => {
        img.addEventListener('click', () => {
            currentImageIndex = index;
            openModal(img);
        });

        // Keyboard support - Enter to open
        img.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                currentImageIndex = index;
                openModal(img);
            }
        });
    });

    // Open modal
    function openModal(imgElement) {
        if (imageModal) {
            modalImage.src = imgElement.src;
            modalImage.alt = imgElement.alt;
            imageModal.removeAttribute('hidden');
            document.body.style.overflow = 'hidden';

            // Focus management
            closeBtn.focus();
        }
    }

    // Close modal
    function closeModal() {
        if (imageModal) {
            imageModal.setAttribute('hidden', '');
            document.body.style.overflow = 'auto';
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Keyboard navigation - Esc to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && imageModal && !imageModal.hasAttribute('hidden')) {
            closeModal();
        }
    });

    // Previous image
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            modalImage.src = galleryImages[currentImageIndex].src;
            modalImage.alt = galleryImages[currentImageIndex].alt;
        });
    }

    // Next image
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            modalImage.src = galleryImages[currentImageIndex].src;
            modalImage.alt = galleryImages[currentImageIndex].alt;
        });
    }

    // Arrow key navigation
    document.addEventListener('keydown', (e) => {
        if (imageModal && !imageModal.hasAttribute('hidden')) {
            if (e.key === 'ArrowLeft') {
                prevBtn.click();
            } else if (e.key === 'ArrowRight') {
                nextBtn.click();
            }
        }
    });

    // Click outside modal to close
    imageModal.addEventListener('click', (e) => {
        if (e.target === imageModal) {
            closeModal();
        }
    });
}

// ===========================
// INTERACTIVE BUTTONS
// ===========================

// Play buttons with real audio playback
let activePlayButton = null;
let currentTrack = '';

document.querySelectorAll('.play-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();

        if (!audioPlayer) return;

        const trackSrc = btn.dataset.audio;
        if (!trackSrc) {
            console.warn('No audio source linked to this button');
            return;
        }

        const isNewTrack = trackSrc !== currentTrack;
        if (isNewTrack) {
            currentTrack = trackSrc;
            audioPlayer.src = trackSrc;
        }

        if (audioPlayer.paused || isNewTrack) {
            audioPlayer.play().then(() => {
                setActivePlayButton(btn);
            }).catch(err => console.error('Playback failed:', err));
        } else {
            audioPlayer.pause();
        }
    });
});

if (audioPlayer) {
    audioPlayer.addEventListener('play', () => {
        if (activePlayButton) {
            activePlayButton.classList.add('is-playing');
            activePlayButton.setAttribute('aria-pressed', 'true');
            activePlayButton.textContent = '⏸';
        }
    });

    audioPlayer.addEventListener('pause', () => {
        if (activePlayButton && !audioPlayer.ended) {
            activePlayButton.classList.remove('is-playing');
            activePlayButton.setAttribute('aria-pressed', 'false');
            activePlayButton.textContent = '▶';
        }
    });

    audioPlayer.addEventListener('ended', () => {
        resetPlayButton();
        currentTrack = '';
    });
}

function setActivePlayButton(btn) {
    if (activePlayButton && activePlayButton !== btn) {
        activePlayButton.classList.remove('is-playing');
        activePlayButton.setAttribute('aria-pressed', 'false');
        activePlayButton.textContent = '▶';
    }
    activePlayButton = btn;
    activePlayButton.classList.add('is-playing');
    activePlayButton.setAttribute('aria-pressed', 'true');
    activePlayButton.textContent = '⏸';
}

function resetPlayButton() {
    if (activePlayButton) {
        activePlayButton.classList.remove('is-playing');
        activePlayButton.setAttribute('aria-pressed', 'false');
        activePlayButton.textContent = '▶';
        activePlayButton = null;
    }
}

// Follow buttons
document.querySelectorAll('.follow-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const isFollowing = btn.textContent === 'Following';
        btn.textContent = isFollowing ? 'Follow' : 'Following';
        btn.style.background = isFollowing ? '' : '#F97316';
    });
});

// Genre buttons with filtering
setupGenreFilters({
    buttonSelector: '.genre-btn[data-target="playlist"]',
    itemSelector: '.playlist-card'
});

setupGenreFilters({
    buttonSelector: '.genre-btn[data-target="artist"]',
    itemSelector: '.artist-card'
});

function setupGenreFilters({ buttonSelector, itemSelector }) {
    const buttons = document.querySelectorAll(buttonSelector);
    const items = document.querySelectorAll(itemSelector);

    if (!buttons.length || !items.length) return;

    function applyFilter(genre) {
        const normalized = genre.toLowerCase();
        items.forEach(item => {
            const tags = (item.dataset.genre || '').toLowerCase();
            const match = normalized === 'all' || tags.includes(normalized);
            item.classList.toggle('is-hidden', !match);
        });
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => {
                b.classList.remove('active');
                b.style.background = '';
                b.style.color = '';
            });

            btn.classList.add('active');
            btn.style.background = '#0EA5E9';
            btn.style.color = '#FFFFFF';

            const genre = btn.dataset.genre || 'all';
            applyFilter(genre);
        });
    });

    // Initialize with the first button's genre
    const initialGenre = buttons[0].dataset.genre || 'all';
    applyFilter(initialGenre);
}

// CTA Button
document.querySelectorAll('.cta-button').forEach(btn => {
    btn.addEventListener('click', (e) => {
        // Scroll to playlists section
        const playlistSection = document.querySelector('.playlist-grid, .trending');
        if (playlistSection) {
            playlistSection.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ===========================
// SMOOTH SCROLLING
// ===========================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ===========================
// SCROLL ANIMATIONS
// ===========================

const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'slideUp 0.6s ease-out forwards';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all feature cards, playlist cards, and artist cards
document.querySelectorAll('.feature-card, .playlist-card, .artist-card, .trending-card').forEach(el => {
    el.style.opacity = '0';
    observer.observe(el);
});

// ===========================
// LOCAL STORAGE - Favorites
// ===========================

class FavoritesManager {
    constructor() {
        this.storageKey = 'soundvibe_favorites';
        this.favorites = this.loadFavorites();
    }

    loadFavorites() {
        const stored = localStorage.getItem(this.storageKey);
        return stored ? JSON.parse(stored) : [];
    }

    saveFavorites() {
        localStorage.setItem(this.storageKey, JSON.stringify(this.favorites));
    }

    addFavorite(item) {
        if (!this.favorites.find(fav => fav.id === item.id)) {
            this.favorites.push(item);
            this.saveFavorites();
            return true;
        }
        return false;
    }

    removeFavorite(itemId) {
        this.favorites = this.favorites.filter(fav => fav.id !== itemId);
        this.saveFavorites();
    }

    isFavorite(itemId) {
        return this.favorites.some(fav => fav.id === itemId);
    }
}

const favManager = new FavoritesManager();

// ===========================
// RESPONSIVE IMAGES
// ===========================

// Lazy loading for images (if needed in future)
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ===========================
// ACCESSIBILITY ENHANCEMENTS
// ===========================

// Manage focus for modal
function manageFocus(isOpen) {
    const mainContent = document.querySelector('main');
    if (mainContent) {
        mainContent.setAttribute('inert', isOpen);
    }
}

// Skip to main content link
const skipLink = document.createElement('a');
skipLink.href = '#main';
skipLink.textContent = 'Skip to main content';
skipLink.style.position = 'absolute';
skipLink.style.top = '-40px';
skipLink.style.left = '0';
skipLink.style.background = '#0EA5E9';
skipLink.style.color = '#FFFFFF';
skipLink.style.padding = '10px';
skipLink.style.zIndex = '10000';

skipLink.addEventListener('focus', () => {
    skipLink.style.top = '0';
});

skipLink.addEventListener('blur', () => {
    skipLink.style.top = '-40px';
});

document.body.insertBefore(skipLink, document.body.firstChild);

// ===========================
// PERFORMANCE MONITORING
// ===========================

if (window.performance && window.performance.timing) {
    window.addEventListener('load', () => {
        const perfTiming = window.performance.timing;
        const pageLoadTime = perfTiming.loadEventEnd - perfTiming.navigationStart;
        console.log(`Page load time: ${pageLoadTime}ms`);
    });
}

// ===========================
// CONSENT MANAGEMENT (for cookies/analytics)
// ===========================

class ConsentManager {
    constructor() {
        this.consentGiven = localStorage.getItem('soundvibe_consent') === 'true';
    }

    requestConsent() {
        if (!this.consentGiven) {
            // Could show a consent banner here
            this.consentGiven = true;
            localStorage.setItem('soundvibe_consent', 'true');
        }
    }

    hasConsent() {
        return this.consentGiven;
    }
}

const consentManager = new ConsentManager();

// ===========================
// INITIALIZATION
// ===========================

document.addEventListener('DOMContentLoaded', () => {
    console.log('SoundVibe Music Streaming App loaded successfully');

    // Initialize any other components
    initializeApp();
});

function initializeApp() {
    // App initialization logic here
    console.log('App initialized');
}

// ===========================
// ERROR HANDLING
// ===========================

window.addEventListener('error', (event) => {
    console.error('An error occurred:', event.error);
    // Could send error to logging service
});

// Handle unhandled promise rejections
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
});

// ===========================
// DARK MODE PREFERENCE
// ===========================

if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.style.colorScheme = 'dark';
}

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    document.documentElement.style.colorScheme = e.matches ? 'dark' : 'light';
});

// ===========================
// EXPORT FUNCTIONS
// ===========================

// Make functions available globally if needed
window.SoundVibe = {
    favManager,
    consentManager,
    validateField: (fieldName) => validateField(fieldName)
};
