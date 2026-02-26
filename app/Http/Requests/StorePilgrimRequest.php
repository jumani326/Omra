<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePilgrimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Pilgrim::class);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:pilgrims,email',
            'phone' => 'required|string|max:20',
            'passport_no' => 'required|string|unique:pilgrims,passport_no',
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

    public function messages(): array
    {
        return [
            'passport_no.unique' => 'Ce numéro de passeport est déjà enregistré.',
            'email.unique' => 'Cet email est déjà utilisé.',
        ];
    }
}
