<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
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
            'id_usuario' => 'sometimes|integer|nullable',
            'name' => 'sometimes|string|nullable',
            'email' => 'sometimes|email|nullable',
            'role' => 'sometimes|string|nullable',
            'paginated' => 'sometimes|boolean',
            'per_page' => 'sometimes|integer|min:5|max:100|exclude_if:paginated,false',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_usuario.integer' => 'El id_usuario debe ser un entero',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'email.email' => 'El email debe ser un correo electrónico',
            'role.string' => 'El rol debe ser una cadena de texto',
            'paginated.boolean' => 'El campo paginated debe ser un booleano.',
            'per_page.integer' => 'El campo per_page debe ser un número entero.',
            'per_page.min' => 'El campo per_page debe ser mínimo 5.',
            'per_page.max' => 'El campo per_page debe ser máximo 100.',
        ];
    }
}
