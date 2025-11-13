<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = DailyReport::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(DailyReport $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, DailyReport $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:Selesai,Dalam Proses,Menunggu',
        ]);

        $report->update($validated);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully.');
    }

    public function destroy(DailyReport $report)
    {
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action === 'delete') {
            DailyReport::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Selected reports deleted.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    public function export()
    {
        // Simple export logic for now
        $reports = DailyReport::with('user')->get();
        
        return response()->json($reports);
    }

    public function getStatistics()
    {
        $stats = [
            'total' => DailyReport::count(),
            'completed' => DailyReport::where('status', 'Selesai')->count(),
            'pending' => DailyReport::where('status', '!=', 'Selesai')->count(),
        ];

        return response()->json($stats);
    }
}