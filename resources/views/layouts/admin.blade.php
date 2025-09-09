<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'HR Management System') }} - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: sticky;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 1rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, .75);
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .navbar-brand {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        .content {
            margin-left: 250px;
            padding: 2rem;
        }
        .dropdown-menu {
            right: 0;
            left: auto;
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <div class="navbar-brand d-flex justify-content-center align-items-center px-3 mb-4">
                        <h5 class="mb-0">HR Management</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        @can('view_permissions')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                                <i class="bi bi-shield-lock"></i> Permissions
                            </a>
                        </li>
                        @endcan

                        @can('view_roles')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                                <i class="bi bi-person-badge"></i> Roles
                            </a>
                        </li>
                        @endcan

                        @can('view_departments')
                        <li class="nav-item">
                            <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->is('admin/departments*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge"></i>
                                Departments
                            </a>
                        </li>
                        @endcan

                        @can('view_tasks')
                        <li class="nav-item">
                            <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->is('admin/tasks*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge"></i>
                                Tasks
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', \App\Models\Department::class)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/departments*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                                <i class="bi bi-person-badge"></i> Departments
                            </a>
                        </li>
                        @endcan

                        @can('view_users')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        @endcan

                        @can('manage_employees')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/employees*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
                                <i class="bi bi-person-lines-fill"></i> Employees
                            </a>
                        </li>
                        @endcan

                        @can('manage_attendance')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/attendances*') ? 'active' : '' }}" href="{{ route('admin.attendances.index') }}">
                                <i class="bi bi-calendar-check"></i> Attendance
                            </a>
                        </li>
                        @endcan

                        @can('manage_leave')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/leave-requests*') ? 'active' : '' }}" href="{{ route('admin.leave-requests.index') }}">
                                <i class="bi bi-calendar-week"></i> Leave Management
                            </a>
                        </li>
                        @endcan

                        @can('manage_payroll')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/payrolls*') ? 'active' : '' }}" href="{{ route('admin.payrolls.index') }}">
                                <i class="bi bi-cash-coin"></i> Payroll
                            </a>
                        </li>
                        @endcan

                        @can('manage_settings')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="content col-md-10 ml-sm-auto">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title', 'Admin Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name ?? 'name'}}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
