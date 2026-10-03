// CafeFlow main application JavaScript

// Scroll reveal animation using Intersection Observer
// Elements with the 'scroll-reveal' class will fade in when they enter the viewport
document.addEventListener('DOMContentLoaded', () => {
    const revealElements = document.querySelectorAll('.scroll-reveal');

    if (revealElements.length === 0) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -60px 0px',
        threshold: 0.1,
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                // Stop observing once revealed for performance
                revealObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    revealElements.forEach((el) => revealObserver.observe(el));
});
