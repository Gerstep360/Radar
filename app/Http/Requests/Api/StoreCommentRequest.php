<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // IMPORTANTE: usar input() en vez de $this->content
        // $this->content en Symfony\Request devuelve el raw body JSON completo
        $rawContent = $this->input('content');
        if ($rawContent !== null) {
            $this->merge(['content' => strip_tags($rawContent)]);
        }
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El comentario es obligatorio.',
            'content.min' => 'El comentario debe tener al menos 3 caracteres.',
            'content.max' => 'El comentario no puede superar los 500 caracteres.',
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
}
