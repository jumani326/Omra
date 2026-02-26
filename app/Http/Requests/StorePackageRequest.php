<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Package::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:economic,standard,premium,vip',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'slots' => 'required|integer|min:1',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
            'hotel_mecca_id' => 'nullable|exists:hotels,id',
            'hotel_medina_id' => 'nullable|exists:hotels,id',
            'nights_mecca' => 'required|integer|min:1',
            'nights_medina' => 'required|integer|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }
}
