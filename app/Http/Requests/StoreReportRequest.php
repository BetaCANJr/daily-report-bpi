<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|no_special_chars',
            'content' => 'required|string|no_special_chars',
            'report_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:draft,submitted,reviewed,approved'
        ];
    }

    public function messages(): array
    {
        return [
            'title.no_special_chars' => 'Title contains forbidden characters.',
            'content.no_special_chars' => 'Content contains forbidden characters.',
            'report_date.before_or_equal' => 'Report date cannot be in the future.',
            'status.in' => 'Invalid status selected.'
        ];
    }
}