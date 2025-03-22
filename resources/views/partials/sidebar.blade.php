<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
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
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Kepegawaian -->
        <li class="menu-item {{ request()->is('admin/guru*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-id"></i>
                <div>Kepegawaian</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.guru.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.guru.index') }}" class="menu-link">
                        <div>Data Guru</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Mata Pelajaran -->
        <li class="menu-item {{ request()->is('admin/mata-pelajaran*') ? 'active open' : '' }}">
            <a href="{{ route('admin.mata-pelajaran.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-book"></i>
                <div>Mata Pelajaran</div>
            </a>
        </li>

        <!-- Tahun Pelajaran -->
        <li class="menu-item {{ request()->is('admin/tahun-pelajaran*') ? 'active open' : '' }}">
            <a href="{{ route('admin.tahun-pelajaran.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-calendar"></i>
                <div>Tahun Pelajaran</div>
            </a>
        </li>

        <!-- Data Ujian -->
        <li class="menu-item {{ request()->is('admin/data-ujian*') ? 'active open' : '' }}">
            <a href="{{ route('admin.data-ujian.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-file-text"></i>
                <div>Data Ujian</div>
            </a>
        </li>

        <!-- Maping Mapel -->
        <li class="menu-item {{ request()->is('admin/maping-mapel*') ? 'active open' : '' }}">
            <a href="{{ route('admin.maping.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-map"></i>
                <div>Maping Mapel</div>
            </a>
        </li>

        <!-- Kelas -->
        <li class="menu-item {{ request()->is('admin/kelas*') ? 'active open' : '' }}">
            <a href="{{ route('admin.kelas.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-school"></i>
                <div>Kelas</div>
            </a>
        </li>

        <!-- Bank Soal -->
        <li class="menu-item {{ request()->is('admin/bank-soal*') ? 'active open' : '' }}">
            <a href="{{ route('admin.bank-soal.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-archive"></i>
                <div>Bank Soal</div>
            </a>
        </li>
    </ul>
</aside>
