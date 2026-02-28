<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateVisaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $visa = \App\Models\Visa::find($this->route('visa'));
        return $visa && Gate::allows('update', $visa);
    }

    public function rules(): array
    {
        return [
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
            'reference_no' => 'référence',
            'expiry_date' => 'date d\'expiration',
            'refusal_reason' => 'motif de refus',
        ];
    }
}
