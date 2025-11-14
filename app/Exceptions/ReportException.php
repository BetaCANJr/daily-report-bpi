<?php

namespace App\Exceptions;

use Exception;

class ReportException extends CustomException
{
    public function __construct(string $message = "", string $userMessage = "Report operation failed", int $errorCode = 500)
    {
        parent::__construct($message, $userMessage, $errorCode);
    }

    /**
     * Custom reporting untuk Report-related errors
     */
    public function report(): void
    {
        \Log::channel('daily_reports')->error('Report Exception: ' . $this->getMessage(), [
            'exception' => $this,
            'timestamp' => now()->toISOString()
        ]);
    }
}