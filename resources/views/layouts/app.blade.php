<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'HR Management System'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="d-flex flex-column h-100">
    <div id="app" class="d-flex flex-column h-100">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="bi bi-building me-2"></i>
                    <span class="font-weight-bold">{{ config('app.name', 'HR Management') }}</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" 
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- Left Side Navigation -->
                    @auth
                        <ul class="navbar-nav me-auto">
                            @include('partials.navigation')
                        </ul>
                    @endauth

                    <!-- Right Side Navigation -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-1"></i> {{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="me-2 d-none d-sm-inline">
                                        <div class="text-truncate" style="max-width: 120px;">{{ Auth::user()->name }}</div>
                                        <div class="text-muted small">{{ Auth::user()->getRoleNames()->first() }}</div>
                                    </div>
                                    <i class="bi bi-person-circle fs-4"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="bi bi-person me-2"></i> {{ __('My Profile') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-shrink-0">
            <!-- Page Heading -->
            @hasSection('page-header')
                <div class="bg-white border-bottom py-3 mb-4">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0 text-gray-800">@yield('page-header')</h1>
                        @hasSection('breadcrumbs')
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                    @yield('breadcrumbs')
                                </ol>
                            </nav>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Alerts -->
            @include('partials.alerts')

            <!-- Page Content -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer mt-auto py-3 bg-light border-top">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
                    <div>
                        <span class="text-muted small">v{{ config('app.version', '1.0.0') }}</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
