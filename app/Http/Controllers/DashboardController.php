<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Display user dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // Cache statistics for better performance (5 minutes)
        $cacheKey = "user_dashboard_{$user->id}_" . today()->format('Y-m-d');
        
        $stats = Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'totalReports' => DailyReport::where('user_id', $user->id)->count(),
                'todayReports' => DailyReport::where('user_id', $user->id)
                                ->whereDate('report_date', today())
                                ->count(),
                'completedReports' => DailyReport::where('user_id', $user->id)
                                    ->where('status', 'Selesai')
                                    ->count(),
                'progressReports' => DailyReport::where('user_id', $user->id)
                                    ->where('status', 'Dalam Pengerjaan')
                                    ->count(),
            ];
        });

        $recentReports = DailyReport::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentReports'));
    }

    /**
     * Display admin dashboard
     */
    public function admin(): View
    {
        // Cache admin statistics for better performance (5 minutes)
        $cacheKey = "admin_dashboard_" . today()->format('Y-m-d');
        
        $stats = Cache::remember($cacheKey, 300, function () {
            $totalUsers = User::count();
            $totalReports = DailyReport::count();
            $todayReports = DailyReport::whereDate('report_date', today())->count();
            $completedReports = DailyReport::where('status', 'Selesai')->count();
            
            return [
                'totalUsers' => $totalUsers,
                'totalReports' => $totalReports,
                'todayReports' => $todayReports,
                'activeUsers' => User::whereHas('dailyReports', function($query) {
                    $query->whereDate('report_date', today());
                })->count(),
                'completedReports' => $completedReports,
                'pendingReports' => DailyReport::where('status', 'Dalam Pengerjaan')->count(),
                'completionRate' => $totalReports > 0 ? round(($completedReports / $totalReports) * 100, 1) : 0,
            ];
        });

        // Chart Data - Last 7 days reports with caching
        $chartData = Cache::remember('admin_chart_data_'.today()->format('Y-m-d'), 300, function () {
            return $this->getChartData();
        });
        
        // Recent Activities with eager loading
        $recentReports = DailyReport::with('user')
            ->latest()
            ->take(8)
            ->get();
            
        $recentUsers = User::latest()
            ->take(6)
            ->get();

        // User Statistics with caching
        $userStats = Cache::remember('user_stats_'.today()->format('Y-m-d'), 300, function () use ($stats) {
            return [
                'adminCount' => User::where('role', 'admin')->count(),
                'userCount' => User::where('role', 'user')->count(),
                'reportsPerUser' => $stats['totalUsers'] > 0 ? round($stats['totalReports'] / $stats['totalUsers'], 1) : 0,
            ];
        });

        // Top performing users
        $topUsers = User::withCount(['dailyReports as completed_reports_count' => function($query) {
                $query->where('status', 'Selesai');
            }])
            ->orderBy('completed_reports_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'chartData', 
            'recentReports', 
            'recentUsers', 
            'userStats',
            'topUsers'
        ));
    }

    /**
     * Get chart data for admin dashboard
     */
    private function getChartData(): array
    {
        $dates = [];
        $totalCounts = [];
        $completedCounts = [];
        
        try {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateString = $date->format('Y-m-d');
                $dates[] = $date->format('M j');
                
                // Get total reports and completed reports for the day
                $totalCount = DailyReport::whereDate('report_date', $dateString)->count();
                $completedCount = DailyReport::whereDate('report_date', $dateString)
                                ->where('status', 'Selesai')
                                ->count();
                
                $totalCounts[] = $totalCount;
                $completedCounts[] = $completedCount;
            }
            
            return [
                'dates' => $dates,
                'total_counts' => $totalCounts,
                'completed_counts' => $completedCounts,
            ];
        } catch (\Exception $e) {
            // Fallback data jika ada error
            return [
                'dates' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'total_counts' => [0, 0, 0, 0, 0, 0, 0],
                'completed_counts' => [0, 0, 0, 0, 0, 0, 0],
            ];
        }
    }

    /**
     * Get weekly progress data for user dashboard
     */
    private function getUserWeeklyProgress(User $user): array
    {
        $cacheKey = "weekly_progress_{$user->id}_" . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $weeklyData = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateString = $date->format('Y-m-d');
                
                $dailyStats = DailyReport::where('user_id', $user->id)
                    ->whereDate('report_date', $dateString)
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as completed')
                    ->first();
                
                $weeklyData[] = [
                    'date' => $date->format('M j'),
                    'total' => $dailyStats->total ?? 0,
                    'completed' => $dailyStats->completed ?? 0,
                ];
            }
            
            return $weeklyData;
        });
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache()
    {
        $user = auth()->user();
        
        // Clear user-specific cache
        $cacheKey = "user_dashboard_{$user->id}_" . today()->format('Y-m-d');
        Cache::forget($cacheKey);
        
        // Clear admin cache jika user adalah admin
        if ($user->isAdmin()) {
            Cache::forget('admin_dashboard_' . today()->format('Y-m-d'));
            Cache::forget('admin_chart_data_'.today()->format('Y-m-d'));
            Cache::forget('user_stats_'.today()->format('Y-m-d'));
        }
        
        // Juga clear cache untuk fitur baru Phase 1
        Cache::forget('reports_statistics_' . $user->id);
        
        return back()->with('success', 'Dashboard cache cleared successfully.');
    }

    /**
     * Get stats for API (real-time updates)
     */
    public function getStats(): JsonResponse
    {
        $user = auth()->user();
        
        // Gunakan cache untuk performance, tapi dengan shorter TTL
        $cacheKey = "api_stats_{$user->id}_" . now()->format('Y-m-d_H');
        
        $stats = Cache::remember($cacheKey, 60, function () use ($user) {
            return [
                'totalReports' => DailyReport::where('user_id', $user->id)->count(),
                'todayReports' => DailyReport::where('user_id', $user->id)
                                ->whereDate('report_date', today())
                                ->count(),
                'completedReports' => DailyReport::where('user_id', $user->id)
                                    ->where('status', 'Selesai')
                                    ->count(),
                'progressReports' => DailyReport::where('user_id', $user->id)
                                    ->where('status', 'Dalam Pengerjaan')
                                    ->count(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get statistics data for reports statistics page (Phase 1)
     */
    public function getReportStatistics(): JsonResponse
    {
        $user = auth()->user();
        
        $cacheKey = "reports_statistics_{$user->id}_" . today()->format('Y-m-d');
        
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            $thirtyDaysAgo = Carbon::now()->subDays(30);
            
            // Daily activity for the last 30 days
            $dailyActivity = DailyReport::where('user_id', $user->id)
                ->where('report_date', '>=', $thirtyDaysAgo)
                ->selectRaw('DATE(report_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            // Status distribution
            $statusDistribution = DailyReport::where('user_id', $user->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
                
            return [
                'daily_activity' => $dailyActivity,
                'status_distribution' => $statusDistribution,
            ];
        });

        return response()->json($data);
    }

    /**
     * Get user activity data for charts (Phase 1)
     */
    public function getUserActivityData(): JsonResponse
    {
        $user = auth()->user();
        
        $cacheKey = "user_activity_{$user->id}_" . today()->format('Y-m-d');
        
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            $activityData = DailyReport::where('user_id', $user->id)
                ->selectRaw('DATE(report_date) as date, COUNT(*) as count')
                ->where('report_date', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'dates' => $activityData->pluck('date'),
                'counts' => $activityData->pluck('count')
            ];
        });

        return response()->json($data);
    }

    /**
     * Get dashboard overview for quick stats (Phase 1)
     */
    public function getDashboardOverview(): JsonResponse
    {
        $user = auth()->user();
        
        $cacheKey = "dashboard_overview_{$user->id}_" . today()->format('Y-m-d');
        
        $overview = Cache::remember($cacheKey, 300, function () use ($user) {
            // Recent reports count
            $recentReportsCount = DailyReport::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            // Completion rate
            $totalReports = DailyReport::where('user_id', $user->id)->count();
            $completedReports = DailyReport::where('user_id', $user->id)
                                ->where('status', 'Selesai')
                                ->count();
            $completionRate = $totalReports > 0 ? round(($completedReports / $totalReports) * 100, 1) : 0;

            // Average reports per day
            $firstReport = DailyReport::where('user_id', $user->id)->oldest()->first();
            $daysActive = $firstReport ? $firstReport->created_at->diffInDays(now()) : 1;
            $avgPerDay = $totalReports > 0 ? round($totalReports / max($daysActive, 1), 1) : 0;

            return [
                'recent_reports_count' => $recentReportsCount,
                'completion_rate' => $completionRate,
                'average_per_day' => $avgPerDay,
                'days_active' => $daysActive,
            ];
        });

        return response()->json($overview);
    }

    /**
     * Get system health status (Admin only - Phase 2)
     */
    public function getSystemHealth(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cacheKey = "system_health_" . today()->format('Y-m-d');
        
        $health = Cache::remember($cacheKey, 300, function () {
            // Database health
            $dbStatus = true;
            try {
                DailyReport::count();
            } catch (\Exception $e) {
                $dbStatus = false;
            }

            // Storage health
            $storagePath = storage_path();
            $storageFree = disk_free_space($storagePath);
            $storageTotal = disk_total_space($storagePath);
            $storageUsage = $storageTotal > 0 ? round(($storageTotal - $storageFree) / $storageTotal * 100, 1) : 0;

            // Cache health
            $cacheStatus = true;
            try {
                Cache::put('health_check', 'ok', 1);
                $cacheStatus = Cache::get('health_check') === 'ok';
            } catch (\Exception $e) {
                $cacheStatus = false;
            }

            return [
                'database' => $dbStatus,
                'cache' => $cacheStatus,
                'storage_usage' => $storageUsage,
                'storage_free' => round($storageFree / (1024 * 1024 * 1024), 2), // GB
                'storage_total' => round($storageTotal / (1024 * 1024 * 1024), 2), // GB
                'last_updated' => now()->toISOString(),
            ];
        });

        return response()->json($health);
    }

    /**
     * Get performance metrics (Phase 2)
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cacheKey = "performance_metrics_" . today()->format('Y-m-d');
        
        $metrics = Cache::remember($cacheKey, 600, function () { // 10 minutes cache
            // Response time metrics (simulated)
            $responseTimes = [
                'average' => rand(50, 200), // ms
                'min' => rand(20, 80),
                'max' => rand(150, 500),
            ];

            // Memory usage
            $memoryUsage = memory_get_usage(true);
            $memoryPeak = memory_get_peak_usage(true);

            // Query performance
            $queryCount = count(\DB::getQueryLog());

            return [
                'response_times' => $responseTimes,
                'memory_usage_mb' => round($memoryUsage / (1024 * 1024), 2),
                'memory_peak_mb' => round($memoryPeak / (1024 * 1024), 2),
                'query_count' => $queryCount,
                'uptime' => $this->getSystemUptime(),
                'last_updated' => now()->toISOString(),
            ];
        });

        return response()->json($metrics);
    }

    /**
     * Helper method to get system uptime
     */
    private function getSystemUptime(): string
    {
        // Simulate uptime calculation
        $startTime = config('app.start_time', now()->subDays(rand(1, 30)));
        $uptime = now()->diff($startTime);
        
        return $uptime->format('%a days, %h hours, %i minutes');
    }

    public function export(Request $request)
{
    $format = $request->get('format', 'csv'); // Default ke CSV
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    $query = DailyReport::with('user')
        ->where('user_id', auth()->id())
        ->when($startDate, function($q) use ($startDate) {
            return $q->whereDate('report_date', '>=', $startDate);
        })
        ->when($endDate, function($q) use ($endDate) {
            return $q->whereDate('report_date', '<=', $endDate);
        });

    $reports = $query->get();

    if ($format === 'pdf') {
        // PDF Export
        try {
            $pdf = PDF::loadView('exports.daily-reports-pdf', compact('reports'))
                      ->setPaper('a4', 'landscape');
            
            return $pdf->download('laporan-harian-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'PDF export error: ' . $e->getMessage());
        }
    }

    // Default ke CSV export (manual - no package needed)
    return $this->exportToCsv($reports);
}

/**
 * Manual CSV Export - TANPA PACKAGE
 */
private function exportToCsv($reports)
{
    $fileName = 'laporan-harian-' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0'
    ];

    $callback = function() use ($reports) {
        $file = fopen('php://output', 'w');
        
        // Add BOM for UTF-8 (agar Excel baca karakter special)
        fwrite($file, "\xEF\xBB\xBF");
        
        // Headers
        fputcsv($file, [
            'Tanggal Laporan',
            'Tugas Harian', 
            'Deskripsi',
            'Kendala',
            'Status',
            'Catatan Tambahan',
            'Dibuat Pada'
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
                $report->created_at->format('d/m/Y H:i')
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

/**
 * Update bulkAction method juga
 */
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

    if ($request->action === 'delete') {
        foreach ($reports as $report) {
            if ($report->bukti_file) {
                Storage::disk('public')->delete($report->bukti_file);
            }
            $report->delete();
        }
        return redirect()->route('reports.index')->with('success', count($reports) . ' laporan berhasil dihapus!');
    }

    // Export selected reports - PAKAI MANUAL CSV
    if ($request->action === 'export') {
        return $this->exportToCsv($reports);
    }
}
}