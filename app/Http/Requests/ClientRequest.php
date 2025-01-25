<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'type_document' => 'required',
            'identity_number' => 'required|min:5|max:20',
            'name' => 'required|max:40',
            'last_name'  => 'required|max:40',
            'type_client' => 'required',
            'number_phone' => 'required|max:20',
            'aditional_phone' => 'max:40',
            'email' => 'required',
            'birthday' => 'required',
        ];
    }
}
