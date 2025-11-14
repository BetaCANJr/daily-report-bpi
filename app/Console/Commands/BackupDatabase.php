<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database daily';

    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        $backupPath = storage_path("app/backups/" . $filename);
        
        // Create backups directory if not exists
        if (!file_exists(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }

        // MySQL dump command
        $command = "mysqldump --user=" . env('DB_USERNAME') .
                  " --password=" . env('DB_PASSWORD') .
                  " --host=" . env('DB_HOST') .
                  " " . env('DB_DATABASE') .
                  " > " . $backupPath;

        $returnVar = NULL;
        $output = NULL;
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("Database backup successful: " . $filename);
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