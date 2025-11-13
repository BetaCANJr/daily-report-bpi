@extends('layouts.admin-nav')

@section('title', 'User Management - LPK BPI')

@section('page-title', 'User Management')
@section('page-subtitle', 'Manage system users and permissions')

@section('content')
<div class="chart-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">All Users</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
    </div>

    <!-- Bulk Actions -->
    <form id="bulkForm" action="{{ route('admin.users.bulk-action') }}" method="POST" class="mb-3">
        @csrf
        <div class="row align-items-center">
            <div class="col-md-6">
                <select name="action" class="form-select form-select-sm" required>
                    <option value="">Bulk Actions</option>
                    <option value="make_admin">Make Admin</option>
                    <option value="make_user">Make User</option>
                    <option value="delete">Move to Trash</option>
                    <option value="restore">Restore</option>
                    <option value="force_delete">Permanently Delete</option>
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-sm btn-outline-primary" id="applyBulkAction">
                    Apply
                </button>
            </div>
        </div>
    </form>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="50">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>User</th>
                    <th>Email & Phone</th>
                    <th>Position</th>
                    <th>Role</th>
                    <th>Last Activity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="{{ $user->trashed() ? 'table-danger' : '' }}">
                    <td>
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox"
                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                @if($user->photo)
                                    <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" 
                                         class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                    <span class="badge bg-info ms-1">You</span>
                                @endif
                                @if($user->trashed())
                                    <span class="badge bg-danger ms-1">Deleted</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $user->email }}</div>
                        <small class="text-muted">{{ $user->phone }}</small>
                    </td>
                    <td>{{ $user->jabatan }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $user->last_activity ? $user->last_activity->diffForHumans() : 'Never' }}
                        </small>
                    </td>
                    <td>
                        @if($user->trashed())
                            <span class="badge bg-danger">Deleted</span>
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$user->trashed())
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            @else
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" 
                                            onclick="return confirm('Are you sure you want to restore this user?')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure? This action cannot be undone.')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bulk selection
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkForm = document.getElementById('bulkForm');

        selectAll.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = selectAll.checked;
                }
            });
        });

        bulkForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one user.');
                return false;
            }
        });
    });
</script>
@endsection