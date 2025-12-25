<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Report;
use App\Events\CommentAdded;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Obtener comentarios de un reporte
     */
    public function index(Report $report): JsonResponse
    {
        $comments = $report->comments()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'content' => $c->content,
                'is_official' => $c->is_official,
                'user' => [
                    'name' => $c->user->name,
                    'initial' => substr($c->user->name, 0, 1),
                ],
                'created_at' => $c->created_at->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $comments,
            'count' => $comments->count(),
        ]);
    }

    /**
     * Agregar comentario a un reporte
     */
    public function store(Request $request, Report $report): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|min:3|max:500',
        ]);

        $user = auth()->user();
        
        // Determinar si es comentario oficial (moderador o admin)
        $isOfficial = $user->hasRole(['admin', 'moderador']);

        $comment = Comment::create([
            'user_id' => $user->id,
            'report_id' => $report->id,
            'content' => $request->content,
            'is_official' => $isOfficial,
        ]);

        // 🔴 Broadcast en tiempo real
        broadcast(new CommentAdded($comment))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Comentario agregado',
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'is_official' => $comment->is_official,
                'user' => [
                    'name' => $user->name,
                    'initial' => substr($user->name, 0, 1),
                ],
                'created_at' => $comment->created_at->diffForHumans(),
            ],
        ], 201);
    }

    /**
     * Eliminar comentario (solo autor o admin)
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $user = auth()->user();

        if ($comment->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado',
        ]);
    }
}
