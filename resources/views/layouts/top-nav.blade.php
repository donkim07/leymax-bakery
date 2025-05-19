<!-- Top Navigation -->
<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <!-- Left side -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button id="sidebar-toggle" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    @svg('heroicon-o-bars-3', 'w-6 h-6')
                </button>

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex ml-2 md:mr-24">
                    <img src="{{ asset('images/logo.png') }}" class="h-8 mr-3" alt="Leymax Logo" />
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Leymax</span>
                </a>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-3">
                <!-- Business Switcher -->
                <div class="hidden lg:flex">
                    <button id="business-switcher" type="button" class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                        <span class="sr-only">Open business switcher</span>
                        <div class="flex items-center px-4 py-2 space-x-3">
                            @svg('heroicon-o-building-storefront', 'w-6 h-6 text-gray-300')
                            <div class="text-left">
                                <div class="text-white">{{ $currentBusiness ?? 'Bakery Shop' }}</div>
                                <div class="text-sm text-gray-400">Switch business</div>
                            </div>
                            @svg('heroicon-o-chevron-down', 'w-4 h-4 text-gray-300 ml-2')
                        </div>
                    </button>
                </div>

                <!-- Theme Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <i class="bi bi-moon"></i>
                    <i class="bi bi-sun d-none"></i>
                </button>

                <!-- Notifications -->
                <button type="button" class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                    <span class="sr-only">View notifications</span>
                    @svg('heroicon-o-bell', 'w-6 h-6')
                    <!-- Notification badge -->
                    <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">5</div>
                </button>

                <!-- Profile -->
                <div class="relative">
                    <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button">
                        <span class="sr-only">Open user menu</span>
                        <img class="w-8 h-8 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="user photo">
                    </button>
                    <!-- Dropdown menu -->
                    <div class="hidden absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:divide-gray-600" role="menu" id="user-dropdown">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900 dark:text-white" role="none">{{ auth()->user()->name }}</p>
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">{{ auth()->user()->email }}</p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Profile</a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                                        Sign out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav> 