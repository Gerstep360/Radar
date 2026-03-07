<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API CategoryController — Categorías de denuncias (REST para Tauri).
 */
class CategoryController extends Controller
{
    /**
     * Listar todas las categorías activas.
     * Cacheado por 1 hora (las categorías cambian poco).
     */
    public function index(): JsonResponse
    {
        $categories = cache()->remember('api_categories', 3600, function () {
            return Category::query()
                ->orderByDesc('priority')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Crear categoría (solo admin).
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->isModerator()), 403, 'No autorizado.');

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
            //'icon' => 'nullable|string|max:50',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            //'icon' => $validated['icon'] ?? null,
            'priority' => $validated['priority'] ?? 1,
        ]);

        cache()->forget('api_categories');

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Actualizar categoría (solo admin).
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->isModerator()), 403, 'No autorizado.');

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
            //'icon' => 'nullable|string|max:50',
            'priority' => 'sometimes|integer|min:1|max:10',
        ]);

        $category->update($validated);
        cache()->forget('api_categories');

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada.',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Eliminar categoría (solo admin, sin denuncias asociadas).
     */
    public function destroy(Request $request, Category $category): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->isModerator()), 403, 'No autorizado.');

        if ($category->reports()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: tiene denuncias asociadas.',
            ], 422);
        }

        $category->delete();
        cache()->forget('api_categories');

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada.',
        ]);
    }
}
