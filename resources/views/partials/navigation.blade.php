@auth
    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('hr_manager'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
               href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ 
                request()->routeIs('employees.*') || 
                request()->routeIs('departments.*') || 
                request()->routeIs('positions.*') ? 'active' : '' 
            }}" 
               href="#" id="hrDropdown" 
               role="button" 
               data-bs-toggle="dropdown" 
               aria-expanded="false">
                <i class="bi bi-people me-2"></i> HR Management
            </a>
            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="hrDropdown">
                <li>
                    <a class="dropdown-item {{ request()->routeIs('employees.*') ? 'active' : '' }}" 
                       href="{{ route('employees.index') }}">
                        <i class="bi bi-person-lines-fill me-2"></i> Employees
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request()->routeIs('departments.*') ? 'active' : '' }}" 
                       href="{{ route('departments.index') }}">
                        <i class="bi bi-diagram-3 me-2"></i> Departments
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request()->routeIs('positions.*') ? 'active' : '' }}" 
                       href="{{ route('positions.index') }}">
                        <i class="bi bi-briefcase me-2"></i> Positions
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}" 
                       href="{{ route('leave-requests.index') }}">
                        <i class="bi bi-calendar-check me-2"></i> Leave Management
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request()->routeIs('attendances.*') ? 'active' : '' }}" 
                       href="{{ route('attendances.index') }}">
                        <i class="bi bi-clock-history me-2"></i> Attendance
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if(Auth::user()->hasRole('employee'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" 
               href="{{ route('employee.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('employee.leave_requests.*') ? 'active' : '' }}" 
               href="{{ route('employee.leave_requests.index') }}">
                <i class="bi bi-calendar-check me-2"></i> My Leave Requests
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('employee.attendances.*') ? 'active' : '' }}" 
               href="{{ route('employee.attendances.index') }}">
                <i class="bi bi-clock-history me-2"></i> My Attendance
            </a>
        </li>
    @endif

    @if(Auth::user()->hasRole('manager'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}" 
               href="{{ route('manager.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('manager.leave_requests.*') ? 'active' : '' }}" 
               href="{{ route('manager.leave_requests.index') }}">
                <i class="bi bi-calendar-check me-2"></i> Team Leave Requests
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('manager.team.*') ? 'active' : '' }}" 
               href="{{ route('manager.team.index') }}">
                <i class="bi bi-people me-2"></i> My Team
            </a>
        </li>
    @endif
@endauth
