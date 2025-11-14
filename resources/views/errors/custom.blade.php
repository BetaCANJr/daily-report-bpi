@extends('layouts.app')

@section('title', 'Error - Daily Report BPI')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> System Error</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-bug fa-3x text-danger"></i>
                    </div>
                    <h5 class="card-title">Something went wrong</h5>
                    <p class="card-text">{{ $message ?? 'An unexpected error occurred. Our team has been notified.' }}</p>
                    
                    @if($errorCode ?? false)
                    <small class="text-muted">Error Code: {{ $errorCode }}</small>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary me-2">
                            <i class="fas fa-home"></i> Go Home
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection