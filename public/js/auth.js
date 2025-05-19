document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle based on system preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    }

    // Listen for system dark mode changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (e.matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });

    // Password visibility toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    const errorDiv = field.parentElement.querySelector('.error-text') || 
                                   document.createElement('div');
                    errorDiv.className = 'error-text';
                    errorDiv.textContent = 'This field is required';
                    if (!field.parentElement.querySelector('.error-text')) {
                        field.parentElement.appendChild(errorDiv);
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // CSRF Token handling for AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    document.addEventListener('ajax:beforeSend', function(e) {
        e.detail.xhr.setRequestHeader('X-CSRF-Token', token);
    });
}); 