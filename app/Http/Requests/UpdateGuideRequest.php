<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('guide'));
    }

    public function rules(): array
    {
        $agencyId = auth()->user()->agence_id ?? auth()->user()->branch?->agency_id;
        $guide = $this->route('guide');
        $userId = $guide->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'group_id' => [
                'nullable',
                'integer',
                Rule::exists('groups', 'id')->where('agency_id', $agencyId),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => 'groupe de pèlerins',
        ];
    }
}
