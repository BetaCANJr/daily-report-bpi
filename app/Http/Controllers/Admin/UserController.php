<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withTrashed()->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:admin,user'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'phone' => $request->phone,
            'role' => $request->role,
            'last_activity' => now(),
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user-photos', 'public');
            $userData['photo'] = $photoPath;
        }

        User::create($userData);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $reports = $user->dailyReports()->latest()->paginate(5);
        return view('admin.users.show', compact('user', 'reports'));
    }

    public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'jabatan' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:admin,user'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'phone' => $request->phone,
            'role' => $request->role,
            'last_activity' => now(),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $photoPath = $request->file('photo')->store('user-photos', 'public');
            $userData['photo'] = $photoPath;
        }

        $user->update($userData);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Delete user photo if exists
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users')
            ->with('success', 'User restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Delete user photo if exists
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->forceDelete();

        return redirect()->route('admin.users')
            ->with('success', 'User permanently deleted.');
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        // Prevent changing your own role
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:make_admin,make_user,delete,restore,force_delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;

        switch ($request->action) {
            case 'make_admin':
                // Prevent changing your own role
                if (in_array(auth()->id(), $userIds)) {
                    return redirect()->back()->with('error', 'You cannot change your own role.');
                }
                User::whereIn('id', $userIds)->update(['role' => 'admin']);
                $message = 'Selected users promoted to admin successfully.';
                break;
            
            case 'make_user':
                // Prevent changing your own role
                if (in_array(auth()->id(), $userIds)) {
                    return redirect()->back()->with('error', 'You cannot change your own role.');
                }
                User::whereIn('id', $userIds)->update(['role' => 'user']);
                $message = 'Selected users set as regular users successfully.';
                break;
            
            case 'delete':
                // Prevent deleting yourself
                if (in_array(auth()->id(), $userIds)) {
                    return redirect()->back()->with('error', 'You cannot delete your own account.');
                }
                User::whereIn('id', $userIds)->delete();
                $message = 'Selected users deleted successfully.';
                break;
            
            case 'restore':
                User::withTrashed()->whereIn('id', $userIds)->restore();
                $message = 'Selected users restored successfully.';
                break;
            
            case 'force_delete':
                // Prevent deleting yourself
                if (in_array(auth()->id(), $userIds)) {
                    return redirect()->back()->with('error', 'You cannot delete your own account.');
                }
                $users = User::withTrashed()->whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    if ($user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }
                    $user->forceDelete();
                }
                $message = 'Selected users permanently deleted.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}