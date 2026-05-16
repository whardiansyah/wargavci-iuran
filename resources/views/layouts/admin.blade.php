<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    
    @vite(['resources/css/app.css'])
    @stack('styles')

    <style>
        :root {
            --primary: #4568DC;
            --success: #2dce89;
            --warning: #fb6340;
            --danger: #f5365c;
            --info: #11cdef;
        }

        body {
            background-color: #f7f8f9;
            font-family: 'Open Sans', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            padding-top: 20px;
            overflow-y: auto;
        }

        .sidebar .brand {
            padding: 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }

        .sidebar .brand h5 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding-left: 30px;
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Top Navbar */
        .navbar-admin {
            background: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .navbar-admin .search-box {
            max-width: 300px;
        }

        .navbar-admin .search-box input {
            border-radius: 20px;
            border: 1px solid #e3e6f0;
            padding: 8px 15px;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }

        .stat-card.card-primary {
            border-left-color: var(--primary);
        }

        .stat-card.card-success {
            border-left-color: var(--success);
        }

        .stat-card.card-warning {
            border-left-color: var(--warning);
        }

        .stat-card.card-danger {
            border-left-color: var(--danger);
        }

        .stat-card.card-info {
            border-left-color: var(--info);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 15px;
        }

        .stat-card.card-primary .stat-icon {
            background-color: var(--primary);
        }

        .stat-card.card-success .stat-icon {
            background-color: var(--success);
        }

        .stat-card.card-warning .stat-icon {
            background-color: var(--warning);
        }

        .stat-card.card-danger .stat-icon {
            background-color: var(--danger);
        }

        .stat-card.card-info .stat-icon {
            background-color: var(--info);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 12px;
            color: #95a5a6;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .chart-card-header {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .chart-card-footer {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 10px;
        }

        /* Table */
        .table-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-card-header {
            background: var(--warning);
            color: white;
            padding: 20px;
            font-weight: 600;
        }

        .table-responsive {
            border-radius: 0 0 8px 8px;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #e3e6f0;
            background-color: #f8f9fa;
            color: #95a5a6;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            padding: 15px;
        }

        .table tbody td {
            border-bottom: 1px solid #e3e6f0;
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }

            .stat-card {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* User Menu */
        .user-menu {
            border-top: 1px solid rgba(255,255,255,0.2);
            margin-top: 20px;
            padding-top: 20px;
        }

        .user-menu a {
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }

        .user-menu a:hover {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding-left: 30px;
        }

        /* Submenu Styles */
        .sidebar-menu li.menu-item {
            position: relative;
        }

        .sidebar-menu .submenu {
            display: none;
            background-color: rgba(0,0,0,0.1);
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar-menu li.menu-item.active > .submenu,
        .sidebar-menu li.menu-item.show > .submenu {
            display: block;
        }

        .sidebar-menu .submenu li {
            margin: 0;
        }

        .sidebar-menu .submenu a {
            padding: 10px 20px 10px 50px;
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            border-left: 2px solid transparent;
        }

        .sidebar-menu .submenu a:hover,
        .sidebar-menu .submenu a.active {
            background-color: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
        }

        .sidebar-menu .menu-toggle::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 20px;
            top: 12px;
            font-size: 12px;
            transition: transform 0.3s;
        }

        .sidebar-menu li.menu-item.show > a.menu-toggle::after {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <h5><i class="fas fa-building"></i> Villa Cilame</h5>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard') }}" ><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li class="menu-item {{ request()->routeIs('anggota.*') ? 'show' : '' }}">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)"><i class="fas fa-building"></i> Villa Management</a>
                <ul class="submenu">
                    @can('viewAny', App\Models\Anggota::class)
                        <li><a href="{{ route('anggota.index') }}" class="{{ request()->routeIs('anggota.*') ? 'active' : '' }}"><i class="fas fa-kaaba"></i> Anggota Umroh</a></li>
                    @endcan
                </ul>
            </li>
            <li class="menu-item {{ request()->routeIs('pencatatan_air.*', 'tagihan.index', 'tagihan.create', 'tagihan.show', 'tagihan.edit', 'transaksi_kas.*') ? 'show' : '' }}">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)"><i class="fas fa-calendar-alt"></i> Transaksi</a>
                <ul class="submenu">
                    @can('viewAny', App\Models\PencatatanAir::class)
                        <li><a href="{{ route('pencatatan_air.index') }}" class="{{ request()->routeIs('pencatatan_air.*') ? 'active' : '' }}"><i class="fas fa-tint"></i> Pencatatan Air</a></li>
                    @endcan
                    @can('viewAny', App\Models\Tagihan::class)
                        <li><a href="{{ route('tagihan.index') }}" class="{{ request()->routeIs('tagihan.index', 'tagihan.create', 'tagihan.show', 'tagihan.edit') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar"></i> Tagihan</a></li>
                    @endcan
                    @can('viewAny', App\Models\TransaksiKas::class)
                        <li><a href="{{ route('transaksi_kas.index') }}" class="{{ request()->routeIs('transaksi_kas.*') ? 'active' : '' }}"><i class="fas fa-cash-register"></i> Transaksi Kas</a></li>
                    @endcan
                </ul>
            </li>
            <li class="menu-item {{ request()->routeIs('tagihan.rutin', 'tabungan_umroh.*') ? 'show' : '' }}">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)"><i class="fas fa-money-bill"></i> Keuangan</a>
                <ul class="submenu">
                    @can('viewAny', App\Models\Tagihan::class)
                        <li><a href="{{ route('tagihan.rutin') }}" class="{{ request()->routeIs('tagihan.rutin') ? 'active' : '' }}"><i class="fas fa-receipt"></i> Tagihan Rutin</a></li>
                    @endcan
                    @can('viewAny', App\Models\TabunganUmroh::class)
                        <li><a href="{{ route('tabungan_umroh.index') }}" class="{{ request()->routeIs('tabungan_umroh.*') ? 'active' : '' }}"><i class="fas fa-piggy-bank"></i> Tabungan Umroh</a></li>
                    @endcan
                </ul>
            </li>
            <li class="menu-item {{ request()->routeIs('master_penghunis.*', 'penyewa.*') ? 'show' : '' }}">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)"><i class="fas fa-users"></i> Penghuni</a>
                <ul class="submenu">
                    <li><a href="{{ route('master_penghunis.index') }}" class="{{ request()->routeIs('master_penghunis.*') ? 'active' : '' }}"><i class="fas fa-home"></i> Data Rumah</a></li>
                    <li><a href="{{ route('penyewa.index') }}" class="{{ request()->routeIs('penyewa.*') ? 'active' : '' }}"><i class="fas fa-person-hiking"></i> Penyewa</a></li>
                </ul>
            </li>
            <li class="menu-item {{ request()->routeIs('laporan.*') ? 'show' : '' }}">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)"><i class="fas fa-file-invoice"></i> Laporan</a>
                <ul class="submenu">
                    @can('viewAny', App\Models\TabunganUmroh::class)
                        <li><a href="{{ route('laporan.tabungan_umroh') }}" class="{{ request()->routeIs('laporan.tabungan_umroh*') ? 'active' : '' }}"><i class="fas fa-piggy-bank"></i> Tabungan Umroh</a></li>
                    @endcan
                </ul>
            </li>
            <li class="menu-item {{ request()->routeIs('users.*', 'roles.*', 'permissions.*', 'master_configs.*') ? 'show' : '' }}">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)"><i class="fas fa-cog"></i> Pengaturan</a>
                <ul class="submenu">
                    <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Kelola Users</a></li>
                    <li><a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}"><i class="fas fa-users-cog"></i> Kelola Roles</a></li>
                    <li><a href="{{ route('permissions.index') }}" class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}"><i class="fas fa-key"></i> Kelola Permissions</a></li>
                    @can('viewAny', App\Models\MasterConfig::class)
                        <li><a href="{{ route('master_configs.index') }}" class="{{ request()->routeIs('master_configs.*') ? 'active' : '' }}"><i class="fas fa-layer-group"></i> Kelola Master</a></li>
                    @endcan
                </ul>
            </li>
        </ul>

        <div class="user-menu">
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="navbar-admin d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Dashboard</h4>
            <div class="search-box">
                <input type="text" class="form-control" placeholder="Cari...">
            </div>
        </div>

        <!-- Content -->
        @yield('content')
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
    
    <script>
        function toggleSubmenu(event, element) {
            event.preventDefault();
            const parent = element.closest('.menu-item');
            parent.classList.toggle('show');
        }
    </script>
</body>
</html>
