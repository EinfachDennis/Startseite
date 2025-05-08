/**
 * animations.js - Advanced animations and visual effects for the portal
 */

// Initialize animations when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize particle background if available
    initParticleBackground();
    
    // Initialize hover effects
    initHoverEffects();
    
    // Initialize scroll animations
    initScrollAnimations();
    
    // Initialize animated backgrounds
    initAnimatedBackgrounds();
});

/**
 * Initialize particle background animation
 */
function initParticleBackground() {
    // Only initialize if the element exists
    if (!document.querySelector('.background-overlay')) return;
    
    // Create canvas element for particles
    const particleContainer = document.createElement('div');
    particleContainer.className = 'particle-container';
    particleContainer.style.position = 'fixed';
    particleContainer.style.top = '0';
    particleContainer.style.left = '0';
    particleContainer.style.width = '100%';
    particleContainer.style.height = '100%';
    particleContainer.style.pointerEvents = 'none';
    particleContainer.style.zIndex = '0';
    
    // Insert after background overlay
    const backgroundOverlay = document.querySelector('.background-overlay');
    backgroundOverlay.parentNode.insertBefore(particleContainer, backgroundOverlay.nextSibling);
    
    // Create particles
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        createParticle(particleContainer);
    }
}

/**
 * Create a single animated particle
 */
function createParticle(container) {
    const particle = document.createElement('div');
    
    // Apply base styles
    particle.style.position = 'absolute';
    particle.style.borderRadius = '50%';
    particle.style.pointerEvents = 'none';
    
    // Randomize particle properties
    const size = Math.random() * 5 + 1;
    const posX = Math.random() * 100;
    const posY = Math.random() * 100;
    const opacity = Math.random() * 0.5 + 0.1;
    const duration = Math.random() * 20 + 10;
    const delay = Math.random() * 5;
    
    // Choose between Twitch purple and orange-red colors
    const color = Math.random() > 0.5 ? 'rgba(100, 65, 165, 0.7)' : 'rgba(255, 69, 0, 0.7)';
    
    // Apply randomized styles
    particle.style.width = `${size}px`;
    particle.style.height = `${size}px`;
    particle.style.top = `${posY}%`;
    particle.style.left = `${posX}%`;
    particle.style.background = color;
    particle.style.opacity = opacity;
    particle.style.animation = `floatParticle ${duration}s infinite linear ${delay}s`;
    
    // Add to container
    container.appendChild(particle);
    
    // Define the animation
    if (!document.getElementById('particle-animations')) {
        const style = document.createElement('style');
        style.id = 'particle-animations';
        style.textContent = `
            @keyframes floatParticle {
                0% {
                    transform: translate(0, 0) rotate(0deg);
                }
                25% {
                    transform: translate(20px, -20px) rotate(90deg);
                }
                50% {
                    transform: translate(0, -40px) rotate(180deg);
                }
                75% {
                    transform: translate(-20px, -20px) rotate(270deg);
                }
                100% {
                    transform: translate(0, 0) rotate(360deg);
                }
            }
        `;
        document.head.appendChild(style);
    }
}

/**
 * Initialize enhanced hover effects
 */
function initHoverEffects() {
    // Enhance button hover effects
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            // Create glow effect
            if (!this.querySelector('.btn-glow')) {
                const glow = document.createElement('div');
                glow.className = 'btn-glow';
                glow.style.position = 'absolute';
                glow.style.top = '0';
                glow.style.left = '0';
                glow.style.width = '100%';
                glow.style.height = '100%';
                glow.style.borderRadius = 'inherit';
                glow.style.boxShadow = '0 0 15px var(--accent-color)';
                glow.style.opacity = '0';
                glow.style.transition = 'opacity 0.3s ease';
                glow.style.zIndex = '-1';
                glow.style.pointerEvents = 'none';
                
                // Make button position relative if not already
                if (getComputedStyle(this).position === 'static') {
                    this.style.position = 'relative';
                }
                
                this.appendChild(glow);
                
                // Animate glow in
                setTimeout(() => {
                    glow.style.opacity = '1';
                }, 0);
            }
        });
        
        button.addEventListener('mouseleave', function() {
            const glow = this.querySelector('.btn-glow');
            if (glow) {
                glow.style.opacity = '0';
                
                // Remove glow element after animation completes
                setTimeout(() => {
                    glow.remove();
                }, 300);
            }
        });
    });
    
    // Enhanced card hover effects
    const cards = document.querySelectorAll('.site-card, .admin-panel');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (typeof gsap !== 'undefined') {
                gsap.to(this, {
                    y: -10,
                    scale: 1.02,
                    boxShadow: '0 15px 30px rgba(0, 0, 0, 0.5)',
                    duration: 0.3,
                    ease: 'power2.out'
                });
            }
        });
        
        card.addEventListener('mouseleave', function() {
            if (typeof gsap !== 'undefined') {
                gsap.to(this, {
                    y: 0,
                    scale: 1,
                    boxShadow: '0 10px 20px rgba(0, 0, 0, 0.3)',
                    duration: 0.3,
                    ease: 'power2.out'
                });
            }
        });
    });
}

/**
 * Initialize scroll-based animations
 */
function initScrollAnimations() {
    // Only initialize if GSAP is available
    if (typeof gsap === 'undefined') return;
    
    // Get all animatable elements
    const animElements = document.querySelectorAll('.admin-card, .dashboard-welcome, .sites-container, .admin-panels');
    
    // If no elements to animate, return
    if (animElements.length === 0) return;
    
    // Create scroll trigger if available
    if (typeof ScrollTrigger !== 'undefined') {
        animElements.forEach(elem => {
            gsap.from(elem, {
                y: 50,
                opacity: 0,
                duration: 0.8,
                scrollTrigger: {
                    trigger: elem,
                    start: 'top bottom-=100',
                    end: 'top center',
                    toggleActions: 'play none none reverse'
                }
            });
        });
    } else {
        // Fallback for when ScrollTrigger is not available
        let scrollAnimated = false;
        
        window.addEventListener('scroll', function() {
            if (!scrollAnimated && window.scrollY > 100) {
                scrollAnimated = true;
                
                animElements.forEach((elem, index) => {
                    gsap.from(elem, {
                        y: 50,
                        opacity: 0,
                        duration: 0.8,
                        delay: index * 0.1
                    });
                });
            }
        });
    }
}

/**
 * Initialize animated backgrounds
 */
function initAnimatedBackgrounds() {
    // Create gradient animation for background
    if (!document.querySelector('.background-overlay')) return;
    
    const overlay = document.querySelector('.background-overlay');
    
    // Add animation classes
    overlay.classList.add('animated-bg');
    
    // Create animation CSS if not already present
    if (!document.getElementById('animated-bg-styles')) {
        const style = document.createElement('style');
        style.id = 'animated-bg-styles';
        style.textContent = `
            .animated-bg {
                animation: gradientShift 15s ease infinite;
                background-size: 400% 400%;
            }
            
            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%;
                    background: linear-gradient(
                        135deg, 
                        rgba(24, 24, 27, 0.7) 0%, 
                        rgba(36, 36, 53, 0.8) 50%, 
                        rgba(100, 65, 165, 0.6) 100%
                    );
                }
                50% {
                    background-position: 100% 50%;
                    background: linear-gradient(
                        135deg, 
                        rgba(36, 36, 53, 0.8) 0%, 
                        rgba(100, 65, 165, 0.6) 50%, 
                        rgba(255, 69, 0, 0.4) 100%
                    );
                }
                100% {
                    background-position: 0% 50%;
                    background: linear-gradient(
                        135deg, 
                        rgba(24, 24, 27, 0.7) 0%, 
                        rgba(36, 36, 53, 0.8) 50%, 
                        rgba(100, 65, 165, 0.6) 100%
                    );
                }
            }
        `;
        document.head.appendChild(style);
    }
}