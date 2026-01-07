<aside class="sidebar">
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.home') }}" class="{{ request()->routeIs('admin.home') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i><span class="menu-text">Dashboard</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="dropdown-toggle">
                <i class="fas fa-users"></i>
                <span class="menu-text">Users</span>
                <span class="arrow-container"><i class="fas fa-chevron-right arrow"></i></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('admin.users.index') }}"
                        class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-ul"></i>
                        <span class="menu-text">User List</span>
                    </a></li>
                <li><a href="{{ route('admin.users.create') }}"
                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus"></i>
                <span class="menu-text">Add User</span>
                </a></li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="#" class="dropdown-toggle">
                <i class="fa-solid fa-circle-question"></i>
                <span class="menu-text">Question Setup</span>
                <span class="arrow-container"><i class="fas fa-chevron-right arrow"></i></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.trade-groups.index') }}"
                        class="{{ request()->routeIs('admin.trade-groups.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-shapes"></i>
                        <span class="menu-text">Trade Groups</span>
                    </a>
                </li>
                <li><a href="{{ route('admin.trades.index') }}"
                        class="{{ request()->routeIs('admin.trades.*') ? 'active' : '' }}">

                        <i class="fa-solid fa-book-journal-whills"></i>
                        <span class="menu-text">Trades</span>
                    </a>
                </li>
                <li><a href="{{ route('admin.subjects.index') }}"
                        class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-language"></i>
                        <span class="menu-text">Subjects</span>

                    </a></li>
                <li><a href="{{ route('admin.chapters.index') }}"
                        class="{{ request()->routeIs('admin.chapters.*') ? 'active' : '' }}">
                        <i class="fa-brands fa-readme"></i>
                        <span class="menu-text">Chapters</span>
                    </a></li>
                <li><a href="{{ route('admin.topics.index') }}"
                        class="{{ request()->routeIs('admin.topics.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book-open-reader"></i>
                        <span class="menu-text">Topics</span>
                    </a></li>

            </ul>
        </li>
        <li class="menu-item">
            <a href="#" class="dropdown-toggle">
                <i class="fas fa-book"></i>
                <span class="menu-text">Online Examination</span>
                <span class="arrow-container"><i class="fas fa-chevron-right arrow"></i></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('admin.tests.index') }}" class="{{ request('admin.tests.index.*')?'active':'' }}">Online Exam</a></li>
                <li><a href="#">Online Test</a></li>
                <li><a href="{{ route('admin.question-bank.index') }}"
                        class="{{ request()->routeIs('admin.question-bank.*') ? 'active' : '' }}">Question Bank</a></li>
            </ul>
        </li>


        <li>
            <a href="#">
                <i class="fas fa-cog"></i>
                <span class="menu-text">Settings</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/') }}">
                <i class="fas fa-arrow-left"></i>
                <span class="menu-text">Back to Site</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Updated CSS -->
<style>
    /* Sidebar Base Styling */
    .sidebar {

        background: #2C3E50;
        color: white;

    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu li {}

    .sidebar-menu a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 16px;
        transition: 0.3s;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: #34495E;
        border-radius: 5px;
    }

    /* Ensures text is always left-aligned */
    .menu-text {
        flex-grow: 1;
        margin-left: 10px;
    }

    /* Arrow Placement */
    .arrow-container {
        display: flex;
        align-items: center;
    }

    /* Dropdown Menu Styling */
    .dropdown-menu {
        display: none;
        list-style: none;
        padding-left: 30px;
        margin-top: 5px;
    }

    .dropdown-menu li {
        padding: 8px 0;
    }

    .dropdown-menu a {
        font-size: 14px;
    }

    /* Rotate Arrow */
    .menu-item.active .arrow {
        transform: rotate(90deg);
        transition: 0.3s ease-in-out;
    }
</style>

<!-- Updated JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const arrows = document.querySelectorAll(".arrow-container");

        arrows.forEach(arrow => {
            arrow.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevents parent link from triggering

                let parent = this.closest(".menu-item");
                let dropdownMenu = parent.querySelector(".dropdown-menu");

                // Toggle Active Class
                parent.classList.toggle("active");

                // Show/Hide Dropdown Menu
                if (dropdownMenu.style.display === "block") {
                    dropdownMenu.style.display = "none";
                } else {
                    dropdownMenu.style.display = "block";
                }
            });
        });
    });
</script>
