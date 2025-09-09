@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cog me-1"></i>
            Application Settings
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="{{ setting('site_name', config('app.name')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="site_email" class="form-label">Site Email</label>
                        <input type="email" class="form-control" id="site_email" name="site_email" 
                               value="{{ setting('site_email', config('mail.from.address')) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="timezone" class="form-label">Timezone</label>
                        <select class="form-select" id="timezone" name="timezone" required>
                            @foreach(timezone_identifiers_list() as $timezone)
                                <option value="{{ $timezone }}" 
                                    {{ setting('timezone', config('app.timezone')) === $timezone ? 'selected' : '' }}>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date_format" class="form-label">Date Format</label>
                        <select class="form-select" id="date_format" name="date_format" required>
                            @php
                                $dateFormats = [
                                    'Y-m-d' => 'YYYY-MM-DD',
                                    'm/d/Y' => 'MM/DD/YYYY',
                                    'd/m/Y' => 'DD/MM/YYYY',
                                    'd M, Y' => 'DD MMM, YYYY',
                                    'M d, Y' => 'MMM DD, YYYY',
                                ];
                                $currentFormat = setting('date_format', 'Y-m-d');
                            @endphp
                            @foreach($dateFormats as $format => $label)
                                <option value="{{ $format }}" {{ $currentFormat === $format ? 'selected' : '' }}>
                                    {{ $label }} ({{ now()->format($format) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="time_format" class="form-label">Time Format</label>
                        <select class="form-select" id="time_format" name="time_format" required>
                            @php
                                $timeFormats = [
                                    'H:i' => '24-hour (14:30)',
                                    'h:i A' => '12-hour (02:30 PM)',
                                ];
                                $currentTimeFormat = setting('time_format', 'H:i');
                            @endphp
                            @foreach($timeFormats as $format => $label)
                                <option value="{{ $format }}" {{ $currentTimeFormat === $format ? 'selected' : '' }}>
                                    {{ $label }} ({{ now()->format($format) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize any JavaScript components if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any plugins or custom JS here
    });
</script>
@endpush
