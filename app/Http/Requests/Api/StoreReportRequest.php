<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => strip_tags($this->title ?? ''),
            'description' => strip_tags($this->description ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'fotos' => ['nullable', 'array', 'max:5'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede superar los 100 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'description.max' => 'La descripción no puede superar los 2000 caracteres.',
            'category_id.required' => 'Debes seleccionar una categoría.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'latitude.required' => 'La ubicación es obligatoria.',
            'longitude.required' => 'La ubicación es obligatoria.',
            'fotos.max' => 'Puedes subir máximo 5 fotos.',
            'fotos.*.image' => 'El archivo debe ser una imagen.',
            'fotos.*.mimes' => 'Solo se permiten PNG, JPG, JPEG o WebP.',
            'fotos.*.max' => 'Cada foto no puede superar los 5MB.',
        ];
    }

    /**
     * Respuesta JSON en caso de error de validación (para Tauri/API).
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'No tienes permiso para crear denuncias.',
            ], 403)
        );
    }
}
