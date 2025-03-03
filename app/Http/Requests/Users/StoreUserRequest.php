<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:empleado,cliente',
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
            'required' => 'El campo :attribute es requerido',
            'email.unique' => 'El email ya está registrado',
            'role.in' => 'Asignacion de rol no permitida',
            'confirmed' => 'Las contraseñas no coinciden',
            'lowercase' => 'El email debe estar en minúsculas',
            'max' => 'El campo :attribute debe tener máximo :max caracteres',
            'min' => 'El campo :attribute debe tener mínimo :min caracteres',
        ];
    }
}
