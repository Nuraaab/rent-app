<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - SpaceGig</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #FF5A5F;
            --secondary-color: #10B981;
            --dark-color: #1F2937;
            --light-gray: #F3F4F6;
            --border-color: #E5E7EB;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-gray);
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1F2937 0%, #111827 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: transform 0.25s ease;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 999;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand span {
            color: var(--primary-color);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-link {
            color: #9CA3AF;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: white;
        }
        
        .nav-link.active {
            background-color: rgba(255, 90, 95, 0.1);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        
        .top-nav {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .sidebar-toggle {
            display: none;
            border: 1px solid #E5E7EB;
            background: #fff;
            color: #374151;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .content-area {
            padding: 1.5rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            border: 1px solid #EEF2F7;
            border-radius: 12px;
            overflow-x: auto;
            overflow-y: visible;
            background: #fff;
        }

        .table {
            min-width: 900px;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th,
        .table th {
            background-color: var(--light-gray);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6B7280;
            border-bottom: 1px solid #E5E7EB;
            padding: 0.9rem 1rem;
        }

        .table tbody td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: 0;
        }

        .table.table-hover tbody tr:hover {
            background: #FFF7F7;
        }

        .table .btn-group-sm > .btn,
        .table .btn-sm {
            border-radius: 8px;
        }

        .pagination {
            margin-bottom: 0;
            gap: 0.25rem;
        }

        .pagination .page-item .page-link {
            border: 1px solid #E5E7EB;
            color: #4B5563;
            border-radius: 8px;
            min-width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #FFF1F2;
            border-color: #FECACA;
            color: #BE123C;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(255, 90, 95, 0.28);
        }

        .pagination .page-item.disabled .page-link {
            color: #9CA3AF;
            background: #F9FAFB;
        }
        
        .badge {
            padding: 0.4em 0.8em;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #E04E53;
            border-color: #E04E53;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 991.98px) {
            body.sidebar-open {
                overflow: hidden;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .top-nav {
                padding: 0.85rem 1rem;
            }

            .top-nav h5 {
                font-size: 1.05rem;
                margin-bottom: 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .sidebar-toggle {
                display: inline-flex;
            }

            .content-area {
                padding: 1rem;
            }

            .table {
                min-width: 760px;
            }
        }

        @media (max-width: 575.98px) {
            .content-area {
                padding: 0.75rem;
            }

            .top-nav .user-name {
                display: none;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            Space<span>Gig</span>
        </a>
        
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                Users
            </a>
            
            <a href="{{ route('admin.properties.index') }}" class="nav-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i>
                Properties
            </a>
            
            <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i>
                Job Openings
            </a>
            
            <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                Applications
            </a>
            
            <a href="{{ route('admin.favorites.index') }}" class="nav-link {{ request()->routeIs('admin.favorites.*') ? 'active' : '' }}">
                <i class="bi bi-heart"></i>
                Favorites
            </a>
            
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
                
                <a href="{{ route('admin.logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="top-nav-left">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-4"></i>
                        <span class="ms-2 user-name">{{ Auth::user()->first_name ?? Auth::user()->email }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (!sidebar || !toggle || !backdrop) return;

            const closeSidebar = () => {
                sidebar.classList.remove('open');
                backdrop.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            };

            const openSidebar = () => {
                sidebar.classList.add('open');
                backdrop.classList.add('show');
                document.body.classList.add('sidebar-open');
            };

            toggle.addEventListener('click', () => {
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            backdrop.addEventListener('click', closeSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });

            sidebar.querySelectorAll('.nav-link').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });
        })();
    </script>
    
    @yield('scripts')
</body>
</html>

