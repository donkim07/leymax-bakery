<!-- Notifications Dropdown -->
<style>
.header-nav .nav-icon {
    font-size: 22px;
    color: #012970;
    margin-right: 25px;
    position: relative;
}

.header-nav .badge-number {
    position: absolute;
    inset: -2px -5px auto auto;
    font-weight: normal;
    font-size: 12px;
    padding: 3px 6px;
}

.header-nav .notifications {
    min-width: 300px;
}

.header-nav .notifications .notification-item {
    display: flex;
    align-items: center;
    padding: 15px 10px;
    transition: 0.3s;
    cursor: pointer;
}

.header-nav .notifications .notification-item:hover {
    background-color: #f6f9ff;
}

.header-nav .notifications .notification-item i {
    margin: 0 20px 0 10px;
    font-size: 24px;
}

.header-nav .notifications .notification-item h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
}

.header-nav .notifications .notification-item p {
    font-size: 13px;
    margin-bottom: 3px;
    color: #919191;
}

.header-nav .notifications .notification-item:last-child {
    border-bottom: 1px solid #eee;
}

.header-nav .notifications .dropdown-footer {
    text-align: center;
    font-size: 15px;
    padding: 10px 25px;
}

.header-nav .notifications .dropdown-footer a {
    color: #444444;
    text-decoration: underline;
}

.header-nav .notifications .dropdown-footer a:hover {
    color: #4154f1;
    text-decoration: none;
}

.header-nav .notifications .dropdown-header {
    text-align: left;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.header-nav .notifications .badge {
    font-size: 12px;
}
</style>

<div x-data="{ open: false, notifications: [] }" 
    x-init="
        Echo.private('App.Models.User.' + {{ auth()->id() }})
            .notification((notification) => {
                notifications.unshift(notification);
                $dispatch('notification-received');
            });
    "
    @notification-received.window="$refs.badge.classList.add('animate-bounce')"
    @click.away="open = false">
    
    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span x-show="notifications.length > 0" x-ref="badge" class="badge bg-primary badge-number" x-text="notifications.length"></span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
        <li class="dropdown-header d-flex align-items-center">
            You have <span class="mx-1" x-text="notifications.length"></span> new notifications
            <a href="#" @click.prevent="notifications = []" class="badge rounded-pill bg-primary p-2 ms-auto">Clear All</a>
        </li>

        <template x-if="notifications.length > 0">
            <template x-for="notification in notifications" :key="notification.id">
                <div>
                    <li><hr class="dropdown-divider"></li>
                    <li class="notification-item" @click="window.location.href = notification.action_url">
                        <template x-if="notification.type.includes('approval')">
                            <i class="bi bi-info-circle text-primary"></i>
                        </template>
                        <template x-if="notification.type.includes('alert')">
                            <i class="bi bi-exclamation-circle text-warning"></i>
                        </template>
                        <template x-if="notification.type.includes('success')">
                            <i class="bi bi-check-circle text-success"></i>
                        </template>
                        <div>
                            <h4 x-text="notification.title"></h4>
                            <p x-text="notification.message"></p>
                            <p class="text-muted small" x-text="notification.time"></p>
                        </div>
                    </li>
                </div>
            </template>
        </template>

        <template x-if="notifications.length === 0">
            <div>
                <li><hr class="dropdown-divider"></li>
                <li class="notification-item">
                    <i class="bi bi-envelope text-muted"></i>
                    <div>
                        <h4>No New Notifications</h4>
                        <p>You're all caught up! Check back later for new notifications.</p>
                    </div>
                </li>
            </div>
        </template>

        <li><hr class="dropdown-divider"></li>
        
        <li class="dropdown-footer">
            <a href="#">Show all notifications</a>
        </li>
    </ul>
</div> 