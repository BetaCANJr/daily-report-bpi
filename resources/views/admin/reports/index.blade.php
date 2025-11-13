<!-- resources/views/admin/reports/index.blade.php -->
@extends('layouts.app')

@section('title', 'Admin - Reports Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Reports Management</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($reports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Report Date</th>
                                <th>Activities</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $report->user->name }}</td>
                                    <td>{{ $report->report_date->format('M j, Y') }}</td>
                                    <td>{{ Str::limit($report->activities, 50) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $report->status === 'Selesai' ? 'success' : 'warning' }}">
                                            {{ $report->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-info">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $reports->links() }}
                </div>
            @else
                <p class="text-muted text-center py-4">No reports found.</p>
            @endif
        </div>
    </div>
</div>
@endsection