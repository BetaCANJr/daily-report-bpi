<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Custom validation rules untuk mencegah XSS
        Validator::extend('no_special_chars', function ($attribute, $value, $parameters, $validator) {
            return !preg_match('/[<>"\']|script|iframe|javascript|onload|onerror/i', $value);
        });

        Validator::replacer('no_special_chars', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute field contains forbidden characters.');
        });

        // Password policy validation
        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
        });

        Validator::replacer('strong_password', function ($message, $attribute, $rule, $parameters) {
            return 'Password must be at least 8 characters with uppercase, lowercase, number and special character.';
        });

        // Gunakan Bootstrap pagination
        Paginator::useBootstrap();
    }
}