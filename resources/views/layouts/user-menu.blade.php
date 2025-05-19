<!-- User Menu Dropdown -->
<style>
.nav-profile {
    color: #012970;
    min-width: 30px;
    padding-right: 20px;
    font-size: 16px;
}

.nav-profile img {
    max-height: 36px;
}

.nav-profile span {
    font-size: 14px;
    font-weight: 600;
}

.dropdown-menu-arrow::before {
    content: "";
    width: 13px;
    height: 13px;
    background: #fff;
    position: absolute;
    top: -7px;
    right: 20px;
    transform: rotate(45deg);
    border-top: 1px solid #eaedf1;
    border-left: 1px solid #eaedf1;
}

.dropdown-header {
    text-align: center;
    font-size: 15px;
    padding: 10px 25px;
}

.dropdown-header h6 {
    margin-bottom: 0;
    font-size: 16px;
    font-weight: 600;
    color: #444444;
}

.dropdown-header span {
    font-size: 14px;
}

.dropdown-item {
    font-size: 14px;
    padding: 10px 15px;
    transition: 0.3s;
}

.dropdown-item i {
    margin-right: 10px;
    font-size: 18px;
    line-height: 0;
}

.dropdown-item:hover {
    background-color: #f6f9ff;
}
</style>

<a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
    @if(auth()->user()->avatar)
        <img src="{{ auth()->user()->avatar }}" alt="Profile" class="rounded-circle">
    @else
        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <span class="text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
        </div>
    @endif
    <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->name }}</span>
</a>

<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
    <li class="dropdown-header">
        <h6>{{ auth()->user()->name }}</h6>
        <span>{{ auth()->user()->email }}</span>
    </li>
    
    <li><hr class="dropdown-divider"></li>

    <li>
        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
            <i class="bi bi-person"></i>
            <span>{{ __('messages.profile') }}</span>
        </a>
    </li>

    <li><hr class="dropdown-divider"></li>

    <li>
        <button type="button" class="dropdown-item d-flex align-items-center" id="theme-toggle">
            <i class="bi bi-moon"></i>
            <i class="bi bi-sun d-none"></i>
            <span>{{ __('messages.theme') }}</span>
        </button>
    </li>

    <li><hr class="dropdown-divider"></li>

    <li>
        <form method="POST" action="{{ route('language.toggle') }}" id="language-form">
            @csrf
            <button type="submit" class="dropdown-item d-flex align-items-center">
                <i class="bi bi-translate"></i>
                <span>{{ auth()->user()->language === 'en' ? __('messages.switch_to_swahili') : __('messages.switch_to_english') }}</span>
            </button>
        </form>
    </li>

    <li><hr class="dropdown-divider"></li>

    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item d-flex align-items-center">
                <i class="bi bi-box-arrow-right"></i>
                <span>{{ __('messages.logout') }}</span>
            </button>
        </form>
    </li>
</ul>

<script>
function toggleTheme() {
    const theme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    setTheme(theme);
}

function toggleLanguage() {
    const language = getStoredLanguage() === 'en' ? 'sw' : 'en';
    setLanguage(language);
}
</script> 