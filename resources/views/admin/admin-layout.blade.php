<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - SIP-O-SIBER DISKOMINFO</title>

    <!-- Google Fonts & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar: #071D36;
            --sidebar-hover: #0E2A4A;
            --sidebar-active: #2F6FED;
            --body: #F4F7FB;
            --white: #FFFFFF;
            --border: #E8EEF5;
            --primary: #2F6FED;
            --text: #1F2937;
            --muted: #7C8798;
            --radius: 18px;
            --shadow: 0 12px 30px rgba(15, 23, 42, .08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--body);
            font-family: 'Inter', sans-serif;
            color: var(--text);
        }

        /* --- SIDEBAR STYLING --- */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 28px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 46px;
            height: 46px;
            background: #2F6FED;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(47, 111, 237, 0.4);
        }

        .logo-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .logo-sub {
            color: #8FA8C7;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .nav-section-title {
            color: #6D87A5;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 24px 26px 8px;
            font-weight: 700;
        }

        .nav-link-custom {
            margin: 4px 14px;
            border-radius: 12px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            color: #DCE8F5;
            text-decoration: none;
            transition: all 0.25s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link-custom:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }

        .nav-link-custom.active {
            background: var(--sidebar-active);
            color: white;
            box-shadow: 0 4px 15px rgba(47, 111, 237, 0.3);
        }

        /* Perbaikan Spasi Icon & Teks */
        .nav-link-custom i {
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
            text-align: center;
            display: inline-block;
        }

        .admin-profile-box {
            padding: 14px 16px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            background: var(--sidebar);
        }

        .sidebar-profile-link {
            transition: background 0.2s ease;
        }

        .sidebar-profile-link:hover {
            background: var(--sidebar-hover);
        }

        /* --- MAIN CONTENT LAYOUT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            background: white;
            padding: 28px 40px;
            border-bottom: 1px solid var(--border);
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #0F172A;
        }

        .page-content {
            padding: 35px 40px;
            flex-grow: 1;
            max-width: 100%;
            overflow-x: visible;
        }

        /* Responsive container for folder detail */
        .folder-detail-container {
            max-width: 100%;
            overflow-x: auto;
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive improvements for folder virtual detail */
        @media (max-width: 1199.98px) {
            .page-content {
                padding: 20px 25px;
            }
            
            .page-header {
                padding: 20px 25px;
            }
            
            .folder-detail-container {
                padding: 0 5px;
            }
        }

        @media (max-width: 991.98px) {
            .page-content {
                padding: 15px 20px;
            }
            
            .page-header {
                padding: 15px 20px;
            }
            
            .page-header h1 {
                font-size: 22px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }

        @media (max-width: 767.98px) {
            .page-content {
                padding: 10px 15px;
            }
            
            .page-header {
                padding: 12px 15px;
            }
            
            .page-header h1 {
                font-size: 20px;
            }
            
            .folder-detail-container {
                padding: 0;
            }
            
            .folder-detail-container .card {
                border-radius: 12px;
            }
            
            .folder-detail-container .card-header {
                padding: 15px;
            }
            
            .folder-detail-container .card-body {
                padding: 15px;
            }
            
            .folder-detail-container .table-responsive {
                border: none;
            }
            
            .folder-detail-container .table th,
            .folder-detail-container .table td {
                padding: 8px 10px;
                font-size: 12px;
            }
            
            .folder-detail-container .table th {
                font-size: 10px;
            }
            
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }
            
            .modal-xl {
                max-width: calc(100% - 20px);
            }
            
            .d-flex.gap-3 {
                gap: 1rem !important;
            }
            
            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .btn-sm {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 575.98px) {
            .page-content {
                padding: 8px 12px;
            }
            
            .page-header {
                padding: 10px 12px;
            }
            
            .page-header h1 {
                font-size: 18px;
            }
            
            .folder-detail-container .card {
                border-radius: 10px;
            }
            
            .folder-detail-container .card-header {
                padding: 12px;
            }
            
            .folder-detail-container .card-body {
                padding: 12px;
            }
            
            .folder-detail-container .table th,
            .folder-detail-container .table td {
                padding: 6px 8px;
                font-size: 11px;
            }
            
            .folder-detail-container .table th {
                font-size: 9px;
            }
            
            .folder-detail-container .badge {
                font-size: 8px;
                padding: 3px 6px;
            }
            
            .modal-dialog {
                margin: 5px;
                max-width: calc(100% - 10px);
            }
            
            .modal-header {
                padding: 15px !important;
            }
            
            .modal-body {
                padding: 15px !important;
            }
            
            .modal-footer {
                padding: 15px !important;
            }
            
            .input-group-sm .form-control {
                font-size: 12px;
                padding: 6px 10px;
            }
        }

        /* Ensure table cells don't overflow */
        .table-responsive table {
            min-width: 600px;
        }

        @media (max-width: 991.98px) {
            .table-responsive table {
                min-width: 550px;
            }
        }

        @media (max-width: 767.98px) {
            .table-responsive table {
                min-width: 500px;
            }
        }

        @media (max-width: 575.98px) {
            .table-responsive table {
                min-width: 450px;
            }
        }

        /* --- CARD & GENERAL UI --- */
        .dashboard-card {
            background: white;
            border-radius: 18px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--border);
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.25s ease;
            min-height: 150px;
            border: 1px solid var(--border);
        }

        .stat-card:hover {
            transform: translateY(-6px);
        }

        .stat-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 5px;
        }

        .border-primary::before { background: #2F6FED; }
        .border-danger::before { background: #D62828; }
        .border-warning::before { background: #FF7A00; }
        .border-orange::before { background: #FDB515; }

        .stat-title {
            font-size: 13px;
            color: #6B7280;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 38px;
            font-weight: 700;
            color: #0F172A;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: #EEF4FF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon i {
            font-size: 24px;
            color: #2F6FED;
        }

        /* Merapikan Icon pada Tombol dan Tabel */
        .btn i, .table td i, .table th i {
            margin-right: 6px;
        }

        /* --- RESPONSIVE MOBILE SIDEBAR --- */
        @media(max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .page-header {
                padding: 20px;
            }
            .page-content {
                padding: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebarMenu">
        <div class="overflow-y-auto flex-grow-1">
            <div class="sidebar-header">
                <div class="logo-box">
                    <div class="logo-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="logo-title">SIP-O-SIBER</div>
                        <div class="logo-sub">DISKOMINFOTIK</div>
                    </div>
                </div>
            </div>

            <div class="nav-section-title">MENU UTAMA</div>

            <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard Admin
            </a>

            <a href="{{ route('admin.attendance.index') }}" class="nav-link-custom {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> Pantau Absensi
            </a>

            <div class="nav-section-title">MANAJEMEN LOG & SMTP</div>

            <a href="{{ route('admin.validasi') }}" class="nav-link-custom {{ request()->routeIs('admin.validasi') || request()->routeIs('admin.patrol.*') ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> Validasi Patroli
            </a>

            <a href="{{ route('admin.smtp') }}" class="nav-link-custom {{ request()->routeIs('admin.smtp') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Distribusi SMTP
            </a>

            <div class="nav-section-title">MANAJEMEN DATA</div>

            <a href="{{ route('admin.master-opd.index') }}" class="nav-link-custom {{ request()->routeIs('admin.master-opd.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i> Master OPD
            </a>

            <a href="{{ route('admin.folders.index') }}" class="nav-link-custom {{ request()->routeIs('admin.folders.*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i> Folder Virtual
            </a>
        </div>

        <!-- Profil Admin & Logout di Sidebar Bawah -->
        <div class="admin-profile-box flex-shrink-0">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <a href="{{ route('admin.profil') }}" class="text-decoration-none text-white d-flex align-items-center gap-2 p-1 rounded-3 sidebar-profile-link overflow-hidden flex-grow-1" style="min-width: 0;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 13px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="overflow-hidden" style="min-width: 0;">
                        <h6 class="mb-0 text-truncate fw-semibold" style="font-size: 13px;">{{ Auth::user()->name ?? 'Administrator' }}</h6>
                        <small class="text-secondary text-truncate d-block" style="font-size: 11px;">Admin Persandian</small>
                    </div>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="m-0 flex-shrink-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 text-white px-2 py-1" title="Keluar Aplikasi" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                        <i class="fas fa-sign-out-alt text-danger"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none border shadow-sm" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1>@yield('page_heading', View::getSection('title') ?? 'Dashboard Admin')</h1>
                        <div class="text-secondary" style="font-size: 13px;">
                            @yield('breadcrumb')
                        </div>
                    </div>
                </div>

                <!-- JAM DIGITAL INDONESIA & TANGGAL REALTIME -->
                <div class="text-end d-none d-sm-block">
                    <div class="fw-bold text-primary px-3 py-2 bg-white rounded-3 shadow-sm border d-inline-flex align-items-center gap-2">
                        <i class="far fa-clock"></i>
                        <span id="liveClockDisplay" style="font-family: 'JetBrains Mono', monospace; font-size: 1.05rem; letter-spacing: 1px;">00:00:00 WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAGE CONTENT -->
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 14px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 14px;">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar khusus tampilan Mobile
        function toggleSidebar() {
            document.getElementById('sidebarMenu').classList.toggle('show');
        }

        // Live Clock Realtime (WIB)
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById("liveClockDisplay");
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Auto-dismiss alerts/notifications after 1 second
        document.addEventListener('DOMContentLoaded', function() {
            // Handle session flash alerts
            document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                setTimeout(function() {
                    var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                }, 1000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>