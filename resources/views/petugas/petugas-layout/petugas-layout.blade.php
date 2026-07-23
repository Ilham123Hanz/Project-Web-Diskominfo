<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIP-O-SIBER - Operator Panel')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Select2 (untuk dropdown pencarian) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --brand-dark: #0A1D37;
            --brand-dark-accent: #0F2744;
            --brand-blue: #0052A3;
            --brand-accent: #00D2FF;
            --brand-success: #10B981;
            --bg-light: #F8FAFC;
            --border-color: #E2E8F0;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-light);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Brand Navbar Header */
        .top-navbar-brand {
            background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand-dark-accent) 100%);
            border-bottom: 3px solid #1E293B;
            padding: 0.65rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .navbar-logo-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: opacity 0.2s;
        }

        .navbar-logo-title:hover {
            color: #FFFFFF;
            opacity: 0.9;
        }

        .navbar-logo-title i {
            color: #F59E0B;
            filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.4));
        }

        .nav-link-cyber {
            color: #94A3B8;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link-cyber:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateY(-1px);
        }

        .nav-link-cyber.active {
            color: #FFFFFF;
            background-color: var(--brand-blue);
            box-shadow: 0 2px 8px rgba(0, 82, 163, 0.4);
            border-bottom: 2px solid var(--brand-accent);
        }

        /* Top Sub-Bar Status Operator */
        .operator-status-bar {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            padding: 0.65rem 2rem;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        }

        .user-avatar-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: rgba(0, 82, 163, 0.1);
            color: var(--brand-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .badge-ip-status {
            background-color: #1E293B;
            color: #00D2FF;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .pulse-online {
            width: 8px;
            height: 8px;
            background-color: var(--brand-success);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .btn-logout-cyber {
            background-color: transparent;
            border: 1px solid #EF4444;
            color: #EF4444;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 6px 14px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-logout-cyber:hover {
            background-color: #EF4444;
            color: #FFFFFF;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        /* Main Workspace Content Area */
        .main-content-wrapper {
            flex: 1;
            padding: 1.75rem 2rem;
        }

        /* Footer Custom */
        .footer-cyber {
            background-color: #FFFFFF;
            border-top: 1px solid var(--border-color);
            padding: 1rem 2rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .footer-cyber a {
            color: var(--brand-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .footer-cyber a:hover {
            color: var(--brand-dark);
            text-decoration: underline;
        }

        /* Responsif Navbar Collapse Custom Styling */
        @media (max-width: 767.98px) {
            .top-navbar-brand { padding: 0.75rem 1rem; }
            .operator-status-bar { padding: 0.75rem 1rem; }
            .main-content-wrapper { padding: 1rem; }
            .nav-link-cyber { width: 100%; margin-bottom: 4px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- 1. NAVBAR UTAMA ATAS (NAVIGASI WIREFRAME) -->
    <nav class="top-navbar-brand navbar navbar-expand-md navbar-dark">
        <div class="container-fluid px-0">
            <a href="{{ Route::has('petugas.dashboard') ? route('petugas.dashboard') : '#' }}" class="navbar-logo-title">
                <i class="fas fa-shield-halved"></i>
                <span>SIP-O-SIBER</span>
            </a>

            <!-- Mobile Navbar Toggler -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCyberMenu" aria-controls="navbarCyberMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse mt-2 mt-md-0" id="navbarCyberMenu">
                <!-- Menu Navigasi Utama -->
                <div class="navbar-nav me-auto ms-md-4 gap-1">
                    <a href="{{ Route::has('petugas.dashboard') ? route('petugas.dashboard') : '#' }}" 
                       class="nav-link-cyber {{ request()->routeIs('petugas.dashboard*') ? 'active' : '' }}">
                        <i class="fas fa-gauge-high"></i> Dashboard & Absensi
                    </a>

                    <a href="{{ Route::has('petugas.riwayat-log-petugas') ? route('petugas.riwayat-log-petugas') : (Route::has('riwayat-log-petugas') ? route('riwayat-log-petugas') : '#') }}" 
                       class="nav-link-cyber {{ request()->routeIs('*riwayat-log-petugas*') ? 'active' : '' }}">
                        <i class="fas fa-list-check"></i> Riwayat Log
                    </a>

                    <a href="{{ Route::has('petugas.laporan.create') ? route('petugas.laporan.create') : '#' }}" 
                       class="nav-link-cyber {{ request()->routeIs('petugas.laporan*') ? 'active' : '' }}">
                        <i class="fas fa-file-circle-plus"></i> Input Patroli Siber
                    </a>
                </div>

                <!-- Right System Header Action -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
                    <span class="badge-ip-status d-none d-sm-inline-block">
                        <i class="fas fa-network-wired me-1"></i> IP: {{ request()->ip() }}
                    </span>
                    @if(Route::has('logout'))
                        <form action="{{ route('logout') }}" method="POST" class="m-0 w-100 w-md-auto">
                            @csrf
                            <button type="submit" class="btn btn-logout-cyber w-100">
                                <i class="fas fa-power-off me-1"></i> KELUAR
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. STATUS OPERATOR AKTIF BAR -->
    <div class="operator-status-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="user-avatar-icon">
                <i class="fas fa-user-gear"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small fw-semibold" style="font-size: 0.72rem; line-height: 1; letter-spacing: 0.5px;">OPERATOR CSIRT AKTIF</span>
                    <span class="pulse-online" title="Sistem Online & Terhubung"></span>
                </div>
                <strong class="text-dark fw-bold" style="font-size: 0.95rem;">
                    {{ Auth::user()->name ?? 'Petugas Patroli' }}
                </strong>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-2 fw-bold" style="font-size: 0.75rem;">
                <i class="fas fa-id-badge me-1"></i> ROLE: {{ strtoupper(Auth::user()->role ?? 'Petugas') }}
            </span>
        </div>
    </div>

    <!-- 3. WRAPPER KONTEN UTAMA HALAMAN -->
    <main class="main-content-wrapper">
        <!-- Flash Alert Notifikasi -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert" style="border-left: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-circle-check fa-lg me-2 text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert" style="border-left: 4px solid #EF4444 !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-circle-exclamation fa-lg me-2 text-danger"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tempat Menyisipkan Konten Spesifik Halaman -->
        @yield('content')
    </main>

    <!-- 4. FOOTER -->
    <footer class="footer-cyber d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <strong>SIP-O-SIBER</strong> &copy; 2026 Diskominfo Provinsi Lampung - Sistem Informasi Patroli Siber
        </div>
        <div class="d-flex gap-3">
            <a href="#"><i class="fas fa-circle-question me-1"></i>Bantuan</a>
            <a href="#"><i class="fas fa-user-shield me-1"></i>Kebijakan Privasi</a>
            <a href="#"><i class="fas fa-headset me-1"></i>Kontak Admin</a>
        </div>
    </footer>

    <!-- JS DEPENDENCIES -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>