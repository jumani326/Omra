<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $payment = \App\Models\Payment::find($this->route('payment'));
        return $payment && Gate::allows('update', $payment);
    }

    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'method' => ['sometimes', 'in:cash,transfer,tpe,mobile_money,cash_espece'],
            'status' => ['sometimes', 'in:pending,completed,refunded'],
            'payment_date' => ['sometimes', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'montant',
            'method' => 'mode de paiement',
            'payment_date' => 'date de paiement',
        ];
    }
}
