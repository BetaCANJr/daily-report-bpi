<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get reports for current user only
        $reports = Report::where('user_id', Auth::id())
                        ->latest()
                        ->paginate(10);
        
        return view('reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        try {
            $report = Report::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'work_done' => $request->work_done,
                'challenges' => $request->challenges,
                'next_plan' => $request->next_plan,
                'report_date' => $request->report_date,
                'status' => 'draft'
            ]);

            return redirect()->route('reports.index')
                           ->with('success', 'Report created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create report: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        // Check if user owns this report
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        // Check if user owns this report
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('reports.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        // Check if user owns this report
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $report->update([
                'title' => $request->title,
                'description' => $request->description,
                'work_done' => $request->work_done,
                'challenges' => $request->challenges,
                'next_plan' => $request->next_plan,
                'report_date' => $request->report_date,
                'status' => $request->status ?? 'draft'
            ]);

            return redirect()->route('reports.index')
                           ->with('success', 'Report updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update report: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        // Check if user owns this report
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $report->delete();

            return redirect()->route('reports.index')
                           ->with('success', 'Report deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to delete report: ' . $e->getMessage());
        }
    }

    /**
     * Submit report (change status to submitted)
     */
    public function submit(Report $report)
    {
        // Check if user owns this report
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $report->update(['status' => 'submitted']);

            return redirect()->route('reports.index')
                           ->with('success', 'Report submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to submit report: ' . $e->getMessage());
        }
    }
}