<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCompteMarchandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\CompteMarchand::class);
    }

    public function rules(): array
    {
        return [
            'nom_methode' => ['required', 'string', 'in:D-money,Waafi,MyCac'],
            'numero_compte' => ['required', 'string', 'max:100'],
            'nom_agence' => ['required', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'solde' => ['nullable', 'numeric', 'min:0'],
            'actif' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nom_methode' => 'méthode',
            'numero_compte' => 'numéro de compte',
            'nom_agence' => 'nom de l\'agence',
        ];
    }
}
