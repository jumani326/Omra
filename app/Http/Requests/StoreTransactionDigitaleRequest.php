<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreTransactionDigitaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', \App\Models\TransactionDigitale::class);
    }

    public function rules(): array
    {
        return [
            'compte_marchand_id' => ['required', 'integer', 'exists:compte_marchands,id'],
            'pilgrim_id' => ['nullable', 'integer', 'exists:pilgrims,id'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'client_nom' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:en_attente,valide,refuse'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'compte_marchand_id' => 'compte marchand',
            'pilgrim_id' => 'pèlerin',
            'client_nom' => 'nom du client',
        ];
    }
}
