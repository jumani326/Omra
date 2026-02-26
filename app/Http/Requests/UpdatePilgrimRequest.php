<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePilgrimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('pilgrim'));
    }

    public function rules(): array
    {
        $pilgrimId = $this->route('pilgrim')->id ?? $this->route('pilgrim');

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('pilgrims')->ignore($pilgrimId)],
            'phone' => 'required|string|max:20',
            'passport_no' => ['required', 'string', Rule::unique('pilgrims')->ignore($pilgrimId)],
            'nationality' => 'required|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'agent_id' => 'nullable|exists:users,id',
            'package_id' => 'nullable|exists:packages,id',
            'status' => 'nullable|in:registered,dossier_complete,visa_submitted,visa_approved,departed,returned',
            
            // Documents (optionnels)
            'documents.passport' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'documents.medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
