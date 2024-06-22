<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbthSalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'prospect_id' => 'required|integer|exists:prospects,id',
            'month' => 'required|string',
            'value' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'prospect_id.required' => 'The Prospect field is required.',
            'prospect_id.exists' => 'The selected Prospect is invalid.',
            'month.required' => 'The Month field is required.',
            'month.string' => 'The Month field must be a valid string.',
            'value.required' => 'The Value field is required.',
            'value.numeric' => 'The Value field must be a valid number',
        ];
    }
}
