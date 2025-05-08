// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin panel animations
    const adminPanels = document.querySelectorAll('.admin-panel');
    
    if (typeof gsap !== 'undefined' && adminPanels.length > 0) {
        gsap.from(adminPanels, {
            duration: 0.8,
            y: 30,
            opacity: 0,
            stagger: 0.1,
            ease: 'power2.out',
            delay: 0.5
        });
    }
    
    // Add hover animation effect for admin panels
    adminPanels.forEach(panel => {
        panel.addEventListener('mouseenter', function() {
            gsap.to(this.querySelector('.panel-header i'), {
                duration: 0.3,
                rotate: 15,
                scale: 1.2
            });
        });
        
        panel.addEventListener('mouseleave', function() {
            gsap.to(this.querySelector('.panel-header i'), {
                duration: 0.3,
                rotate: 0,
                scale: 1
            });
        });
    });
    
    // Add confirmation for delete buttons
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmed = confirm('Are you sure you want to delete this item? This action cannot be undone.');
            
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
    
    // Image preview for file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewElement = document.createElement('div');
            previewElement.className = 'image-preview';
            previewElement.innerHTML = '<p>Loading preview...</p>';
            
            // Remove any existing preview
            const existingPreview = this.parentElement.querySelector('.image-preview');
            if (existingPreview) {
                existingPreview.remove();
            }
            
            // Add new preview
            this.parentElement.appendChild(previewElement);
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewElement.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});