// Theme Management
const themeManager = {
    init() {
        // Get initial theme from localStorage or user preference
        this.theme = localStorage.getItem('theme') || document.documentElement.getAttribute('data-theme') || 'light';
        this.applyTheme(this.theme);
        this.setupEventListeners();
    },

    applyTheme(theme) {
        // Update theme attributes
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);

        // Update theme toggle button icons
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const moonIcon = themeToggle.querySelector('.bi-moon');
            const sunIcon = themeToggle.querySelector('.bi-sun');
            if (theme === 'dark') {
                moonIcon?.classList.add('d-none');
                sunIcon?.classList.remove('d-none');
            } else {
                moonIcon?.classList.remove('d-none');
                sunIcon?.classList.add('d-none');
            }
        }

        // Send theme change to server
        fetch('/theme/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(response => {
            if (!response.ok) {
                console.error('Failed to update theme on server');
                if (window.notificationSystem) {
                    window.notificationSystem.show({
                        message: 'Failed to update theme. Please try again.',
                        type: 'error'
                    });
                }
            }
        }).catch(error => {
            console.error('Error updating theme:', error);
            if (window.notificationSystem) {
                window.notificationSystem.show({
                    message: 'Error updating theme. Please try again.',
                    type: 'error'
                });
            }
        });
    },

    toggleTheme() {
        const newTheme = this.theme === 'light' ? 'dark' : 'light';
        this.theme = newTheme;
        this.applyTheme(newTheme);
    },

    setupEventListeners() {
        // Theme toggle button
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }

        // Watch for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('theme')) {
                    this.applyTheme(e.matches ? 'dark' : 'light');
                }
            });
        }
    }
};

// Business Switcher
const businessSwitcher = {
    init() {
        this.setupEventListeners();
    },

    async switchBusiness(businessId) {
        try {
            const response = await fetch('/api/switch-business', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ business_id: businessId })
            });

            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error switching business:', error);
        }
    },

    setupEventListeners() {
        document.querySelectorAll('[data-business-switcher]').forEach(button => {
            button.addEventListener('click', (e) => {
                const businessId = e.currentTarget.dataset.businessId;
                this.switchBusiness(businessId);
            });
        });
    }
};

// Notification System
const notificationSystem = {
    init() {
        this.container = document.getElementById('notification-container');
        if (!this.container) {
            this.createContainer();
        }
    },

    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.className = 'fixed top-4 right-4 z-50 space-y-4';
        document.body.appendChild(this.container);
    },

    show({ message, type = 'info', duration = 5000 }) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} notification-enter`;
        notification.innerHTML = `
            <div class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${this.getIcon(type)}
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${message}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.container.appendChild(notification);

        // Setup close button
        notification.querySelector('button').addEventListener('click', () => {
            this.dismiss(notification);
        });

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => {
                this.dismiss(notification);
            }, duration);
        }
    },

    dismiss(notification) {
        notification.classList.replace('notification-enter', 'notification-exit');
        setTimeout(() => {
            notification.remove();
        }, 300);
    },

    getIcon(type) {
        const icons = {
            success: '<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            error: '<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            warning: '<svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
            info: '<svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        };
        return icons[type] || icons.info;
    }
};

// Language Switcher
const languageSwitcher = {
    init() {
        this.setupEventListeners();
    },

    switchLanguage(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        }).then(response => {
            if (response.ok) {
                // Force a full page reload to apply the new language
                window.location.href = window.location.href.split('?')[0];
            } else {
                console.error('Failed to switch language');
                if (window.notificationSystem) {
                    window.notificationSystem.show({
                        message: 'Failed to switch language. Please try again.',
                        type: 'error'
                    });
                }
            }
        }).catch(error => {
            console.error('Error switching language:', error);
            if (window.notificationSystem) {
                window.notificationSystem.show({
                    message: 'Error switching language. Please try again.',
                    type: 'error'
                });
            }
        });
    },

    setupEventListeners() {
        const languageForm = document.getElementById('language-form');
        if (languageForm) {
            languageForm.addEventListener('submit', (e) => this.switchLanguage(e));
        }
    }
};

// Initialize components when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    themeManager.init();
    businessSwitcher.init();
    notificationSystem.init();
    languageSwitcher.init();
});

// Export for use in other files
window.themeManager = themeManager;
window.notificationSystem = notificationSystem; 