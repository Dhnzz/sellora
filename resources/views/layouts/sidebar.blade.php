<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('owner.dashboard') }}" class="text-nowrap">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('assets/front/img/favicon.png') }}" width="50">
                    <h4 class="mb-0 px-2 fw-bolder">OWNER PANEL</h4>
                </div>
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar border-top">
            <ul id="sidebarnav">

                @switch($data['role'])
                    @case('owner')
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ Route::currentRouteName() == $data['role'] . '.dashboard' ? 'active' : '' }}"
                                href="{{ route($data['role'] . '.dashboard') }}" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Manajemen Pengguna</span>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.user_management.admin.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.user_management.admin.*') ? 'active' : '' }}">
                                <i class="ti ti-user-cog fs-5 me-2"></i>
                                <span class="hide-menu">Admin</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.user_management.warehouse_manager.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.user_management.warehouse_manager.*') ? 'active' : '' }}">
                                <i class="ti ti-building-warehouse fs-5 me-2"></i>
                                <span class="hide-menu">Warehouse Manager</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.user_management.sales.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.user_management.sales.*') ? 'active' : '' }}">
                                <i class="ti ti-user-dollar fs-5 me-2"></i>
                                <span class="hide-menu">Sales</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.user_management.customer.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.user_management.customer.*') ? 'active' : '' }}">
                                <i class="ti ti-user-check fs-5 me-2"></i>
                                <span class="hide-menu">Customer</span>
                            </a>
                        </li>
                        {{-- <li class="sidebar-item">
                            <a href="{{ route('owner.user_management.role.index') }}" class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.user_management.role.*') ? 'active' : '' }}">
                                <i class="ti ti-shield-lock fs-5 me-2"></i>
                                <span class="hide-menu">Role &amp; Permission</span>
                            </a>
                        </li> --}}
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Master Data</span>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.master_data.product_unit.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.master_data.product_unit.*') ? 'active' : '' }}">
                                <i class="ti ti-ruler fs-5 me-2"></i>
                                <span class="hide-menu">Unit Produk</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.master_data.product_brand.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.master_data.product_brand.*') ? 'active' : '' }}">
                                <i class="ti ti-brand-apple fs-5 me-2"></i>
                                <span class="hide-menu">Brand Produk</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('owner.master_data.product.index') }}"
                                class="sidebar-link d-flex align-items-center {{ Request::routeIs('owner.master_data.product.*') ? 'active' : '' }}">
                                <i class="ti ti-package fs-5 me-2"></i>
                                <span class="hide-menu">Produk</span>
                            </a>
                        </li>
                    @break

                    @case('admin')
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Master Data</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link @if ($active == 'category') active @endif" href="#"
                                aria-expanded="false">
                                <span>
                                    <i class="ti ti-category-2"></i>
                                </span>
                                <span class="hide-menu">Category</span>
                            </a>
                        </li>
                    @break

                    @case('warehouse')
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link " href="{{ url('/dashboard') }}" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Master Data</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link @if ($active == 'category') active @endif" href="#"
                                aria-expanded="false">
                                <span>
                                    <i class="ti ti-category-2"></i>
                                </span>
                                <span class="hide-menu">Category</span>
                            </a>
                        </li>
                    @break

                    @case('sales')
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link " href="{{ url('/dashboard') }}" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Master Data</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link @if ($active == 'category') active @endif" href="#"
                                aria-expanded="false">
                                <span>
                                    <i class="ti ti-category-2"></i>
                                </span>
                                <span class="hide-menu">Category</span>
                            </a>
                        </li>
                    @break

                    @case('customer')
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link " href="{{ url('/dashboard') }}" aria-expanded="false">
                                <span>
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Master Data</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link @if ($active == 'category') active @endif" href="#"
                                aria-expanded="false">
                                <span>
                                    <i class="ti ti-category-2"></i>
                                </span>
                                <span class="hide-menu">Category</span>
                            </a>
                        </li>
                    @break

                    @default
                @endswitch

                {{-- <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'catalog') active @endif" href="{{ route('admin.catalog') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-brand-appgallery"></i>
                        </span>
                        <span class="hide-menu">Catalog</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'gallery') active @endif" href="{{ route('admin.gallery') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-cards"></i>
                        </span>
                        <span class="hide-menu">Gallery</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'video') active @endif" href="{{ route('admin.video') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-brand-youtube"></i>
                        </span>
                        <span class="hide-menu">Video</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'information') active @endif" href="{{ route('admin.information') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-news"></i>
                        </span>
                        <span class="hide-menu">Information</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Settings</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'about-us') active @endif" href="{{ route('admin.about-us') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-info-hexagon"></i>
                        </span>
                        <span class="hide-menu">About Us</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'main-slider') active @endif" href="{{ route('admin.main-slider') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-slideshow"></i>
                        </span>
                        <span class="hide-menu">Main Slider</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'review-slider') active @endif" href="{{ route('admin.review-slider') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-slideshow"></i>
                        </span>
                        <span class="hide-menu">Review Slider</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'account-setting') active @endif" href="{{ route('admin.account-setting') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-user-cog"></i>
                        </span>
                        <span class="hide-menu">Account Setting</span>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
