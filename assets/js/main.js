// Main JavaScript File

// Wait for document to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initAnimations();
    
    // Initialize interactive elements
    initInteractiveElements();
});

// Initialize GSAP animations
function initAnimations() {
    // Check if GSAP is available
    if (typeof gsap !== 'undefined') {
        // Header animation
        gsap.from('header', {
            duration: 1,
            y: -50,
            opacity: 0,
            ease: 'power3.out'
        });
        
        // Spezielle Animation für Login-Container (falls vorhanden)
        if (document.querySelector('.login-container')) {
            // Keine Transform-Animation, nur Fade-In für das Login-Formular
            gsap.from('.login-container', {
                duration: 1.2,
                opacity: 0,
                ease: 'power2.out',
                delay: 0.2
            });
        } else {
            // Main content animation für alle anderen Seiten
            gsap.from('main > *', {
                duration: 0.8,
                y: 20,
                opacity: 0,
                stagger: 0.2,
                ease: 'power2.out',
                delay: 0.3
            });
        }
        
        // Footer animation
        gsap.from('footer', {
            duration: 0.8,
            y: 20,
            opacity: 0,
            ease: 'power2.out',
            delay: 1
        });
    }
}

// Initialize interactive elements
function initInteractiveElements() {
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                gsap.to(alert, {
                    duration: 0.5,
                    opacity: 0,
                    y: -20,
                    onComplete: function() {
                        alert.style.display = 'none';
                    }
                });
            });
        }, 5000);
    }
}