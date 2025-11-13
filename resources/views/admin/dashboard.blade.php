@extends('layouts.admin-nav')

@section('title', 'Admin Dashboard - Daily Report LPK BPI')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'System Overview & Analytics')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light text-primary">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['totalUsers'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Total Users</p>
            <small class="text-success">
                <i class="fas fa-chart-line me-1"></i>
                {{ ($userStats['adminCount'] ?? 0) }} admin, {{ ($userStats['userCount'] ?? 0) }} user
            </small>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light text-success">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['totalReports'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Total Reports</p>
            <small class="text-info">
                <i class="fas fa-calculator me-1"></i>
                {{ ($userStats['reportsPerUser'] ?? 0) }} reports/user
            </small>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light text-warning">
                <i class="fas fa-calendar-day fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['todayReports'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Today's Reports</p>
            <small class="text-primary">
                <i class="fas fa-clock me-1"></i>
                Updated: {{ now()->format('H:i') }}
            </small>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-light text-info">
                <i class="fas fa-user-check fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['activeUsers'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Active Users Today</p>
            <small class="text-success">
                <i class="fas fa-percentage me-1"></i>
                {{ (($stats['totalUsers'] ?? 0) > 0 ? round((($stats['activeUsers'] ?? 0) / ($stats['totalUsers'] ?? 1)) * 100, 1) : 0) }}% active
            </small>
        </div>
    </div>
</div>

<!-- Status Overview Cards -->
<div class="row mb-4">
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-secondary-light text-secondary">
                <i class="fas fa-clock fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['pendingReports'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Pending Reports</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-light text-success">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['completedReports'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Completed</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-light text-info">
                <i class="fas fa-chart-line fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['completionRate'] ?? 0 }}%</h3>
            <p class="text-muted mb-0">Completion Rate</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light text-primary">
                <i class="fas fa-tasks fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $userStats['reportsPerUser'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Reports per User</p>
        </div>
    </div>
</div>

<!-- Charts & Main Content -->
<div class="row">
    <!-- Weekly Summary -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Weekly Report Summary</h5>
            </div>
            
            @php
                $chartData = $chartData ?? ['dates' => [], 'counts' => []];
            @endphp
            
            <div class="weekly-summary">
                @if(!empty($chartData['dates']) && !empty($chartData['counts']))
                    @foreach($chartData['dates'] as $index => $date)
                        @php
                            $count = $chartData['counts'][$index] ?? 0;
                            $isToday = $date === now()->format('M j');
                        @endphp
                        <div class="day-summary {{ $isToday ? 'today' : '' }}">
                            <div class="day-name">{{ $date }}</div>
                            <div class="report-count">{{ $count }} reports</div>
                            @if($isToday)
                                <div class="today-badge">Today</div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No report data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="chart-container">
            <h5 class="mb-4">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.users') }}" class="btn btn-primary">
                    <i class="fas fa-users me-2"></i>Manage Users
                </a>
                <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-alt me-2"></i>View All Reports
                </a>
                <a href="{{ route('reports.create') }}" class="btn btn-outline-success">
                    <i class="fas fa-plus me-2"></i>Create New Report
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-tachometer-alt me-2"></i>User Dashboard
                </a>
            </div>
            
            <!-- System Info -->
            <div class="mt-4 pt-4 border-top">
                <h6 class="mb-3">System Information</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted">Laravel Version</small>
                        <div class="fw-bold">v{{ app()->version() }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">PHP Version</small>
                        <div class="fw-bold">v{{ phpversion() }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Server Time</small>
                        <div class="fw-bold">{{ now()->format('H:i:s') }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Today's Date</small>
                        <div class="fw-bold">{{ now()->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <!-- Recent Reports -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Recent Reports</h5>
                <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            
            @if($recentReports->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentReports as $report)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">{{ $report->tugas_harian ?? 'Daily Report' }}</h6>
                            <span class="badge 
                                @if($report->status == 'Selesai') bg-success
                                @elseif($report->status == 'Dalam Pengerjaan') bg-warning
                                @elseif($report->status == 'Ditolak') bg-danger
                                @else bg-secondary @endif">
                                {{ $report->status ?? 'Unknown' }}
                            </span>
                        </div>
                        <p class="text-muted small mb-2">
                            @if(isset($report->deskripsi) && !empty($report->deskripsi))
                                {{ Str::limit($report->deskripsi, 80) }}
                            @else
                                No description available
                            @endif
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-primary">
                                <i class="fas fa-user me-1"></i>
                                {{ $report->user->name ?? 'Unknown User' }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $report->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No reports yet</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Recent Users</h5>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            
            @if(isset($recentUsers) && $recentUsers->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentUsers as $user)
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                @if($user->photo)
                                    <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" 
                                         class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $user->name ?? 'Unknown User' }}</h6>
                                <p class="text-muted small mb-1">{{ $user->jabatan ?? 'No position' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i>
                                        {{ $user->email ?? 'No email' }}
                                    </small>
                                    <span class="badge {{ ($user->role ?? 'user') == 'admin' ? 'bg-danger' : 'bg-secondary' }}">
                                        {{ $user->role ?? 'user' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No users yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.chart-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    height: 100%;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #1e3a8a;
}

.activity-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e9ecef;
}

.activity-item:last-child {
    border-bottom: none;
}

.weekly-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.day-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    border: 2px solid transparent;
    position: relative;
}

.day-summary.today {
    background: #e3f2fd;
    border-color: #2196f3;
}

.day-name {
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.report-count {
    font-size: 1.2rem;
    font-weight: bold;
    color: #1e3a8a;
}

.today-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #2196f3;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: bold;
}
</style>
@endsection