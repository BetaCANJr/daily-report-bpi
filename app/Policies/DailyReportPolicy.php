<?php

namespace App\Policies;

use App\Models\DailyReport;
use App\Models\User;

class DailyReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua user yang login bisa melihat laporan
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DailyReport $report): bool
    {
        return $user->isAdmin() || $user->id === $report->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Semua user yang login bisa buat laporan
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DailyReport $report): bool
    {
        return $user->isAdmin() || $user->id === $report->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DailyReport $report): bool
    {
        return $user->isAdmin() || $user->id === $report->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DailyReport $report): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DailyReport $report): bool
    {
        return $user->isAdmin();
    }
}