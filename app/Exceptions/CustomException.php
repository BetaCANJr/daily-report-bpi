<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomException extends Exception
{
    protected $userMessage;
    protected $errorCode;

    public function __construct(string $message = "", string $userMessage = "Something went wrong", int $errorCode = 500)
    {
        parent::__construct($message);
        $this->userMessage = $userMessage;
        $this->errorCode = $errorCode;
    }

    /**
     * Report the exception (logging)
     */
    public function report(): void
    {
        // Log ke channel khusus daily_reports
        \Log::channel('daily_reports')->error('Custom Exception: ' . $this->getMessage(), [
            'exception' => $this,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString()
        ]);
    }

    /**
     * Render the exception into HTTP response
     */
    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->userMessage,
                'error_code' => $this->errorCode
            ], $this->errorCode);
        }

        return response()->view('errors.custom', [
            'message' => $this->userMessage,
            'errorCode' => $this->errorCode
        ], $this->errorCode);
    }
}