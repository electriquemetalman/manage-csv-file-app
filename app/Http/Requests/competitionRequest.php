<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class competitionRequest extends FormRequest
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
            //
            'title' => 'required|min:4',
            'litel_description' => 'required|min:10',
            'long_description' => 'required|min:20',
            'evaluation_text' => 'required|min:10',
            'ref_file' => 'required',
        ];
    }
}
