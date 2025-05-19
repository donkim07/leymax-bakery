<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Business Info -->
        <li class="nav-heading">
            <div class="d-flex align-items-center">
                <i class="bi bi-shop me-2"></i>
                <span>{{ $currentBusiness ?? 'Bakery Shop' }}</span>
            </div>
            <p class="text-muted small mt-1 mb-3">Manage your {{ strtolower($currentBusiness ?? 'bakery') }} operations and track performance.</p>
        </li>

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Navigation Items -->
        @foreach($sidebarItems as $item)
            @if(isset($item['items']))
                <li class="nav-item">
                    <a class="nav-link {{ in_array(request()->route()->getName(), collect($item['items'])->pluck('route')->toArray()) ? '' : 'collapsed' }}" 
                       data-bs-target="#{{ Str::slug($item['title']) }}-nav" 
                       data-bs-toggle="collapse" 
                       href="#">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['title'] }}</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="{{ Str::slug($item['title']) }}-nav" 
                        class="nav-content collapse {{ in_array(request()->route()->getName(), collect($item['items'])->pluck('route')->toArray()) ? 'show' : '' }}" 
                        data-bs-parent="#sidebar-nav">
                        @foreach($item['items'] as $subItem)
                            <li>
                                <a href="{{ route($subItem['route']) }}" 
                                   class="{{ request()->routeIs($subItem['route']) ? 'active' : '' }}">
                                    <i class="bi {{ $subItem['icon'] }} bi-circle"></i>
                                    <span>{{ $subItem['title'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                @unless($item['title'] === 'Dashboard')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs($item['route']) ? '' : 'collapsed' }}" 
                       href="{{ route($item['route']) }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>
                </li>
                @endunless
            @endif
        @endforeach
    </ul>
</aside>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle sidebar menu active state and smooth transitions
    const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    sidebarLinks.forEach(link => {
        if (!link.classList.contains('collapsed')) {
            link.closest('.nav-item').querySelector('.nav-content')?.classList.add('show');
        }
    });
});
</script>
@endpush 