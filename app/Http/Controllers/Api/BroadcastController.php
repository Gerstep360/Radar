<?php

namespace App\Http\Controllers\Api;

use App\Models\Broadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API BroadcastController — Consultar broadcasts enviados a todos los clientes.
 */
class BroadcastController extends \App\Http\Controllers\Controller
{
    /**
     * Devuelve el broadcast activo más reciente (no expirado).
     * - La app llama a esto al iniciar para ver si hay algo pendiente.
     */
    public function latest(): JsonResponse
    {
        try {
            $broadcast = Broadcast::active()
                ->latest()
                ->first();
        } catch (\Throwable) {
            return response()->json(['success' => true, 'data' => null]);
        }

        if (!$broadcast) {
            return response()->json(['success' => true, 'data' => null, 'message' => 'No hay mensajes activos.']);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatBroadcast($broadcast),
        ]);
    }

    /**
     * Devuelve el historial de todos los broadcasts (paginado, 20 por página).
     * - Útil para un panel de historial en la app.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $broadcasts = Broadcast::active()
                ->latest()
                ->paginate(20);
        } catch (\Throwable) {
            return response()->json(['success' => true, 'data' => [], 'meta' => []]);
        }

        return response()->json([
            'success' => true,
            'data' => $broadcasts->getCollection()->map(fn($b) => $this->formatBroadcast($b)),
            'meta' => [
                'current_page' => $broadcasts->currentPage(),
                'last_page'    => $broadcasts->lastPage(),
                'total'        => $broadcasts->total(),
                'per_page'     => $broadcasts->perPage(),
            ],
        ]);
    }

    /**
     * Formatea un broadcast para la respuesta JSON.
     */
    private function formatBroadcast(Broadcast $broadcast): array
    {
        return [
            'id'                 => $broadcast->id,
            'type'               => $broadcast->type,
            'title'              => $broadcast->title,
            'body'               => $broadcast->body,
            'image_url'          => $broadcast->image_url,
            'is_popup'           => $broadcast->is_popup,
            'auto_close_seconds' => $broadcast->auto_close_seconds,
            'action_url'         => $broadcast->action_url,
            'icon'               => $broadcast->effective_icon,
            'color'              => $broadcast->effective_color,
            'expires_at'         => $broadcast->expires_at?->toIso8601String(),
            'sent_by'            => $broadcast->sent_by,
            'sent_at'            => $broadcast->created_at->toIso8601String(),
        ];
    }
}
