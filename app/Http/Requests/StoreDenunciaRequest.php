<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDenunciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La seguridad real va en la Policy
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            // Fotos múltiples: máximo 5 fotos de 5MB cada una
            'fotos' => ['nullable', 'array', 'max:5'],
            'fotos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'description.required' => 'La descripción es obligatoria',
            'description.min' => 'La descripción debe tener al menos 20 caracteres',
            'category_id.required' => 'Debes seleccionar una categoría',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'latitude.required' => 'La ubicación es obligatoria',
            'longitude.required' => 'La ubicación es obligatoria',
            'fotos.max' => 'Puedes subir máximo 5 fotos',
            'fotos.*.image' => 'El archivo debe ser una imagen',
            'fotos.*.mimes' => 'Solo se permiten imágenes JPG, JPEG o PNG',
            'fotos.*.max' => 'Cada foto no puede superar los 5MB',
        ];
    }
}