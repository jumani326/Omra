<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreVisaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Visa::class);
    }

    public function rules(): array
    {
        return [
            'pilgrim_id' => ['required', 'integer', 'exists:pilgrims,id'],
            'status' => ['required', 'in:not_submitted,submitted,processing,approved,refused'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'expiry_date' => ['nullable', 'date'],
            'refusal_reason' => ['nullable', 'string', 'max:1000'],
            'documents_upload.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'pilgrim_id' => 'pèlerin',
            'reference_no' => 'référence',
            'expiry_date' => 'date d\'expiration',
            'refusal_reason' => 'motif de refus',
        ];
    }
}
