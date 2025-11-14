<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyReportsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    public function index(): View
    {
        $reports = DailyReport::with('user')
            ->latest()
            ->paginate(10);

        return view('daily-reports.index', compact('reports'));
    }

    public function create(): View
    {
        return view('daily-reports.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_date' => ['required', 'date'],
            'tugas_harian' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'status' => ['required', 'in:Dalam Pengerjaan,Selesai'],
            'catatan_tambahan' => ['nullable', 'string'],
            'bukti_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        // Handle file upload
        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('bukti-files', 'public');
        }

        $validated['user_id'] = auth()->id();

        DailyReport::create($validated);

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dibuat!');
    }

    public function show(DailyReport $report): View
    {
        // Authorization - user can only view their own reports unless admin
        if (!Gate::allows('view-report', $report)) {
            abort(403, 'Unauthorized action.');
        }

        return view('daily-reports.show', compact('report'));
    }

    public function edit(DailyReport $report): View
    {
        if (!Gate::allows('update-report', $report)) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('daily-reports.edit', compact('report'));
    }

    public function update(Request $request, DailyReport $report): RedirectResponse
    {
        if (!Gate::allows('update-report', $report)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'report_date' => ['required', 'date'],
            'tugas_harian' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'status' => ['required', 'in:Dalam Pengerjaan,Selesai'],
            'catatan_tambahan' => ['nullable', 'string'],
            'bukti_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        // Handle file upload
        if ($request->hasFile('bukti_file')) {
            // Delete old file if exists
            if ($report->bukti_file) {
                Storage::disk('public')->delete($report->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('bukti-files', 'public');
        }

        $report->update($validated);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy(DailyReport $report): RedirectResponse
    {
        if (!Gate::allows('delete-report', $report)) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete file if exists
        if ($report->bukti_file) {
            Storage::disk('public')->delete($report->bukti_file);
        }
        
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dihapus!');
    }

    public function calendar(): View
    {
        $reports = DailyReport::with('user')
            ->where('user_id', auth()->id())
            ->get(['id', 'report_date', 'tugas_harian', 'status']);

        $calendarEvents = $reports->map(function ($report) {
            $color = $report->status == 'Selesai' ? '#10b981' : '#f59e0b';
            return [
                'id' => $report->id,
                'title' => $report->tugas_harian,
                'start' => $report->report_date->format('Y-m-d'),
                'color' => $color,
                'extendedProps' => [
                    'status' => $report->status,
                    'url' => route('reports.edit', $report)
                ]
            ];
        });

        return view('daily-reports.calendar', compact('calendarEvents'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Validasi format
        if (!in_array($format, ['csv', 'pdf', 'excel'])) {
            return back()->with('error', 'Format export tidak valid!');
        }

        // Validasi tanggal
        if ($startDate && $endDate && $startDate > $endDate) {
            return back()->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
        }

        $query = DailyReport::with('user')
            ->where('user_id', auth()->id())
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('report_date', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('report_date', '<=', $endDate);
            })
            ->orderBy('report_date', 'desc');

        $reports = $query->get();

        // Jika tidak ada data
        if ($reports->isEmpty()) {
            return back()->with('error', 'Tidak ada data laporan untuk diexport!');
        }

        $fileName = 'laporan-harian-' . now()->format('Y-m-d');

        switch ($format) {
            case 'pdf':
                try {
                    $pdf = PDF::loadView('exports.daily-reports-pdf', compact('reports'))
                              ->setPaper('a4', 'landscape');
                    
                    return $pdf->download($fileName . '.pdf');
                } catch (\Exception $e) {
                    return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
                }
                break;

            case 'excel':
                return Excel::download(new DailyReportsExport($reports), $fileName . '.xlsx');
                break;

            default:
                return $this->exportToCsv($reports, $fileName . '.csv');
                break;
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,export',
            'reports' => 'required|array',
            'reports.*' => 'exists:daily_reports,id'
        ]);

        $reports = DailyReport::whereIn('id', $request->reports)
                    ->where('user_id', auth()->id())
                    ->get();

        if ($reports->isEmpty()) {
            return redirect()->route('reports.index')->with('error', 'Tidak ada laporan yang dipilih!');
        }

        if ($request->action === 'delete') {
            foreach ($reports as $report) {
                if ($report->bukti_file) {
                    Storage::disk('public')->delete($report->bukti_file);
                }
                $report->delete();
            }
            return redirect()->route('reports.index')->with('success', count($reports) . ' laporan berhasil dihapus!');
        }

        // Export selected reports
        if ($request->action === 'export') {
            $format = $request->get('export_format', 'excel');
            $fileName = 'laporan-terpilih-' . now()->format('Y-m-d');

            switch ($format) {
                case 'pdf':
                    $pdf = PDF::loadView('exports.daily-reports-pdf', compact('reports'))
                              ->setPaper('a4', 'landscape');
                    return $pdf->download($fileName . '.pdf');
                    break;

                case 'csv':
                    return $this->exportToCsv($reports, $fileName . '.csv');
                    break;

                default:
                    return Excel::download(new DailyReportsExport($reports), $fileName . '.xlsx');
                    break;
            }
        }
    }

    public function statistics(): View
    {
        $user = auth()->user();
        
        // Basic stats
        $totalReports = DailyReport::where('user_id', $user->id)->count();
        $completedReports = DailyReport::where('user_id', $user->id)->where('status', 'Selesai')->count();
        $todayReports = DailyReport::where('user_id', $user->id)
            ->whereDate('report_date', today())
            ->count();
        
        // Monthly stats for chart
        $monthlyStats = DailyReport::where('user_id', $user->id)
            ->selectRaw('YEAR(report_date) as year, MONTH(report_date) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        // Status distribution
        $statusStats = DailyReport::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Recent activity
        $recentReports = DailyReport::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('daily-reports.statistics', compact(
            'totalReports', 
            'completedReports', 
            'todayReports',
            'monthlyStats',
            'statusStats',
            'recentReports'
        ));
    }

    public function chartData()
    {
        $user = auth()->user();
        
        $data = DailyReport::where('user_id', $user->id)
            ->selectRaw('DATE(report_date) as date, COUNT(*) as count')
            ->where('report_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'dates' => $data->pluck('date'),
            'counts' => $data->pluck('count')
        ]);
    }

    /**
     * Export data to CSV
     */
    private function exportToCsv($reports, $filename = null)
    {
        $filename = $filename ?: 'laporan-harian-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reports) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            // Header
            fputcsv($file, [
                'Tanggal Laporan',
                'Tugas Harian',
                'Deskripsi',
                'Kendala',
                'Status',
                'Catatan Tambahan',
                'Dibuat Oleh',
                'Tanggal Dibuat'
            ]);

            // Data
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->report_date->format('d/m/Y'),
                    $report->tugas_harian,
                    $report->deskripsi,
                    $report->kendala ?? '-',
                    $report->status,
                    $report->catatan_tambahan ?? '-',
                    $report->user->name,
                    $report->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}