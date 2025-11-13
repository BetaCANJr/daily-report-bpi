@extends('layouts.user-nav')

@section('title', 'Statistics - Daily Report LPK BPI')
@section('page-title', 'Statistik Laporan')
@section('page-subtitle', 'Analisis dan insight laporan harian Anda')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-12 col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-light text-primary">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $totalReports }}</h3>
            <p class="text-muted mb-0">Total Laporan</p>
        </div>
    </div>
    
    <div class="col-12 col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-light text-success">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $completedReports }}</h3>
            <p class="text-muted mb-0">Laporan Selesai</p>
        </div>
    </div>
    
    <div class="col-12 col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-light text-warning">
                <i class="fas fa-calendar-day fa-2x"></i>
            </div>
            <h3 class="stat-number">{{ $todayReports }}</h3>
            <p class="text-muted mb-0">Laporan Hari Ini</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Status Distribution -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Distribusi Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Activity -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aktivitas 30 Hari Terakhir</h5>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Export Options -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ekspor Laporan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <p class="mb-0">Export laporan harian dalam format CSV atau PDF dengan filter tanggal.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-bpi w-100" onclick="showExportModal()">
                            <i class="fas fa-download me-2"></i>Export Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: @json($statusStats->pluck('status')),
        datasets: [{
            data: @json($statusStats->pluck('count')),
            backgroundColor: [
                '#f59e0b', // Dalam Pengerjaan - orange
                '#10b981'  // Selesai - green
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Activity Chart (will be populated via AJAX)
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Jumlah Laporan',
            data: [],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Load activity data via AJAX
fetch('{{ route("api.reports.chart-data") }}')
    .then(response => response.json())
    .then(data => {
        activityChart.data.labels = data.dates;
        activityChart.data.datasets[0].data = data.counts;
        activityChart.update();
    });
</script>
@endpush