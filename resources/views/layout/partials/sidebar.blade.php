<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        {{-- <img src="{{ asset('app-src/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
        <span class="brand-text font-weight-light">Booking Tiket SpeedBoat</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 d-flex">
            <div class="image">
                <img src="{{ asset('app-src/img/default_profile.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="javascript:void();" id="user-link" class="d-block">
                    <span>{{ auth()->user()?->name }}</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">MENU</li>
                @auth

                    <li class="nav-item">
                        <a href="{{ url('dashboard/index') }}" class="nav-link {{ request()->is('dashboard/index') ? 'active' : null }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    @if (preg_match('[admin|owner|driver]', auth()->user()->role->name))
                        <li class="nav-item">
                            <a href="{{ url('dashboard/speedboats/index') }}"
                                class="nav-link  {{ request()->is('dashboard/speedboats/index') ? 'active' : null }}">
                                <i class="nav-icon fas fa-ship"></i>
                                <p>SpeedBoat</p>
                            </a>
                        </li>
                    @endif

                    @if (preg_match('[admin|owner|driver]', auth()->user()->role->name))
                        <li class="nav-item">
                            <a href="{{ url('dashboard/streets/index') }}"
                                class="nav-link {{ request()->is('dashboard/streets/index') ? 'active' : null }}">
                                <i class="nav-icon fas fa-route"></i>
                                <p>Rute</p>
                            </a>
                        </li>
                    @endif

                    @if (preg_match('[admin|owner]', auth()->user()->role->name))
                        <li class="nav-item">
                            <a href="{{ url('dashboard/payment-methods/index') }}"
                                class="nav-link {{ request()->is('dashboard/payment-methods/index') ? 'active' : null }}">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>Metode Pembayaran</p>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ url('dashboard/tickets/index') }}"
                            class="nav-link {{ request()->is('dashboard/tickets/index') ? 'active' : null }}">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>Daftar Tiket</p>
                        </a>
                    </li>

                    @if (preg_match('[customer]', auth()->user()->role->name))
                        <li class="nav-item">
                            <a href="{{ url('dashboard/orders/create') }}"
                                class="nav-link {{ request()->is('dashboard/orders/create') ? 'active' : null }}">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>Buat Pesanan</p>
                            </a>
                        </li>
                    @endif

                    @if (!preg_match('[driver|admin]', auth()->user()->role->name))
                        <li class="nav-item {{ request()->is('dashboard/*history*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('dashboard/*history*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list-ul"></i>
                                <p>
                                    Riwayat
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('dashboard/orders/history/index') }}"
                                        class="nav-link {{ request()->is('dashboard/orders/history/index') ? 'active' : null }}">
                                        <i
                                            class="far fa-circle nav-icon
                                    {{ request()->is('dashboard/orders/history/index') ? 'text-danger' : null }}">
                                        </i>
                                        <p>Pesanan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('dashboard/transactions/history/index') }}"
                                        class="nav-link {{ request()->is('dashboard/transactions/history/index') ? 'active' : null }}">
                                        <i
                                            class="far fa-circle nav-icon
                                    {{ request()->is('dashboard/transactions/history/index') ? 'text-danger' : null }}">
                                        </i>
                                        <p>Transaksi</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item {{ request()->is('dashboard/users*') ? 'menu-open' : '' }}">
                        <a href="{{ url('dashboard/users/index') }}"
                            class="nav-link {{ request()->is('dashboard/users/index') ? 'active' : null }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Pengguna</p>
                        </a>
                    </li>
                @endauth
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->

    <!-- Sidebar-custom -->
    <div class="sidebar-custom">
        <form action="{{ url('auth/logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-block btn-secondary hide-on-collapse px-1">
                Keluar
            </button>
        </form>
    </div>
    <!-- /.sidebar-custom -->
</aside>
