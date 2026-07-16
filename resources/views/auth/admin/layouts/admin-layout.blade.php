<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pusat Kendali') - SIP-O-SIBER</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --bg-dark-slate: #1e293b;
            --bg-darker-slate: #0f172a;
            --text-muted-slate: #94a3b8;
            --primary-glow: #3b82f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        /* Kompleks Sidebar Arsitektur */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background-color: var(--bg-darker-slate);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Navigasi Link Kustomization */
        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted-slate);
            padding: 1.25rem 1.5rem 0.5rem;
            font-weight: 700;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.5rem;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.03);
            color: #ffffff;
        }

        .nav-link-custom.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary-glow);
            border-left-color: var(--primary-glow);
            font-weight: 600;
        }

        .admin-profile-box {
            background-color: var(--bg-dark-slate);
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Sistem Overlay Mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1030;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        .font-monospace {
            font-family: 'JetBrains Mono', monospace !important;
        }

        /* Pulse Animasi Efek Clock */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }

        /* Aturan Responsif Media Screen */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 1.25rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar d-flex flex-column justify-content-between" id="sidebarMenu">
        <div>
            <div class="p-4 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-10">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary p-2 rounded text-white shadow-sm"><i class="fas fa-user-shield fa-lg"></i></div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px; font-size: 1.1rem;">SIP-O-SIBER</h5>
                        <small class="text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px; color: var(--text-muted-slate);">Diskominfotik</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white d-lg-none sidebar-toggle-btn" aria-label="Close"></button>
            </div>
            
            <div class="mt-3">
                <div class="nav-section-title">Menu Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-3"></i> Dashboard Statistik
                </a>
                <a href="{{ route('admin.attendance.index') }}" class="nav-link-custom {{ request()->routeIs('admin.attendance.index') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check me-3"></i> Pantau Absensi Petugas
                </a>

                <div class="nav-section-title mt-2">Manajemen Log</div>
                <a href="{{ route('admin.patrols.all') }}" class="nav-link-custom {{ request()->routeIs('admin.patrols.all') ? 'active' : '' }}">
                    <i class="fas fa-check-circle me-3"></i> Validasi & Approval Log
                </a>
                <a href="{{ route('admin.archives.folders') }}" class="nav-link-custom {{ request()->routeIs('admin.archives.folders') ? 'active' : '' }}">
                    <i class="fas fa-folder me-3"></i> Folder Virtual
                </a>

                <div class="nav-section-title mt-2">Pengaturan</div>
                <a href="{{ route('admin.master-opd.index') }}" class="nav-link-custom {{ request()->routeIs('admin.master-opd.*') ? 'active' : '' }}">
                    <i class="fas fa-cogs me-3"></i> Master Data OPD
                </a>
            </div>
        </div>

        <div class="admin-profile-box d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center text-white fw-bold border border-secondary border-opacity-25" style="width: 38px; height: 38px;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div style="max-width: 130px;">
                    <h6 class="mb-0 text-white small fw-bold text-truncate">{{ Auth::user()->name ?? 'Admin Persandian' }}</h6>
                    <small class="text-truncate d-block" style="font-size: 0.7rem; color: var(--text-muted-slate);">{{ Auth::user()->role ?? 'Administrator' }}</small>
                </div>
            </div>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-danger p-2 rounded hover-scale btn-logout-trigger" 
               title="Keluar Sistem">
                <i class="fas fa-power-off"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-white bg-white shadow-sm border d-lg-none sidebar-toggle-btn" type="button" id="mobileSidebarOpen">
                    <i class="fas fa-bars text-dark"></i>
                </button>
                <div>
                    <h4 class="fw-bold text-dark mb-1">@yield('page_heading', 'Dashboard Utama')</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="badge bg-white text-dark shadow-sm px-3 py-2 border rounded font-monospace d-flex align-items-center gap-2">
                <i class="fas fa-clock text-primary animate-pulse"></i> 
                <span id="liveClockDisplay">SINKRONISASI WAKTU...</span>
            </div>
        </div>

        <div class="flash-message-container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm px-4 py-3" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm px-4 py-3" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. KONTROL RESPONSIVE SIDEBAR TOGGLING
            const sidebarMenu = document.getElementById('sidebarMenu');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const toggleButtons = document.querySelectorAll('.sidebar-toggle-btn, #mobileSidebarOpen');

            toggleButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    sidebarMenu.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            });

            sidebarOverlay.addEventListener('click', () => {
                sidebarMenu.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // 2. RUNNING LIVE REALTIME CLOCK (FORMAT INDONESIA)
            function updateLiveClock() {
                const now = new Date();
                const options = { 
                    day: '2-digit', 
                    month: 'long', 
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                
                const formattedDate = new Intl.DateTimeFormat('id-ID', options).format(now);
                document.getElementById('liveClockDisplay').textContent = formattedDate.replace(/\./g, ':') + ' WIB';
            }
            setInterval(updateLiveClock, 1000);
            updateLiveClock();

            // 3. AUTO-DISMISS FLASH MESSAGE SMOOTHLY
            setTimeout(() => {
                const alerts = document.querySelectorAll('.flash-message-container .alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    @yield('scripts')
</body>
</html>