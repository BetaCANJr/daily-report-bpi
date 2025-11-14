<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database daily';

    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        
        // MySQL dump command (sesuaikan dengan environment)
        $command = "mysqldump --user=" . env('DB_USERNAME') .
                  " --password=" . env('DB_PASSWORD') .
                  " --host=" . env('DB_HOST') .
                  " " . env('DB_DATABASE') .
                  " > " . storage_path("app/backups/" . $filename);

        $returnVar = NULL;
        $output = NULL;
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("Database backup successful: " . $filename);
            
            // Cleanup old backups (keep only last 7 days)
            $this->cleanupOldBackups();
        } else {
            $this->error("Database backup failed");
            \Log::channel('daily_reports')->error('Database backup failed', [
                'return_var' => $returnVar,
                'output' => $output
            ]);
        }
    }

    private function cleanupOldBackups()
    {
        $files = glob(storage_path('app/backups/backup-*.sql'));
        
        foreach ($files as $file) {
            if (filemtime($file) < now()->subDays(7)->getTimestamp()) {
                unlink($file);
                $this->info("Deleted old backup: " . basename($file));
            }
        }
    }
}