<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $report = $this->route('report');

        // Dueño del reporte o admin/moderador
        return $user && ($user->id === $report?->user_id || $user->isAdmin() || $user->isModerator());
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'string', 'min:10', 'max:2000'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => strip_tags($this->title)]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description)]);
        }
    }

    public function messages(): array
    {
        return [
            'title.max' => 'El título no puede superar los 100 caracteres.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'description.max' => 'La descripción no puede superar los 2000 caracteres.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }

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
                'message' => 'No tienes permiso para editar esta denuncia.',
            ], 403)
        );
    }
}
