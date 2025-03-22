<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ route('guru.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img src="{{ asset('assets/img/logo-stella.png') }}" alt="Logo" width="32">
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">Bank Soal</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <a href="{{ route('guru.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Bank Soal -->
        <li class="menu-item {{ request()->is('guru/bank-soal*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-archive"></i>
                <div>Bank Soal</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('guru.bank-soal.index') ? 'active' : '' }}">
                    <a href="{{ route('guru.bank-soal.index') }}" class="menu-link">
                        <div>Daftar Bank Soal</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>
