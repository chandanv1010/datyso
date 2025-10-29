<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle text-dark mx-2" href="#" data-bs-toggle="dropdown"
                    aria-expanded="false"> {{ Auth::guard('admin')->user()->username }}</a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-white shadow border-0">
                    <li>
                        <a class="dropdown-item text-danger logout" href="{{ route('admin.logout') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                class="bi bi-box-arrow-right mt-n1 me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                                <path fill-rule="evenodd"
                                    d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                            </svg>
                            Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
