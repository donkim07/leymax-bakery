<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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
    <script src="{{ asset('js/business.js') }}" defer></script>
    <script src="{{ asset('js/charts.js') }}" defer></script>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased" data-business="{{ $currentBusiness ?? 'bakery' }}" data-permissions="{{ json_encode($userPermissions ?? []) }}">
    <div id="app" class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Top Navigation -->
        @include('layouts.top-nav')

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="lg:pl-72">
            <div class="px-4 sm:px-6 lg:px-8 py-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        @yield('header')
                    </h1>
                    @yield('header_actions')
                </div>

                <!-- Page Content -->
                @yield('content')
            </div>
        </main>

        <!-- Notification System -->
        <div id="notification-container" class="fixed bottom-4 right-4 z-50"></div>

        <!-- Modal Container -->
        <div id="modal-container"></div>
    </div>

    @stack('scripts')
</body>
</html> 