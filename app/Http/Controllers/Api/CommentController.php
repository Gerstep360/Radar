<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Events\CommentAdded;
use App\Events\NotificationCreated;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API CommentController — Comentarios en denuncias (REST para Tauri).
 */
class CommentController extends Controller
{
    /**
     * Listar comentarios de un reporte (paginados).
     *
     * Query params:
     *  - per_page: int (default 20, max 50)
     *  - cursor: string (para cursor pagination)
     */
    public function index(Request $request, Report $report): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 20), 50);

        $comments = $report->comments()
            ->with('user:id,name')
            ->latest()
            ->cursorPaginate($perPage);

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments),
            'meta' => [
                'per_page' => $comments->perPage(),
                'has_more' => $comments->hasMorePages(),
                'next_cursor' => $comments->nextCursor()?->encode(),
            ],
            'count' => $report->comments()->count(),
        ]);
    }

    /**
     * Agregar comentario a un reporte.
     */
    public function store(StoreCommentRequest $request, Report $report): JsonResponse
    {
        $user = $request->user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'report_id' => $report->id,
            'content' => $request->validated('content'),
            'is_official' => $user->isAdmin() || $user->isModerator(),
        ]);

        $comment->load('user:id,name');

        // Broadcast en tiempo real al canal del reporte
        broadcast(new CommentAdded($comment))->toOthers();

        // Notificar al dueño del reporte (si no es el mismo que comenta)
        if ($report->user_id !== $user->id) {
            $notification = Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'type' => 'comment',
                'title' => 'Nuevo comentario en tu denuncia',
                'message' => "{$user->name} comentó en \"{$report->title}\".",
                'data' => [
                    'comment_id' => $comment->id,
                    'commenter_name' => $user->name,
                    'report_title' => $report->title,
                    'is_official' => $comment->is_official,
                ],
            ]);

            event(new NotificationCreated($notification));
        }

        return response()->json([
            'success' => true,
            'message' => 'Comentario agregado.',
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Eliminar un comentario (solo autor o admin).
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if ($comment->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este comentario.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado.',
        ]);
    }
}
