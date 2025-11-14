<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && ($request->routeIs('register') || $request->routeIs('password.update'))) {
            $password = $request->input('password');
            
            if ($password && !$this->isStrongPassword($password)) {
                return redirect()->back()
                    ->withErrors(['password' => 'Password must be at least 8 characters with uppercase, lowercase, number and special character.'])
                    ->withInput();
            }
        }

        return $next($request);
    }

    private function isStrongPassword($password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }
}