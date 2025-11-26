<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agriculture Equipment Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #2c3e50;
            --light-gray: #ecf0f1;
            --border-color: #bdc3c7;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-color);
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--light-gray);
        }
        
        .sidebar-header h4 {
            color: var(--text-color);
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin: 2px 0;
        }
        
        .nav-link {
            color: var(--text-color);
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-radius: 0 25px 25px 0;
            margin-right: 20px;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: var(--accent-color);
            background: var(--light-gray);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            color: var(--accent-color);
            background: var(--light-gray);
            border-left: 3px solid var(--accent-color);
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            color: var(--text-color);
        }
        
        .nav-link:hover i,
        .nav-link.active i {
            color: var(--accent-color);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .header {
            background: white;
            height: var(--header-height);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .content {
            padding: 30px;
        }
        
        .card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-left: 4px solid var(--accent-color);
        }
        
        .stat-card .card-body {
            padding: 25px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .table thead th {
            background: var(--light-gray);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
            font-weight: 600;
        }
        
        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-outline-primary {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .border-left-primary {
            border-left: 4px solid var(--primary-color) !important;
        }
        
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }
        
        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }
        
        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }
        
        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }
        
        .alert-success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .text-success {
            color: #27ae60 !important;
        }
        
        .text-warning {
            color: #f39c12 !important;
        }
        
        .text-danger {
            color: #e74c3c !important;
        }
        
        .text-info {
            color: var(--accent-color) !important;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-tractor me-2"></i>Agriculture Dashboard</h4>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-list"></i>
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}" href="{{ route('admin.subcategories.index') }}">
                        <i class="fas fa-list-alt"></i>
                        Subcategories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="fas fa-tractor"></i>
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                        <i class="fas fa-tag"></i>
                        Brands
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}" href="{{ route('admin.offers.index') }}">
                        <i class="fas fa-tags"></i>
                        Offers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dealers.*') ? 'active' : '' }}" href="{{ route('admin.dealers.index') }}">
                        <i class="fas fa-user-tie"></i>
                        Dealer Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-users"></i>
                        Customer Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-chart-bar"></i>
                        Reports & Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('agriculture.home') }}">
                        <i class="fas fa-external-link-alt"></i>
                        View Website
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-primary d-md-none" id="sidebarToggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>Admin
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        })();

        // SweetAlert2 for session messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: '{{ session('warning') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: '{{ session('info') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Handle validation errors
        @if($errors->any())
            const errorMessages = @json($errors->all());
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '<ul style="text-align: left; margin: 10px 0;"><li>' + errorMessages.join('</li><li>') + '</li></ul>',
                confirmButtonText: 'OK'
            });
        @endif

        // Helper function for SweetAlert (replaces alert())
        window.showAlert = function(message, type = 'info', title = null) {
            const config = {
                icon: type,
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: type === 'error' ? 4000 : 3000,
                timerProgressBar: true
            };
            
            if (title) {
                config.title = title;
            } else {
                config.title = type.charAt(0).toUpperCase() + type.slice(1) + '!';
            }
            
            Swal.fire(config);
        };

        // Replace native alert with SweetAlert
        window.alert = function(message) {
            Swal.fire({
                icon: 'info',
                title: 'Notice',
                text: message,
                confirmButtonText: 'OK'
            });
        };
    </script>
    
    @stack('scripts')
</body>
</html>
