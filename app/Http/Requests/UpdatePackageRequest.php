<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('package'));
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('is_published')) {
            $this->merge(['is_published' => false]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:economic,standard,premium,vip',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'slots' => 'required|integer|min:1',
            'is_published' => 'sometimes|boolean',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'hotel_mecca_id' => 'nullable|exists:hotels,id',
            'hotel_medina_id' => 'nullable|exists:hotels,id',
            'nights_mecca' => 'required|integer|min:1',
            'nights_medina' => 'required|integer|min:0',
        ];
    }
}
