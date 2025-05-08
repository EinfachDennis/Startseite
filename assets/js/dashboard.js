// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize site card animations
    const siteCards = document.querySelectorAll('.site-card');
    
    if (typeof gsap !== 'undefined' && siteCards.length > 0) {
        gsap.from(siteCards, {
            duration: 0.8,
            y: 30,
            opacity: 0,
            stagger: 0.1,
            ease: 'power2.out',
            delay: 0.5
        });
    }
    
    // Add hover animation effect
    siteCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.site-icon i, .site-icon img').style.transform = 'scale(1.2)';
            this.querySelector('.site-info h3').style.color = 'var(--accent-color-dark)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.querySelector('.site-icon i, .site-icon img').style.transform = 'scale(1)';
            this.querySelector('.site-info h3').style.color = 'var(--accent-color)';
        });
    });
});