<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CategoryController - Gestión de categorías de denuncias.
 * 
 * Responsabilidades:
 * - Listar categorías activas (para selects/forms)
 * - CRUD de categorías (solo admin)
 */
class CategoryController extends Controller
{
    /**
     * Listar todas las categorías activas.
     * Usado para selects en formularios de crear/editar denuncia.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'priority', 'icon']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Crear nueva categoría (solo admin).
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless(auth()->user()?->can('gestionar denuncias'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'priority' => 'nullable|integer|min:1|max:10',
            'icon' => 'nullable|string|max:50',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'priority' => $validated['priority'] ?? 1,
            'icon' => $validated['icon'] ?? 'flag',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada',
            'data' => $category
        ], 201);
    }

    /**
     * Actualizar categoría (solo admin).
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        abort_unless(auth()->user()?->can('gestionar denuncias'), 403);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:categories,name,' . $category->id,
            'priority' => 'sometimes|integer|min:1|max:10',
            'icon' => 'sometimes|string|max:50',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada',
            'data' => $category
        ]);
    }

    /**
     * Eliminar categoría (solo admin).
     * Solo si no tiene denuncias asociadas.
     */
    public function destroy(Category $category): JsonResponse
    {
        abort_unless(auth()->user()?->can('gestionar denuncias'), 403);

        // Verificar que no tenga denuncias
        if ($category->reports()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: tiene denuncias asociadas'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada'
        ]);
    }
}
