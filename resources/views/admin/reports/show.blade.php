@extends('layouts.admin-nav')

@section('title', 'Report Details - LPK BPI')

@section('page-title', 'Report Details')
@section('page-subtitle', 'View report information')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="chart-container">
            <!-- Report Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Report Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>User:</strong>
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div>{{ $report->user->name }}</div>
                                    <small class="text-muted">{{ $report->user->jabatan }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Report Date:</strong>
                            <div class="mt-1">{{ $report->report_date->format('F d, Y') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <div class="mt-1">
                                <span class="badge 
                                    @if($report->status == 'Selesai') bg-success
                                    @elseif($report->status == 'Dalam Pengerjaan') bg-warning
                                    @else bg-secondary @endif">
                                    {{ $report->status }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong>
                            <div class="mt-1">{{ $report->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Daily Task:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {{ $report->tugas_harian }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {{ $report->deskripsi ?? 'No description provided' }}
                        </div>
                    </div>

                    @if($report->kendala)
                    <div class="mb-3">
                        <strong>Kendala:</strong>
                        <div class="mt-1 p-3 bg-warning bg-opacity-10 rounded">
                            {{ $report->kendala }}
                        </div>
                    </div>
                    @endif

                    @if($report->catatan_tambahan)
                    <div class="mb-3">
                        <strong>Catatan Tambahan:</strong>
                        <div class="mt-1 p-3 bg-info bg-opacity-10 rounded">
                            {{ $report->catatan_tambahan }}
                        </div>
                    </div>
                    @endif

                    @if($report->bukti_file)
                    <div class="mb-3">
                        <strong>Attachment:</strong>
                        <div class="mt-2">
                            <a href="{{ Storage::url($report->bukti_file) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Download Attachment
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="chart-container">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.edit', $report->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Report
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Report Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Report Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Created</small>
                        <div>{{ $report->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Last Updated</small>
                        <div>{{ $report->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Duration</small>
                        <div>{{ $report->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection