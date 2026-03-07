<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $report = $this->route('report');

        return $user && ($user->id === $report?->user_id || $user->isAdmin() || $user->isModerator());
    }

    public function rules(): array
    {
        return [
            'media' => ['required', 'array', 'min:1', 'max:5'],
            'media.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'media.required' => 'Debes subir al menos una imagen.',
            'media.max' => 'Puedes subir máximo 5 imágenes.',
            'media.*.image' => 'El archivo debe ser una imagen.',
            'media.*.mimes' => 'Solo se permiten PNG, JPG, JPEG o WebP.',
            'media.*.max' => 'Cada imagen no puede superar los 5MB.',
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
                'message' => 'No tienes permiso para subir archivos a esta denuncia.',
            ], 403)
        );
    }
}
