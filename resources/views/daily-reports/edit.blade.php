@extends('layouts.user-nav')

@section('title', 'Edit Laporan - Daily Report LPK BPI')
@section('page-title', 'Edit Laporan')
@section('page-subtitle', 'Perbarui data laporan harian')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Tanggal Laporan -->
                        <div class="col-12 col-md-6 mb-3">
                            <label for="report_date" class="form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('report_date') is-invalid @enderror" 
                                   id="report_date" 
                                   name="report_date" 
                                   value="{{ old('report_date', $report->report_date->format('Y-m-d')) }}"
                                   required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-12 col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Pilih Status</option>
                                <option value="Dalam Pengerjaan" {{ old('status', $report->status) == 'Dalam Pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                                <option value="Selesai" {{ old('status', $report->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tugas Harian -->
                        <div class="col-12 mb-3">
                            <label for="tugas_harian" class="form-label">Tugas Harian <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('tugas_harian') is-invalid @enderror" 
                                   id="tugas_harian" 
                                   name="tugas_harian" 
                                   value="{{ old('tugas_harian', $report->tugas_harian) }}"
                                   placeholder="Contoh: Maintenance server, Update website, dll."
                                   required>
                            @error('tugas_harian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi Pekerjaan -->
                        <div class="col-12 mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="4" 
                                      placeholder="Jelaskan detail pekerjaan yang dilakukan..."
                                      required>{{ old('deskripsi', $report->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kendala -->
                        <div class="col-12 mb-3">
                            <label for="kendala" class="form-label">Kendala (Optional)</label>
                            <textarea class="form-control @error('kendala') is-invalid @enderror" 
                                      id="kendala" 
                                      name="kendala" 
                                      rows="3" 
                                      placeholder="Jelaskan kendala yang dihadapi (jika ada)...">{{ old('kendala', $report->kendala) }}</textarea>
                            @error('kendala')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Catatan Tambahan -->
                        <div class="col-12 mb-3">
                            <label for="catatan_tambahan" class="form-label">Catatan Tambahan (Optional)</label>
                            <textarea class="form-control @error('catatan_tambahan') is-invalid @enderror" 
                                      id="catatan_tambahan" 
                                      name="catatan_tambahan" 
                                      rows="3" 
                                      placeholder="Catatan tambahan lainnya...">{{ old('catatan_tambahan', $report->catatan_tambahan) }}</textarea>
                            @error('catatan_tambahan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current File -->
                        @if($report->bukti_file)
                        <div class="col-12 mb-3">
                            <label class="form-label">File Saat Ini</label>
                            <div>
                                <a href="{{ Storage::url($report->bukti_file) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-2"></i>Lihat File Saat Ini
                                </a>
                                <small class="text-muted ms-2">File yang sudah diupload</small>
                            </div>
                        </div>
                        @endif

                        <!-- Bukti File -->
                        <div class="col-12 mb-4">
                            <label for="bukti_file" class="form-label">
                                Bukti File Baru (Optional) 
                                @if($report->bukti_file)
                                <small class="text-muted">- Kosongkan jika tidak ingin mengubah file</small>
                                @endif
                            </label>
                            <input type="file" 
                                   class="form-control @error('bukti_file') is-invalid @enderror" 
                                   id="bukti_file" 
                                   name="bukti_file"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">
                                Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10MB.
                            </div>
                            @error('bukti_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-bpi">
                                    <i class="fas fa-save me-2"></i>Update Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .card {
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
</style>
@endsection