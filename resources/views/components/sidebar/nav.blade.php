@if(session('business_unit') == config('constants.business_units.bakery'))
    
    <li class="nav-item">
        <x-sidebar.nav-item 
            current-url="{{ request()->url() }}" 
            url="{{ route('dashboard') }}" 
            title="Dashboard" 
            icon="bi-grid"
        />
    </li>
    
    <!-- Additional Bakery Menu Items -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('bakery.*') && !request()->routeIs('bakery.items') ? '' : 'collapsed' }}" data-bs-target="#bakery-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-cake2"></i><span>Bakery Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="bakery-nav" class="nav-content collapse {{ request()->routeIs('bakery.*') && !request()->routeIs('bakery.items') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
            <!-- Existing bakery menu items -->
        </ul>
    </li>
    
    <!-- Bakery Inventory/Items -->
    <li class="nav-item">
        <x-sidebar.nav-item 
            current-url="{{ request()->url() }}" 
            url="{{ route('bakery.items') }}" 
            title="Bakery Items" 
            icon="bi-box-seam"
        />
    </li>
    
    <!-- Manufacturing menu -->
    <li class="nav-item">
        <a class="nav-link {{ request()->is('manufacturing/*') ? '' : 'collapsed' }}" data-bs-target="#manufacturing-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-tools"></i><span>Manufacturing</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manufacturing-nav" class="nav-content collapse {{ request()->is('manufacturing/*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
            <li>
                <a href="{{ route('manufacturing.assembly') }}" class="{{ request()->routeIs('manufacturing.assembly') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i><span>Assembly</span>
                </a>
            </li>
            <li>
                <a href="{{ route('manufacturing.process') }}" class="{{ request()->routeIs('manufacturing.process') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i><span>Process</span>
                </a>
            </li>
        </ul>
    </li>

@elseif(session('business_unit') == config('constants.business_units.tools'))
    
    <!-- Tools Business Unit Menu Items -->
    <!-- ... existing code ... -->

@endif 