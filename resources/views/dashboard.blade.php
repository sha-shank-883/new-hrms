<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">HRMS System</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('hr-manager'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-people me-2"></i>
                                Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-building me-2"></i>
                                Departments
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-calendar-check me-2"></i>
                                Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-calendar-minus me-2"></i>
                                Leave Requests
                            </a>
                        </li>
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('hr-manager'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-cash-stack me-2"></i>
                                Payroll
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-list-task me-2"></i>
                                Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Documents
                            </a>
                        </li>
                        @if(auth()->user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Employees</h6>
                                        <h2 class="mb-0">0</h2>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Departments</h6>
                                        <h2 class="mb-0">0</h2>
                                    </div>
                                    <i class="bi bi-building fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Tasks</h6>
                                        <h2 class="mb-0">0</h2>
                                    </div>
                                    <i class="bi bi-list-task fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Leave Requests</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">No leave requests found.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Tasks</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">No tasks found.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>