<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $report_date
 * @property string $tugas_harian
 * @property string $deskripsi
 * @property string|null $kendala
 * @property string $status
 * @property string|null $catatan_tambahan
 * @property string|null $bukti_file
 * @property string|null $file_location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereBuktiFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereCatatanTambahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereFileLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereKendala($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereTugasHarian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReport withoutTrashed()
 * @mixin \Eloquent
 */
class DailyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'report_date',
        'tugas_harian',
        'deskripsi',
        'kendala',
        'status',
        'catatan_tambahan',
        'bukti_file',
        'file_location',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('report_date', today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Selesai');
    }
}