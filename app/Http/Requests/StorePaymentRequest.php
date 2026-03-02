<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Payment::class);
    }

    public function rules(): array
    {
        return [
            'pilgrim_id' => ['required', 'integer', 'exists:pilgrims,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,transfer,tpe,mobile_money,cash_espece'],
            'status' => ['required', 'in:pending,completed,refunded'],
            'payment_date' => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'pilgrim_id' => 'pèlerin',
            'amount' => 'montant',
            'method' => 'mode de paiement',
            'payment_date' => 'date de paiement',
        ];
    }
}
