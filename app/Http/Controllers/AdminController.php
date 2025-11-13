<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function users(): View
    {
        $users = User::withCount('dailyReports')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function reports(): View
    {
        $reports = DailyReport::with('user')->latest()->paginate(20);
        return view('admin.reports', compact('reports'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        // Prevent user from changing their own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroyUser(User $user)
    {
        // Prevent user from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}