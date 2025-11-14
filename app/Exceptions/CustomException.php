<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

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

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function report(): void
    {
        \Log::channel('daily_reports')->error('Custom Exception: ' . $this->getMessage(), [
            'exception' => $this,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString()
        ]);
    }

    public function render(Request $request)
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