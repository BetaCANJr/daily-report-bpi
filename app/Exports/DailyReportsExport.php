<?php

namespace App\Exports;

use App\Models\DailyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'Tanggal Laporan',
            'Tugas Harian',
            'Deskripsi',
            'Kendala',
            'Status',
            'Catatan Tambahan',
            'Dibuat Pada'
        ];
    }

    public function map($report): array
    {
        return [
            $report->report_date->format('d/m/Y'),
            $report->tugas_harian,
            $report->deskripsi,
            $report->kendala ?? '-',
            $report->status,
            $report->catatan_tambahan ?? '-',
            $report->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:G' => ['alignment' => ['wrapText' => true]]
        ];
    }
}