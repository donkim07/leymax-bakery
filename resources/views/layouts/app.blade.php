<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <!-- Apply sidebar state before page renders to prevent flashing -->
    <script>
        // Theme handling
        let theme = localStorage.getItem('theme') || '{{ auth()->user()->theme ?? "light" }}';
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme);
        
        // Apply sidebar state immediately before DOM renders
        const sidebarState = localStorage.getItem('sidebar-collapsed');
        if (sidebarState === 'true') {
            document.documentElement.classList.add('toggle-sidebar');
        }
    </script>

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet">

    <!-- Theme and Language Management Script -->
    <script src="{{ asset('js/theme.js') }}" defer></script>
    
    @stack('styles')
</head>
<body>
<script>
    // Apply sidebar toggle immediately to prevent flash 
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        document.body.classList.add('toggle-sidebar');
    }
</script>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="">
                <span class="d-none d-lg-block">{{ config('app.name') }}</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>

        <div class="search-bar">
            <form class="search-form d-flex align-items-center" method="POST" action="#">
                <input type="text" name="query" placeholder="Search" title="Enter search keyword">
                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle" href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li>

                <!-- Notifications Nav -->
                <li class="nav-item dropdown">
                    @include('layouts.notifications')
                </li>

                <!-- Profile Nav -->
                <li class="nav-item dropdown pe-3">
                    @include('layouts.user-menu')
                </li>
            </ul>
        </nav>
    </header>

    <!-- ======= Sidebar ======= -->
    @include('layouts.sidebar')

   

    <!-- ======= Main ======= -->
    <main id="main" class="main min-vh-100">
        <div class="pagetitle">
            <h1>@yield('title', 'Dashboard')</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            @yield('content')
        </section>
    </main>

    <!-- Toast Notification Component -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="mainToast" class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="mainToastMessage">
                    Message goes here
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; {{ date('Y') }} <strong><span>{{ config('app.name') }}</span></strong>. All Rights Reserved
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')

    @if(Auth::check())
    <script>
        // Global app object to store business data
        window.appData = window.appData || {
            businessData: {},
            initChoices: function() {
                if (window.Choices) {
                    document.querySelectorAll('.searchable:not(.choices__input)').forEach(function(select) {
                        if (!select.classList.contains('choices-initialized')) {
                            new Choices(select, {
                                searchEnabled: true,
                                shouldSort: false,
                                removeItemButton: true,
                                placeholder: true,
                                placeholderValue: select.getAttribute('placeholder') || 'Type to search...',
                                classNames: {
                                    containerOuter: 'choices search-enabled'
                                }
                            });
                            select.classList.add('choices-initialized');
                        }
                    });
                }
            },
            prefetchData: function() {
                // Only fetch if user is logged in
                if (!document.querySelector('meta[name="csrf-token"]')) {
                    return;
                }
                
                // Fetch business data
                fetch('/api/user/business-data', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    window.appData.businessData = data;
                    console.log('Business data prefetched successfully');
                })
                .catch(error => {
                    console.error('Error prefetching business data:', error);
                });
            }
        };

        // Initialize on document ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize choices
            window.appData.initChoices();
            
            // Prefetch business data
            window.appData.prefetchData();
            
            // Re-initialize choices when any modal is shown
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    setTimeout(function() {
                        window.appData.initChoices();
                    }, 100);
                });
            });
            
            // Setup real-time name uniqueness checking for common forms
            setupRealTimeNameCheck();
        });
        
        // Setup real-time name uniqueness checking
        function setupRealTimeNameCheck() {
            // For assembled items
            const assembledItemNameInputs = document.querySelectorAll('input[name="name"][data-unique-check="assembled-item"]');
            assembledItemNameInputs.forEach(input => {
                input.addEventListener('input', function() {
                    checkNameUniqueness(
                        this, 
                        '/api/check-assembled-item-name-unique', 
                        this.closest('form').querySelector('button[type="submit"]')
                    );
                });
            });
            
            // For products
            const productNameInputs = document.querySelectorAll('input[name="name"][data-unique-check="product"]');
            productNameInputs.forEach(input => {
                input.addEventListener('input', function() {
                    checkNameUniqueness(
                        this, 
                        '/api/check-product-name-unique', 
                        this.closest('form').querySelector('button[type="submit"]')
                    );
                });
            });
        }
        
        // Check name uniqueness via AJAX
        function checkNameUniqueness(input, url, submitBtn) {
            const name = input.value.trim();
            const id = input.dataset.id || '';
            
            if (!name) {
                clearFeedback(input);
                return;
            }
            
            fetch(`${url}?name=${encodeURIComponent(name)}&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.unique) {
                        showNameError(input, 'This name is already in use. Please choose another.', submitBtn);
                    } else {
                        clearFeedback(input);
                    }
                })
                .catch(error => {
                    console.error('Error checking name uniqueness:', error);
                });
        }
        
        // Show name error feedback
        function showNameError(input, message, submitBtn) {
            clearFeedback(input);
            
            input.classList.add('is-invalid');
            const feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'invalid-feedback name-unique-feedback';
            feedbackDiv.innerText = message;
            
            input.parentNode.appendChild(feedbackDiv);
            
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        }
        
        // Clear name feedback
        function clearFeedback(input) {
            input.classList.remove('is-invalid');
            
            const existingFeedback = input.parentNode.querySelector('.name-unique-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            const submitBtn = input.closest('form').querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }
    </script>
    @endif
</body>
</html>

