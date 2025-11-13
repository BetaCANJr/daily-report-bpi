@extends('layouts.user-nav')

@section('title', 'Semua Laporan - Daily Report LPK BPI')
@section('page-title', 'Semua Laporan')
@section('page-subtitle', 'Riwayat laporan harian Anda')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-3">
                        <label for="search" class="form-label">Cari Laporan</label>
                        <input type="text" class="form-control" id="search" placeholder="Cari tugas atau deskripsi...">
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="col-12 col-md-3">
                        <label for="user_filter" class="form-label">Filter User</label>
                        <select class="form-select" id="user_filter">
                            <option value="">Semua User</option>
                            <option value="my_reports">Laporan Saya Saja</option>
                            <option value="other_reports">Laporan User Lain</option>
                        </select>
                    </div>
                    @endif
                    
                    <div class="col-12 col-md-3">
                        <label for="status_filter" class="form-label">Filter Status</label>
                        <select class="form-select" id="status_filter">
                            <option value="">Semua Status</option>
                            <option value="Dalam Pengerjaan">Dalam Pengerjaan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    
                    <div class="col-12 col-md-3">
                        <label for="date_filter" class="form-label">Filter Tanggal</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="filterReports()">
                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-refresh me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports List -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if($reports->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($reports as $report)
                        <div class="list-group-item p-4 report-item" 
                             data-status="{{ $report->status }}"
                             data-date="{{ $report->report_date->format('Y-m-d') }}"
                             data-search="{{ strtolower($report->tugas_harian . ' ' . $report->deskripsi) }}"
                             data-user-id="{{ $report->user_id }}">
                            <div class="row align-items-center">
                                <!-- User Info & Date -->
                                <div class="col-12 col-md-3 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $report->user->name }}
                                                @if($report->user_id == auth()->id())
                                                    <span class="badge bg-success ms-1">Milik Saya</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">User Lain</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $report->report_date->format('d M Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Report Content -->
                                <div class="col-12 col-md-6">
                                    <h6 class="mb-2 text-primary">{{ $report->tugas_harian }}</h6>
                                    <p class="mb-2 text-muted small">{{ Str::limit($report->deskripsi, 120) }}</p>
                                    
                                    @if($report->kendala)
                                    <div class="mb-2">
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>Kendala:</strong> {{ Str::limit($report->kendala, 80) }}
                                        </small>
                                    </div>
                                    @endif

                                    @if($report->catatan_tambahan)
                                    <div class="mb-2">
                                        <small class="text-info">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            <strong>Catatan:</strong> {{ Str::limit($report->catatan_tambahan, 80) }}
                                        </small>
                                    </div>
                                    @endif

                                    @if($report->bukti_file)
                                    <div>
                                        <small>
                                            <i class="fas fa-paperclip me-1"></i>
                                            <a href="{{ Storage::url($report->bukti_file) }}" target="_blank" class="text-decoration-none">
                                                Lihat Bukti File
                                            </a>
                                        </small>
                                    </div>
                                    @endif

                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Dibuat: {{ $report->created_at->format('H:i') }}
                                        @if($report->updated_at != $report->created_at)
                                            • Diupdate: {{ $report->updated_at->format('H:i') }}
                                        @endif
                                    </small>
                                </div>

                                <!-- Status & Actions -->
                                <div class="col-12 col-md-3 text-md-end">
                                    <div class="mb-2">
                                        <span class="status-badge {{ $report->status == 'Selesai' ? 'status-completed' : 'status-progress' }}">
                                            {{ $report->status }}
                                        </span>
                                    </div>
                                    
                                    <div class="btn-group">
                                        @can('update-report', $report)
                                        <a href="{{ route('reports.edit', $report) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-report', $report)
                                        <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Hapus laporan ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Menampilkan {{ $reports->firstItem() }} - {{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
                                </small>
                            </div>
                            <div>
                                {{ $reports->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
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
</div>

<style>
    .report-item {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.3s ease;
    }
    
    .report-item:hover {
        background-color: #f9fafb;
    }
    
    .report-item:last-child {
        border-bottom: none;
    }
    
    .avatar-placeholder {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-progress {
        background: #fef3c7;
        color: #d97706;
    }
    
    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }
</style>

<script>
    // Enhanced filter functionality
    function filterReports() {
        const searchTerm = document.getElementById('search').value.toLowerCase();
        const statusFilter = document.getElementById('status_filter').value;
        const dateFilter = document.getElementById('date_filter').value;
        const userFilter = document.getElementById('user_filter') ? document.getElementById('user_filter').value : '';
        
        const reports = document.querySelectorAll('.report-item');
        const currentUserId = {{ auth()->id() }};
        
        let visibleCount = 0;
        
        reports.forEach(report => {
            const status = report.getAttribute('data-status');
            const date = report.getAttribute('data-date');
            const searchText = report.getAttribute('data-search');
            const userId = parseInt(report.getAttribute('data-user-id'));
            
            const matchesSearch = searchTerm === '' || searchText.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesDate = !dateFilter || date === dateFilter;
            
            // User filter logic for admin
            let matchesUser = true;
            if (userFilter === 'my_reports') {
                matchesUser = userId === currentUserId;
            } else if (userFilter === 'other_reports') {
                matchesUser = userId !== currentUserId;
            }
            
            if (matchesSearch && matchesStatus && matchesDate && matchesUser) {
                report.style.display = 'block';
                visibleCount++;
            } else {
                report.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        const noResults = document.getElementById('no-results');
        if (visibleCount === 0) {
            if (!noResults) {
                const noResultsDiv = document.createElement('div');
                noResultsDiv.id = 'no-results';
                noResultsDiv.className = 'text-center py-5';
                noResultsDiv.innerHTML = `
                    <div class="mb-3">
                        <i class="fas fa-search fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Tidak ada laporan yang sesuai</h5>
                    <p class="text-muted">Coba ubah filter pencarian Anda</p>
                `;
                const listGroup = document.querySelector('.list-group');
                if (listGroup) {
                    listGroup.appendChild(noResultsDiv);
                }
            }
        } else if (noResults) {
            noResults.remove();
        }
    }
    
    function resetFilters() {
        document.getElementById('search').value = '';
        document.getElementById('status_filter').value = '';
        document.getElementById('date_filter').value = '';
        if (document.getElementById('user_filter')) {
            document.getElementById('user_filter').value = '';
        }
        filterReports();
    }
    
    // Add event listeners setelah DOM fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan elements exist sebelum add event listeners
        const searchEl = document.getElementById('search');
        const statusFilterEl = document.getElementById('status_filter');
        const dateFilterEl = document.getElementById('date_filter');
        const userFilterEl = document.getElementById('user_filter');
        
        if (searchEl) searchEl.addEventListener('input', filterReports);
        if (statusFilterEl) statusFilterEl.addEventListener('change', filterReports);
        if (dateFilterEl) dateFilterEl.addEventListener('change', filterReports);
        if (userFilterEl) userFilterEl.addEventListener('change', filterReports);
        
        // Initial filter
        setTimeout(filterReports, 100);
    });
</script>
@endsection