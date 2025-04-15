<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RangoRequest extends FormRequest
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
            'reporte_anio' => 'required|numeric',
            'mes_inicial' => 'required|integer|min:1|max:12',
            'mes_final' => 'required|integer|min:1|max:12|gte:mes_inicial',
        ];
    }

    public function messages()
    {
        return [
            'reporte_anio' => 'El :attribute es incorrecto.',
            'mes_inicial' => 'El :attribute es incorrecto.',
            'mes_final.gte' => 'El :attribute no puede ser menor al mes inicial.',
            'mes_final' => 'El :attribute es incorrecto.'
        ];
    }

    public function attributes()
    {
        return [
            'reporte_anio' => 'año del reporte',
            'mes_inicial' => 'mes inicial',
            'mes_final' => 'mes final'
        ];
    }
}
