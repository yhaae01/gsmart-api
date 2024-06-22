<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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

    public function messages()
    {
        return [
            'files.required' => 'The File field is required.',
            'files.array' => 'The File field must be an array.',
            'files.*.required' => 'The File is required.',
            'files.*.file' => 'The uploaded content must be a file.',
            'files.*.mimes' => 'Allowed File types are jpeg, jpg, png, pdf, doc, xls, ppt, and eml.',
            'files.*.max' => 'The File must not be greater than 5MB.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpeg,jpg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,eml|max:5120',
            'sales_id' => 'required|integer|exists:sales,id',
            'requirement_id' => 'required|integer|exists:requirements,id',
        ];
    }
}
