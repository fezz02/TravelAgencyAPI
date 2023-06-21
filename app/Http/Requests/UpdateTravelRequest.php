<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'is_public' => ['required', 'sometimes', 'boolean'],
            'name' => ['required', 'sometimes'],
            'description' => ['required', 'sometimes'],
            'number_of_days' => ['required', 'sometimes', 'min:1', 'max:365'],
        ];
    }
}
