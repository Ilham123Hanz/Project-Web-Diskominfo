<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - SIP-O-SIBER</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --sidebar-width:280px;
            --sidebar:#071D36;
            --sidebar-hover:#0E2A4A;
            --sidebar-active:#2F6FED;
            --body:#F4F7FB;
            --white:#FFFFFF;
            --border:#E8EEF5;
            --primary:#2F6FED;
            --text:#1F2937;
            --muted:#7C8798;
            --radius:18px;
            --shadow:0 12px 30px rgba(15,23,42,.08);
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:var(--body);
            font-family:'Inter',sans-serif;
            color:var(--text);
        }

        .sidebar{
            position:fixed;
            left:0;
            top:0;
            width:var(--sidebar-width);
            height:100vh;
            background:var(--sidebar);
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            z-index:1000;
        }

        .main-content{
            margin-left:var(--sidebar-width);
            min-height:100vh;
        }

        .sidebar-header{
            padding:28px;
            border-bottom:1px solid rgba(255,255,255,.08);
        }

        .logo-box{
            display:flex;
            align-items:center;
            gap:15px;
        }

        .logo-icon{
            width:46px;
            height:46px;
            background:#2F6FED;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-size:20px;
        }

        .logo-title{
            color:white;
            font-size:22px;
            font-weight:700;
        }

        .logo-sub{
            color:#8FA8C7;
            font-size:12px;
        }

        .nav-section-title{
            color:#6D87A5;
            font-size:11px;
            letter-spacing:1px;
            padding:26px 26px 10px;
            font-weight:700;
        }

        .nav-link-custom{
            margin:6px 14px;
            border-radius:12px;
            padding:14px 18px;
            display:flex;
            align-items:center;
            color:#DCE8F5;
            text-decoration:none;
            transition:.25s;
            font-size:15px;
        }

        .nav-link-custom:hover{
            background:var(--sidebar-hover);
            color:white;
        }

        .nav-link-custom.active{
            background:var(--sidebar-active);
            color:white;
        }

        .nav-link-custom i{
            width:24px;
            margin-right:12px;
        }

        .admin-profile-box{
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,.08);
            background: var(--sidebar);
        }

        .sidebar-profile-link {
            transition: background 0.2s ease;
        }

        .sidebar-profile-link:hover {
            background: var(--sidebar-hover);
        }

        .page-header{
            background:white;
            padding:34px 45px;
            border-bottom:1px solid var(--border);
        }

        .page-header h1{
            font-size:32px;
            font-weight:700;
            margin-bottom:4px;
            color: #0F172A;
        }

        .page-content{
            padding:35px;
        }

        .dashboard-card{
            background:white;
            border-radius:18px;
            box-shadow:var(--shadow);
            padding:30px;
            margin-bottom:30px;
        }

        .stat-card{
            background:white;
            border-radius:18px;
            padding:26px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            position:relative;
            overflow:hidden;
            box-shadow:var(--shadow);
            transition:.25s;
            min-height:165px;
        }

        .stat-card:hover{
            transform:translateY(-6px);
        }

        .stat-card::before{
            content:"";
            position:absolute;
            left:0;
            top:0;
            width:100%;
            height:5px;
        }

        .border-primary::before{ background:#2F6FED; }
        .border-danger::before{ background:#D62828; }
        .border-warning::before{ background:#FF7A00; }
        .border-orange::before{ background:#FDB515; }

        .stat-title{
            font-size:13px;
            color:#6B7280;
            font-weight:700;
            margin-bottom:10px;
        }

        .stat-value{
            font-size:42px;
            font-weight:700;
        }

        .stat-sub{
            margin-top:12px;
            font-size:14px;
        }

        .stat-icon{
            width:65px;
            height:65px;
            border-radius:18px;
            background:#EEF4FF;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .stat-icon i{
            font-size:26px;
            color:#2F6FED;
        }

        #dashboardChart{
            width:100% !important;
            height:380px !important;
        }

        .activity-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:15px 0;
            border-bottom:1px solid #edf2f7;
        }

        .status-card{
            background:white;
            border-radius:16px;
            padding:20px;
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:15px;
        }

        .status-dot{
            width:15px;
            height:15px;
            border-radius:50%;
            background:#22c55e;
            box-shadow:0 0 10px rgba(34,197,94,.7);
        }

        @media(max-width:991px){
            .sidebar{
                transform:translateX(-100%);
            }
            .main-content{
                margin-left:0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
    <div class="sidebar">
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

            <div class="nav-section-title">MANAJEMEN LOG</div>

            <a href="{{ route('admin.validasi') }}" class="nav-link-custom {{ request()->routeIs('admin.validasi') ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> Validasi Riwayat Log
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

        <!-- Profil Admin & Tombol Logout di Sidebar Bawah -->
        <div class="admin-profile-box flex-shrink-0">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <a href="{{ route('admin.profil') }}" class="text-decoration-none text-white d-flex align-items-center gap-2 p-1 rounded-3 sidebar-profile-link overflow-hidden flex-grow-1" style="min-width: 0;">
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 34px; height: 34px; font-size: 12px;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="overflow-hidden" style="min-width: 0;">
                        <h6 class="mb-0 text-truncate fw-semibold" style="font-size: 12px;">{{ Auth::user()->name ?? 'Admin Persandian' }}</h6>
                        <small class="text-secondary text-truncate d-block" style="font-size: 10px;">Administrator</small>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0 flex-shrink-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light px-2 py-1" title="Logout" style="font-size: 12px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>@yield('page_heading', View::getSection('title') ?? 'Dashboard Admin')</h1>
                    <div class="text-secondary">
                        @yield('breadcrumb')
                    </div>
                </div>
                <!-- POJOK KANAN ATAS: Jam Digital -->
                <div class="text-end">
                    <span id="liveClockDisplay" class="fw-bold text-primary px-3 py-2 bg-white rounded-3 shadow-sm border" style="font-size: 1rem; letter-spacing: 1px;">00:00:00</span>
                </div>
            </div>
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 13px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 13px;">
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
        function updateClock(){
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById("liveClockDisplay");
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @stack('scripts')
</body>
</html>