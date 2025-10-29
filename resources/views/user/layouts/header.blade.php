<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white py-2">
    <div class="container position-relative">
        <a class="navbar-brand py-0 h-100" href="{{ route('index') }}">
            <img src="{{ url('asset/images/Logo.png') }}" alt="Logo" width="120" class="d-inline-block">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-start bg-sidebar-light" tabindex="-1" id="offcanvasNavbar"
            aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header bg-white py-0">
                <!-- <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Offcanvas</h5> -->
                <a class="navbar-brand py-0" href="{{ route('index') }}">
                    <img src="{{ url('asset/images/Logo.png') }}" alt="Logo" width="150"
                        class="d-inline-block align-text-top">
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body bg-white">
                <ul class="navbar-nav mx-auto fw-semibold">
                    <li class="nav-item category-menu-nav mx-1">
                        <a class="nav-link" href="{{ route('product.list', ['categorySlug' => 'danh-muc']) }}">
                            <span class="py-2 fs-7 fw-bold">
                                DANH MỤC SẢN PHẨM
                            </span>
                        </a>
                        <div class="position-absolute top-100 start-0 z-3 w-100 category-menu">
                            <div class="bg-white border-0 rounded-3 shadow-lg w-100 sub-category-menu overflow-x-hidden overflow-y-scroll"
                                style="max-height: 500px">
                                <ul class="navbar-nav ms-auto row p-3">
                                    @foreach ($categories as $category)
                                        <li class="nav-item col-2 mb-4">
                                            <span class="fw-semibold mb-4 pb-2"><a
                                                    href="{{ route('product.list', ['categorySlug' => $category->slug]) }}"
                                                    class="text-decoration-none text-dark-blue">{{ $category->name }}</a></span>
                                            <ul class="list-group list-group-flush">
                                                @if (count($category->child) > 0)
                                                    @foreach ($category->child as $index => $child)
                                                        <li
                                                            class="list-group-item {{ $index + 1 != count($category->child) ? 'border-bottom' : '' }} {{ $index == 0 ? 'border-top mt-2' : '' }} px-0 py-2">
                                                            <a href="{{ route('product.list', ['categorySlug' => $child->slug]) }}"
                                                                class="text-decoration-none fw-normal text-dark-blue">{{ $child->name }}</a>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="list-group-item border-top mt-2 px-0 py-2">
                                                        <a href="{{ route('product.list', ['categorySlug' => $category->slug]) }}"
                                                            class="text-decoration-none fw-normal text-dark-blue">Xem
                                                            chi tiết</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('introduce.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                GIỚI THIỆU
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('instruction.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                CHÍNH SÁCH
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('post.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                TIN TỨC
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('solution.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                               GIẢI PHÁP
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('recruitment.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                               TUYỂN DỤNG
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('contact.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                LIÊN HỆ
                            </span>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav fw-semibold">
                    <li class="nav-item">
                        <button data-bs-toggle="modal" data-bs-target="#homeSearch" class="nav-link" aria-current="page"
                            role="button" aria-expanded="false" aria-controls="search-form">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem" fill="currentColor"
                                class="bi bi-search mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                            </svg>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem" fill="currentColor"
                                class="bi bi-person-circle mb-1" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path fill-rule="evenodd"
                                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/gio-hang" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem"
                                fill="currentColor" class="bi bi-cart mb-1" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1h1.11a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 7A.5.5 0 0 1 13 
                                        13H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zM3.14 6l1.25 5.5h8.22l1.25-5.5H3.14zM5.5 
                                        14a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm6 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem"
                                fill="currentColor" class="bi bi-globe2 mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855q-.215.403-.395.872c.705.157 1.472.257 2.282.287zM4.249 3.539q.214-.577.481-1.078a7 7 0 0 1 .597-.933A7 7 0 0 0 3.051 3.05q.544.277 1.198.49zM3.509 7.5c.036-1.07.188-2.087.436-3.008a9 9 0 0 1-1.565-.667A6.96 6.96 0 0 0 1.018 7.5zm1.4-2.741a12.3 12.3 0 0 0-.4 2.741H7.5V5.091c-.91-.03-1.783-.145-2.591-.332M8.5 5.09V7.5h2.99a12.3 12.3 0 0 0-.399-2.741c-.808.187-1.681.301-2.591.332zM4.51 8.5c.035.987.176 1.914.399 2.741A13.6 13.6 0 0 1 7.5 10.91V8.5zm3.99 0v2.409c.91.03 1.783.145 2.591.332.223-.827.364-1.754.4-2.741zm-3.282 3.696q.18.469.395.872c.552 1.035 1.218 1.65 1.887 1.855V11.91c-.81.03-1.577.13-2.282.287zm.11 2.276a7 7 0 0 1-.598-.933 9 9 0 0 1-.481-1.079 8.4 8.4 0 0 0-1.198.49 7 7 0 0 0 2.276 1.522zm-1.383-2.964A13.4 13.4 0 0 1 3.508 8.5h-2.49a6.96 6.96 0 0 0 1.362 3.675c.47-.258.995-.482 1.565-.667m6.728 2.964a7 7 0 0 0 2.275-1.521 8.4 8.4 0 0 0-1.197-.49 9 9 0 0 1-.481 1.078 7 7 0 0 1-.597.933M8.5 11.909v3.014c.67-.204 1.335-.82 1.887-1.855q.216-.403.395-.872A12.6 12.6 0 0 0 8.5 11.91zm3.555-.401c.57.185 1.095.409 1.565.667A6.96 6.96 0 0 0 14.982 8.5h-2.49a13.4 13.4 0 0 1-.437 3.008M14.982 7.5a6.96 6.96 0 0 0-1.362-3.675c-.47.258-.995.482-1.565.667.248.92.4 1.938.437 3.008zM11.27 2.461q.266.502.482 1.078a8.4 8.4 0 0 0 1.196-.49 7 7 0 0 0-2.275-1.52c.218.283.418.597.597.932m-.488 1.343a8 8 0 0 0-.395-.872C9.835 1.897 9.17 1.282 8.5 1.077V4.09c.81-.03 1.577-.13 2.282-.287z" />
                            </svg> VN
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="1.2rem"
                                        height="1.2rem" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                        role="img" class="iconify iconify--emojione mb-1"
                                        preserveAspectRatio="xMidYMid meet" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <circle cx="32" cy="32" r="30" fill="#f42f4c"></circle>
                                            <path fill="#ffe62e"
                                                d="M32 39l9.9 7l-3.7-11.4l9.8-7.4H35.8L32 16l-3.7 11.2H16l9.8 7.4L22.1 46z">
                                            </path>
                                        </g>
                                    </svg>
                                    <span class="text-dark-blue fw-semibold">VN</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="1.2rem"
                                        height="1.2rem" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                        role="img" class="iconify iconify--emojione mb-1"
                                        preserveAspectRatio="xMidYMid meet" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g fill="#2a5f9e">
                                                <path d="M22 60.3V46.5l-10.3 7.6c2.9 2.7 6.4 4.8 10.3 6.2"> </path>
                                                <path d="M42 60.3c3.9-1.4 7.4-3.5 10.3-6.2L42 46.4v13.9"> </path>
                                                <path d="M3.7 42c.3 1 .7 1.9 1.2 2.9L8.8 42H3.7"> </path>
                                                <path d="M55.2 42l3.9 2.9c.4-.9.8-1.9 1.2-2.9h-5.1"> </path>
                                            </g>
                                            <g fill="#ffffff">
                                                <path
                                                    d="M23.5 38H2.6c.3 1.4.7 2.7 1.1 4h5.1l-3.9 2.9c.8 1.7 1.7 3.2 2.8 4.7L18 42h4v2l-11.7 8.6l1.4 1.4L22 46.5v13.8c1.3.5 2.6.8 4 1.1V38h-2.5">
                                                </path>
                                                <path
                                                    d="M61.4 38H38v23.4c1.4-.3 2.7-.7 4-1.1V46.5L52.3 54c1.4-1.3 2.6-2.7 3.8-4.2L45.4 42h6.8l6.1 4.5c.3-.5.6-1.1.8-1.6L55.2 42h5.1c.4-1.3.8-2.6 1.1-4">
                                                </path>
                                            </g>
                                            <g fill="#ed4c5c">
                                                <path d="M7.7 49.6c.8 1.1 1.6 2.1 2.5 3.1L22 44.1v-2h-4L7.7 49.6">
                                                </path>
                                                <path
                                                    d="M45.5 42l10.7 7.8c.4-.5.7-1 1.1-1.5c.1-.1.1-.2.2-.2c.3-.5.7-1.1 1-1.6L52.2 42h-6.7">
                                                </path>
                                            </g>
                                            <g fill="#2a5f9e">
                                                <path d="M42 3.7v13.8l10.3-7.6C49.4 7.2 45.9 5.1 42 3.7"> </path>
                                                <path d="M22 3.7c-3.9 1.4-7.4 3.5-10.3 6.2L22 17.6V3.7"> </path>
                                                <path d="M60.3 22c-.3-1-.7-1.9-1.2-2.9L55.2 22h5.1"> </path>
                                                <path d="M8.8 22l-3.9-2.9c-.4 1-.8 1.9-1.2 2.9h5.1"> </path>
                                            </g>
                                            <g fill="#ffffff">
                                                <path
                                                    d="M40.5 26h20.8c-.3-1.4-.7-2.7-1.1-4h-5.1l3.9-2.9c-.8-1.7-1.7-3.2-2.8-4.7L46 22h-4v-2l11.7-8.6l-1.4-1.4L42 17.5V3.7c-1.3-.5-2.6-.8-4-1.1V26h2.5">
                                                </path>
                                                <path
                                                    d="M2.6 26H26V2.6c-1.4.3-2.7.7-4 1.1v13.8L11.7 10c-1.4 1.3-2.6 2.7-3.8 4.2L18.6 22h-6.8l-6.1-4.5c-.3.5-.6 1.1-.8 1.6L8.8 22H3.7c-.4 1.3-.8 2.6-1.1 4">
                                                </path>
                                            </g>
                                            <g fill="#ed4c5c">
                                                <path d="M56.3 14.4c-.8-1.1-1.6-2.1-2.5-3.1L42 19.9v2h4l10.3-7.5">
                                                </path>
                                                <path
                                                    d="M18.5 22L7.9 14.2c-.4.5-.7 1-1.1 1.5c-.1.1-.1.2-.2.2c-.3.5-.7 1.1-1 1.6l6.1 4.5h6.8">
                                                </path>
                                                <path
                                                    d="M61.4 26H38V2.6c-1.9-.4-3.9-.6-6-.6s-4.1.2-6 .6V26H2.6c-.4 1.9-.6 3.9-.6 6s.2 4.1.6 6H26v23.4c1.9.4 3.9.6 6 .6s4.1-.2 6-.6V38h23.4c.4-1.9.6-3.9.6-6s-.2-4.1-.6-6">
                                                </path>
                                            </g>
                                        </g>
                                    </svg>
                                    <span class="text-dark-blue fw-semibold">ENG</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white fixed-top py-2 hidden" id="isScroll">
    <div class="container position-relative">
        <a class="navbar-brand py-0 h-100" href="{{ route('instruction.index') }}">
            <img src="{{ url('asset/images/Logo.png') }}" alt="Logo" width="100" class="d-inline-block">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-start bg-sidebar-light" tabindex="-1" id="offcanvasNavbar"
            aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header bg-white py-0">
                <!-- <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Offcanvas</h5> -->
                <a class="navbar-brand py-0" href="{{ route('instruction.index') }}">
                    <img src="{{ url('asset/images/Logo.png') }}" alt="Logo" width="150"
                        class="d-inline-block align-text-top">
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body bg-white">
                <ul class="navbar-nav mx-auto fw-semibold">
                    <li class="nav-item category-menu-nav mx-1">
                        <a class="nav-link" href="{{ route('product.list', ['categorySlug' => 'danh-muc']) }}">
                            <span class="py-2 fs-7 fw-bold">
                                DANH MỤC SẢN PHẨM
                            </span>
                        </a>
                        <div class="position-absolute top-100 start-0 z-3 w-100 category-menu">
                            <div class="bg-white border-0 rounded-3 shadow-lg w-100 sub-category-menu overflow-x-hidden overflow-y-scroll"
                                style="max-height: 500px">
                                <ul class="navbar-nav ms-auto row p-3">
                                    @foreach ($categories as $category)
                                        <li class="nav-item col-2 mb-4">
                                            <span class="fw-semibold mb-4 pb-2"><a
                                                    href="{{ route('product.list', ['categorySlug' => $category->slug]) }}"
                                                    class="text-decoration-none text-dark-blue">{{ $category->name }}</a></span>
                                            <ul class="list-group list-group-flush">
                                                @if (count($category->child) > 0)
                                                    @foreach ($category->child as $index => $child)
                                                        <li
                                                            class="list-group-item {{ $index + 1 != count($category->child) ? 'border-bottom' : '' }} {{ $index == 0 ? 'border-top mt-2' : '' }} px-0 py-2">
                                                            <a href="{{ route('product.list', ['categorySlug' => $child->slug]) }}"
                                                                class="text-decoration-none fw-normal text-dark-blue">{{ $child->name }}</a>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="list-group-item border-top mt-2 px-0 py-2">
                                                        <a href="{{ route('product.list', ['categorySlug' => $category->slug]) }}"
                                                            class="text-decoration-none fw-normal text-dark-blue">Xem
                                                            chi tiết</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('introduce.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                GIỚI THIỆU
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('instruction.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                CHÍNH SÁCH 
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('post.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                TIN TỨC
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('solution.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                               GIẢI PHÁP
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('recruitment.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                               TUYỂN DỤNG
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link" href="{{ route('contact.index') }}">
                            <span class="py-2 fs-7 fw-bold">
                                LIÊN HỆ
                            </span>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav fw-semibold">
                    <li class="nav-item">
                        <button data-bs-toggle="modal" data-bs-target="#homeSearch" class="nav-link"
                            aria-current="page" role="button" aria-expanded="false" aria-controls="search-form">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem"
                                fill="currentColor" class="bi bi-search mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                            </svg>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem"
                                fill="currentColor" class="bi bi-person-circle mb-1" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path fill-rule="evenodd"
                                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/gio-hang" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem"
                                fill="currentColor" class="bi bi-cart mb-1" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1h1.11a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 7A.5.5 0 0 1 13 
                                        13H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zM3.14 6l1.25 5.5h8.22l1.25-5.5H3.14zM5.5 
                                        14a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm6 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2rem" height="1.2rem"
                                fill="currentColor" class="bi bi-globe2 mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855q-.215.403-.395.872c.705.157 1.472.257 2.282.287zM4.249 3.539q.214-.577.481-1.078a7 7 0 0 1 .597-.933A7 7 0 0 0 3.051 3.05q.544.277 1.198.49zM3.509 7.5c.036-1.07.188-2.087.436-3.008a9 9 0 0 1-1.565-.667A6.96 6.96 0 0 0 1.018 7.5zm1.4-2.741a12.3 12.3 0 0 0-.4 2.741H7.5V5.091c-.91-.03-1.783-.145-2.591-.332M8.5 5.09V7.5h2.99a12.3 12.3 0 0 0-.399-2.741c-.808.187-1.681.301-2.591.332zM4.51 8.5c.035.987.176 1.914.399 2.741A13.6 13.6 0 0 1 7.5 10.91V8.5zm3.99 0v2.409c.91.03 1.783.145 2.591.332.223-.827.364-1.754.4-2.741zm-3.282 3.696q.18.469.395.872c.552 1.035 1.218 1.65 1.887 1.855V11.91c-.81.03-1.577.13-2.282.287zm.11 2.276a7 7 0 0 1-.598-.933 9 9 0 0 1-.481-1.079 8.4 8.4 0 0 0-1.198.49 7 7 0 0 0 2.276 1.522zm-1.383-2.964A13.4 13.4 0 0 1 3.508 8.5h-2.49a6.96 6.96 0 0 0 1.362 3.675c.47-.258.995-.482 1.565-.667m6.728 2.964a7 7 0 0 0 2.275-1.521 8.4 8.4 0 0 0-1.197-.49 9 9 0 0 1-.481 1.078 7 7 0 0 1-.597.933M8.5 11.909v3.014c.67-.204 1.335-.82 1.887-1.855q.216-.403.395-.872A12.6 12.6 0 0 0 8.5 11.91zm3.555-.401c.57.185 1.095.409 1.565.667A6.96 6.96 0 0 0 14.982 8.5h-2.49a13.4 13.4 0 0 1-.437 3.008M14.982 7.5a6.96 6.96 0 0 0-1.362-3.675c-.47.258-.995.482-1.565.667.248.92.4 1.938.437 3.008zM11.27 2.461q.266.502.482 1.078a8.4 8.4 0 0 0 1.196-.49 7 7 0 0 0-2.275-1.52c.218.283.418.597.597.932m-.488 1.343a8 8 0 0 0-.395-.872C9.835 1.897 9.17 1.282 8.5 1.077V4.09c.81-.03 1.577-.13 2.282-.287z" />
                            </svg> VN
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="1.2rem"
                                        height="1.2rem" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                        role="img" class="iconify iconify--emojione mb-1"
                                        preserveAspectRatio="xMidYMid meet" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <circle cx="32" cy="32" r="30" fill="#f42f4c"></circle>
                                            <path fill="#ffe62e"
                                                d="M32 39l9.9 7l-3.7-11.4l9.8-7.4H35.8L32 16l-3.7 11.2H16l9.8 7.4L22.1 46z">
                                            </path>
                                        </g>
                                    </svg>
                                    <span class="text-dark-blue fw-semibold">VN</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="1.2rem"
                                        height="1.2rem" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                        role="img" class="iconify iconify--emojione mb-1"
                                        preserveAspectRatio="xMidYMid meet" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g fill="#2a5f9e">
                                                <path d="M22 60.3V46.5l-10.3 7.6c2.9 2.7 6.4 4.8 10.3 6.2"> </path>
                                                <path d="M42 60.3c3.9-1.4 7.4-3.5 10.3-6.2L42 46.4v13.9"> </path>
                                                <path d="M3.7 42c.3 1 .7 1.9 1.2 2.9L8.8 42H3.7"> </path>
                                                <path d="M55.2 42l3.9 2.9c.4-.9.8-1.9 1.2-2.9h-5.1"> </path>
                                            </g>
                                            <g fill="#ffffff">
                                                <path
                                                    d="M23.5 38H2.6c.3 1.4.7 2.7 1.1 4h5.1l-3.9 2.9c.8 1.7 1.7 3.2 2.8 4.7L18 42h4v2l-11.7 8.6l1.4 1.4L22 46.5v13.8c1.3.5 2.6.8 4 1.1V38h-2.5">
                                                </path>
                                                <path
                                                    d="M61.4 38H38v23.4c1.4-.3 2.7-.7 4-1.1V46.5L52.3 54c1.4-1.3 2.6-2.7 3.8-4.2L45.4 42h6.8l6.1 4.5c.3-.5.6-1.1.8-1.6L55.2 42h5.1c.4-1.3.8-2.6 1.1-4">
                                                </path>
                                            </g>
                                            <g fill="#ed4c5c">
                                                <path d="M7.7 49.6c.8 1.1 1.6 2.1 2.5 3.1L22 44.1v-2h-4L7.7 49.6">
                                                </path>
                                                <path
                                                    d="M45.5 42l10.7 7.8c.4-.5.7-1 1.1-1.5c.1-.1.1-.2.2-.2c.3-.5.7-1.1 1-1.6L52.2 42h-6.7">
                                                </path>
                                            </g>
                                            <g fill="#2a5f9e">
                                                <path d="M42 3.7v13.8l10.3-7.6C49.4 7.2 45.9 5.1 42 3.7"> </path>
                                                <path d="M22 3.7c-3.9 1.4-7.4 3.5-10.3 6.2L22 17.6V3.7"> </path>
                                                <path d="M60.3 22c-.3-1-.7-1.9-1.2-2.9L55.2 22h5.1"> </path>
                                                <path d="M8.8 22l-3.9-2.9c-.4 1-.8 1.9-1.2 2.9h5.1"> </path>
                                            </g>
                                            <g fill="#ffffff">
                                                <path
                                                    d="M40.5 26h20.8c-.3-1.4-.7-2.7-1.1-4h-5.1l3.9-2.9c-.8-1.7-1.7-3.2-2.8-4.7L46 22h-4v-2l11.7-8.6l-1.4-1.4L42 17.5V3.7c-1.3-.5-2.6-.8-4-1.1V26h2.5">
                                                </path>
                                                <path
                                                    d="M2.6 26H26V2.6c-1.4.3-2.7.7-4 1.1v13.8L11.7 10c-1.4 1.3-2.6 2.7-3.8 4.2L18.6 22h-6.8l-6.1-4.5c-.3.5-.6 1.1-.8 1.6L8.8 22H3.7c-.4 1.3-.8 2.6-1.1 4">
                                                </path>
                                            </g>
                                            <g fill="#ed4c5c">
                                                <path d="M56.3 14.4c-.8-1.1-1.6-2.1-2.5-3.1L42 19.9v2h4l10.3-7.5">
                                                </path>
                                                <path
                                                    d="M18.5 22L7.9 14.2c-.4.5-.7 1-1.1 1.5c-.1.1-.1.2-.2.2c-.3.5-.7 1.1-1 1.6l6.1 4.5h6.8">
                                                </path>
                                                <path
                                                    d="M61.4 26H38V2.6c-1.9-.4-3.9-.6-6-.6s-4.1.2-6 .6V26H2.6c-.4 1.9-.6 3.9-.6 6s.2 4.1.6 6H26v23.4c1.9.4 3.9.6 6 .6s4.1-.2 6-.6V38h23.4c.4-1.9.6-3.9.6-6s-.2-4.1-.6-6">
                                                </path>
                                            </g>
                                        </g>
                                    </svg>
                                    <span class="text-dark-blue fw-semibold">ENG</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div class="item-menu w-100 visually-hidden" id="categoryIsHover">
    <div class="d-flex mt-5 justify-content-center">
        <div class="mx-4">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><span class="fw-bolder mb-4">MÁY HÀN</span></li>
                <li class="nav-item"><a href="" class="nav-link">Máy hàn laser</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="homeSearch" tabindex="-1" aria-labelledby="homeSearchLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('search') }}" method="GET">
                    <div class="d-flex">
                        <input type="text" class="form-control" id="key" name="tu-khoa"
                            placeholder="Tìm kiếm...">
                        <button type="submit" class="btn btn-dark-blue ms-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                fill="currentColor" class="bi bi-search mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
