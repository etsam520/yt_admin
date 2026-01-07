<header class="admin-header bg-light p-3 shadow">
    <h3 class="m-0">{{ config('app.name', 'Laravel') }} - Admin Panel</h3>

    <div class="dropdown ms-auto">
    <a href="#" class="d-flex align-items-center text-decoration-none" id="userDropdownToggle" >
    <span style="    padding: 0px 12px;
    color: white;">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
    <img src="{{ Auth::user()->profile_image ?? asset('default-user.png') }}"
         class="rounded-circle ms-2 user-icon" width="40" height="40">
</a>
        <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu">

            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</header>

<!-- Link CSS -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<!-- Link JS -->
<script src="{{ asset('js/custom.js') }}" defer></script>
