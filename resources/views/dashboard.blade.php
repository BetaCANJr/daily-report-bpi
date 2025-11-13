@extends('layouts.user-nav')

@section('title', 'Dashboard - Daily Report LPK BPI')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
<div class="row">
    <!-- Statistics Cards -->
<div class="row">
    <div class="col-12 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light text-primary">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['totalReports'] }}</h3>
            <p class="text-muted mb-0">Total Laporan</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light text-warning">
                <i class="fas fa-calendar-day fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['todayReports'] }}</h3>
            <p class="text-muted mb-0">Laporan Hari Ini</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-light text-success">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['completedReports'] }}</h3>
            <p class="text-muted mb-0">Laporan Selesai</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-info-light text-info">
                <i class="fas fa-tasks fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['progressReports'] ?? 0 }}</h3>
            <p class="text-muted mb-0">Dalam Pengerjaan</p>
        </div>
    </div>
</div>
    
    <div class="col-12 col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light text-warning">
                <i class="fas fa-calendar-day fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['todayReports'] }}</h3>
            <p class="text-muted mb-0">Laporan Hari Ini</p>
        </div>
    </div>
    
    <div class="col-12 col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-light text-success">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $stats['completedReports'] }}</h3>
            <p class="text-muted mb-0">Laporan Selesai</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="recent-reports">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('reports.create') }}" class="btn btn-bpi w-100 py-3">
                        <i class="fas fa-plus-circle me-2"></i>
                        Buat Laporan Baru
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-list me-2"></i>
                        Lihat Semua Laporan
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <button type="button" class="btn btn-outline-success w-100 py-3" onclick="showExportModal()">
                        <i class="fas fa-download me-2"></i>
                        Export Laporan
                    </button>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('reports.statistics') }}" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistik
                    </a>
                </div>
            </div>
            
            <!-- Second Row of Quick Actions -->
            <div class="row g-3 mt-2">
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('reports.calendar') }}" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-calendar me-2"></i>
                        Kalender Laporan
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-user me-2"></i>
                        Edit Profil
                    </a>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-warning w-100 py-3">
                        <i class="fas fa-cog me-2"></i>
                        Admin Panel
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Reports -->
<div class="row">
    <div class="col-12">
        <div class="recent-reports">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Laporan Terbaru</h5>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            
            @if($recentReports->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentReports as $report)
                    <div class="report-item">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-6">
                                <h6 class="mb-1">{{ $report->tugas_harian }}</h6>
                                <p class="text-muted mb-1 small">{{ Str::limit($report->deskripsi, 100) }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $report->report_date->format('d M Y') }}
                                    • 
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $report->created_at->format('H:i') }}
                                </small>
                            </div>
                            <div class="col-12 col-md-4">
                                @if($report->kendala)
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ Str::limit($report->kendala, 50) }}
                                    </small>
                                @endif
                            </div>
                            <div class="col-12 col-md-2 text-end">
                                <span class="status-badge {{ $report->status == 'Selesai' ? 'status-completed' : 'status-progress' }}">
                                    {{ $report->status }}
                                </span>
                                <div class="mt-2">
                                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Hapus laporan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-file-alt fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Belum ada laporan</h5>
                    <p class="text-muted mb-4">Mulai dengan membuat laporan pertama Anda</p>
                    <a href="{{ route('reports.create') }}" class="btn btn-bpi">
                        <i class="fas fa-plus me-2"></i>Buat Laporan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Real-time Clock -->
<div class="row mt-4">
    <div class="col-12">
        <div class="text-center">
            <small class="text-muted">
                <i class="fas fa-clock me-1"></i>
                <span id="real-time-clock"></span>
            </small>
        </div>
    </div>
</div>

<script>
    // Real-time clock
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        document.getElementById('real-time-clock').textContent = now.toLocaleDateString('id-ID', options);
    }
    
    setInterval(updateClock, 1000);
    updateClock();
</script>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm" action="{{ route('reports.export') }}" method="GET">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Pilih Format Export</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="">-- Pilih Format --</option>
                            <option value="csv">CSV (Excel Compatible)</option>
                            <option value="pdf">PDF Document</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                    </div>
                    
                    <div class="form-text">
                        Kosongkan tanggal untuk export semua data
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-bpi" onclick="submitExport()">Export</button>
            </div>
        </div>
    </div>
</div>

<script>
// Export Modal Functions
function showExportModal() {
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function submitExport() {
    const format = document.getElementById('exportFormat').value;
    if (!format) {
        alert('Pilih format export terlebih dahulu!');
        return;
    }
    
    document.getElementById('exportForm').submit();
}

// Set default dates (last 30 days)
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    const thirtyDaysAgoStr = thirtyDaysAgo.toISOString().split('T')[0];
    
    document.getElementById('endDate').value = today;
    document.getElementById('startDate').value = thirtyDaysAgoStr;
});
</script>
@endsection