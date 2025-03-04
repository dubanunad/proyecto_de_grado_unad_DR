<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
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
            'branch_id' => 'required|exists:branches,id', // Asegura que la sucursal exista
            'client_id' => 'required|exists:clients,id', // Asegura que el cliente exista
            'plan_id' => 'nullable|exists:plans,id', // El plan es opcional, pero si se proporciona, debe existir
            'neighborhood' => 'required|string|max:255', // La colonia debe ser una cadena de texto con un máximo de 255 caracteres
            'address' => 'required|string|max:255', // La dirección debe ser una cadena de texto con un máximo de 255 caracteres
            'home_type' => 'required|string|in:Propia,En Arriendo,Otro',
            'nap_port' => 'nullable|string', // El puerto NAP es opcional
            'cpe_sn' => 'nullable|string|max:20|unique:contracts,cpe_sn', // El número de serie del CPE debe ser único y no superar los 20 caracteres
            'user_pppoe' => 'nullable|string|unique:contracts,user_pppoe', // El nombre de usuario PPPoE es opcional, pero debe ser único si se proporciona
            'password_pppoe' => 'nullable|string', // La contraseña PPPoE es opcional
            'status' => 'required|string|in:Por Instalar,Activo,Cortado,Retirado,Por Reconectar', // El estado es opcional pero debe ser uno de los valores predefinidos
            'social_stratum' => 'required|string', // El estrato social debe ser una cadena de texto
            'permanence_clause' => 'nullable|integer', // La cláusula de permanencia es opcional, pero si se proporciona, debe ser un número entero
            'ssid_wifi' => 'nullable|string', // El SSID del WiFi es opcional
            'password_wifi' => 'nullable|string', // La contraseña del WiFi es opcional
            'comment' => 'nullable|string', // El comentario es opcional
            'user_id' => 'nullable|exists:users,id', // El ID del usuario debe existir, por defecto es 1
            'department' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
        ];
    }
    /**
     * Get the custom messages for the validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'branch_id.required' => 'La sucursal es obligatoria.',
            'branch_id.exists' => 'La sucursal seleccionada no existe.',
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'plan_id.exists' => 'El plan seleccionado no existe.',
            'neighborhood.required' => 'La colonia es obligatoria.',
            'address.required' => 'La dirección es obligatoria.',
            'home_type.required' => 'El tipo de vivienda es obligatorio',
            'cpe_sn.required' => 'El número de serie del CPE es obligatorio.',
            'cpe_sn.unique' => 'El número de serie del CPE ya está registrado.',
            'user_pppoe.unique' => 'El nombre de usuario PPPoE ya está en uso.',
            'status.in' => 'El estado seleccionado no es válido.',
            'social_stratum.required' => 'El estrato social es obligatorio.',
            'permanence_clause.integer' => 'La cláusula de permanencia debe ser un número entero.',
            'ssid_wifi.string' => 'El SSID del WiFi debe ser una cadena de texto.',
            'password_wifi.string' => 'La contraseña del WiFi debe ser una cadena de texto.',
        ];
    }

    /**
     * Get the attributes that should be used in the validation messages.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'branch_id' => 'sucursal',
            'client_id' => 'cliente',
            'plan_id' => 'plan de servicio',
            'neighborhood' => 'barrio',
            'address' => 'dirección',
            'home_type' => 'tipo de vivienda',
            'nap_port' => 'puerto NAP',
            'cpe_sn' => 'número de serie del CPE',
            'user_pppoe' => 'nombre de usuario PPPoE',
            'password_pppoe' => 'contraseña PPPoE',
            'status' => 'estado',
            'social_stratum' => 'estrato social',
            'permanence_clause' => 'cláusula de permanencia',
            'ssid_wifi' => 'SSID WiFi',
            'password_wifi' => 'contraseña WiFi',
            'comment' => 'comentario',
            'user_id' => 'usuario',
        ];
    }
}
