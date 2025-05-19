// Business Module Manager
const businessManager = {
    currentBusiness: null,
    businesses: {
        bakery: {
            name: 'Bakery Shop',
            icon: 'cake',
            routes: {
                dashboard: '/bakery/dashboard',
                products: '/bakery/products',
                orders: '/bakery/orders',
                inventory: '/bakery/inventory',
                customers: '/bakery/customers',
                reports: '/bakery/reports'
            },
            permissions: [
                'bakery.view',
                'bakery.manage',
                'bakery.orders',
                'bakery.inventory'
            ]
        },
        tools: {
            name: 'Cake Tools',
            icon: 'wrench',
            routes: {
                dashboard: '/tools/dashboard',
                products: '/tools/products',
                orders: '/tools/orders',
                inventory: '/tools/inventory',
                suppliers: '/tools/suppliers',
                reports: '/tools/reports'
            },
            permissions: [
                'tools.view',
                'tools.manage',
                'tools.orders',
                'tools.inventory'
            ]
        },
        academy: {
            name: 'Academy',
            icon: 'academic-cap',
            routes: {
                dashboard: '/academy/dashboard',
                courses: '/academy/courses',
                students: '/academy/students',
                instructors: '/academy/instructors',
                lessons: '/academy/lessons',
                reports: '/academy/reports'
            },
            permissions: [
                'academy.view',
                'academy.manage',
                'academy.courses',
                'academy.students'
            ]
        }
    },

    init() {
        this.currentBusiness = document.body.dataset.business || 'bakery';
        this.setupEventListeners();
        this.updateSidebar();
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
                const data = await response.json();
                this.currentBusiness = businessId;
                this.updateSidebar();
                this.updateHeader();
                
                // Show success notification
                window.notificationSystem.show({
                    message: `Switched to ${this.businesses[businessId].name}`,
                    type: 'success'
                });

                // Redirect to business dashboard
                window.location.href = this.businesses[businessId].routes.dashboard;
            }
        } catch (error) {
            console.error('Error switching business:', error);
            window.notificationSystem.show({
                message: 'Failed to switch business. Please try again.',
                type: 'error'
            });
        }
    },

    updateSidebar() {
        const business = this.businesses[this.currentBusiness];
        const sidebar = document.getElementById('sidebar-menu');
        
        if (!sidebar || !business) return;

        // Update sidebar links based on current business
        const sidebarContent = this.generateSidebarContent(business);
        sidebar.innerHTML = sidebarContent;

        // Initialize any sidebar components (dropdowns, collapse, etc.)
        this.initializeSidebarComponents();
    },

    updateHeader() {
        const business = this.businesses[this.currentBusiness];
        const header = document.getElementById('business-header');
        
        if (!header || !business) return;

        // Update header with business info
        header.querySelector('.business-name').textContent = business.name;
        header.querySelector('.business-icon').setAttribute('data-icon', business.icon);
    },

    generateSidebarContent(business) {
        return `
            <nav class="space-y-1">
                ${Object.entries(business.routes).map(([key, route]) => `
                    <a href="${route}" class="nav-item ${window.location.pathname === route ? 'nav-item-active' : 'nav-item-inactive'}">
                        <span class="mr-3 flex-shrink-0 h-6 w-6">
                            @svg('heroicon-o-${this.getSidebarIcon(key)}', 'h-6 w-6')
                        </span>
                        <span>${this.formatRouteName(key)}</span>
                    </a>
                `).join('')}
            </nav>
        `;
    },

    getSidebarIcon(routeName) {
        const icons = {
            dashboard: 'home',
            products: 'cube',
            orders: 'shopping-cart',
            inventory: 'collection',
            customers: 'users',
            suppliers: 'truck',
            reports: 'chart-bar',
            courses: 'book-open',
            students: 'academic-cap',
            instructors: 'user-group',
            lessons: 'clipboard-list',
            settings: 'cog',
            profile: 'user-circle',
            notifications: 'bell',
            search: 'search',
            language: 'translate',
            theme: 'moon',
            logout: 'logout'
        };
        return icons[routeName] || 'document-text';
    },

    formatRouteName(name) {
        return name.charAt(0).toUpperCase() + name.slice(1);
    },

    initializeSidebarComponents() {
        // Initialize dropdowns
        document.querySelectorAll('.sidebar-dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', (e) => {
                const content = dropdown.nextElementSibling;
                content.classList.toggle('hidden');
                dropdown.querySelector('.dropdown-icon').classList.toggle('rotate-180');
            });
        });

        // Initialize collapse
        document.querySelectorAll('.sidebar-collapse').forEach(collapse => {
            collapse.addEventListener('click', () => {
                document.getElementById('sidebar').classList.toggle('collapsed');
            });
        });
    },

    setupEventListeners() {
        // Business switcher
        document.querySelectorAll('[data-business-switcher]').forEach(button => {
            button.addEventListener('click', (e) => {
                const businessId = e.currentTarget.dataset.businessId;
                this.switchBusiness(businessId);
            });
        });

        // Handle responsive sidebar
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('translate-x-0');
                sidebar.classList.toggle('-translate-x-full');
            });
        }

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && 
                sidebar &&
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) &&
                sidebar.classList.contains('translate-x-0')) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
            }
        });
    },

    hasPermission(permission) {
        const userPermissions = JSON.parse(document.body.dataset.permissions || '[]');
        return userPermissions.includes(permission);
    },

    checkPermissions() {
        const business = this.businesses[this.currentBusiness];
        
        if (!business) return false;

        // Check if user has any of the required permissions for this business
        return business.permissions.some(permission => this.hasPermission(permission));
    }
};

// Initialize business manager
document.addEventListener('DOMContentLoaded', () => {
    businessManager.init();
});

// Export for use in other files
window.businessManager = businessManager; 