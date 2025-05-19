<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animations.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/theme.js') }}" defer></script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div id="app" class="min-h-screen">
        <!-- Theme Toggle -->
        <div class="fixed top-4 right-4">
            <button type="button" id="theme-toggle" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg">
                @svg('heroicon-o-moon', 'h-5 w-5 hidden dark:block')
                @svg('heroicon-o-sun', 'h-5 w-5 block dark:hidden')
            </button>
        </div>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Notification System -->
    <div id="notification-container" class="fixed bottom-4 right-4 z-50"></div>
</body>
</html> 