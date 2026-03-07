<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Http\Requests\Api\UpdateReportRequest;
use App\Http\Requests\Api\UpdateReportStatusRequest;
use App\Http\Requests\Api\UploadMediaRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\ReportCollection;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Services\DenunciaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API DenunciaController — Optimizado para Tauri (REST + Sanctum).
 *
 * Endpoints:
 *  GET    /api/reports            → index   (listado paginado con filtros)
 *  POST   /api/reports            → store   (crear denuncia)
 *  GET    /api/reports/{report}   → show    (detalle completo)
 *  PUT    /api/reports/{report}   → update  (editar denuncia)
 *  DELETE /api/reports/{report}   → destroy (eliminar denuncia)
 *  PATCH  /api/reports/{report}/status → updateStatus (cambiar estado)
 *  POST   /api/reports/{report}/media  → uploadMedia  (subir evidencias)
 *  GET    /api/reports/my         → myReports (denuncias del usuario)
 */
class DenunciaController extends Controller
{
    public function __construct(
        protected DenunciaService $denunciaService
    ) {}

    /**
     * Listado paginado con filtros opcionales.
     *
     * Query params:
     *  - per_page: int (default 15, max 50)
     *  - status: pendiente|en_revision|atendido|desestimado
     *  - category_id: int
     *  - search: string (busca en título y descripción)
     *  - sort: recent|oldest|most_voted (default recent)
     */
    public function index(Request $request): JsonResponse
    {
        // Normal users, moderators and admins can view denuncias. Auth is guaranteed by middleware.

        $perPage = min((int) $request->input('per_page', 15), 50);

        $query = Report::query()
            ->with(['category:id,name,priority', 'user:id,name', 'media'])
            ->withCount(['votes', 'comments']);

        // ── Filtros ──
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->input('search')) {
            $search = strip_tags($search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // ── Ordenamiento ──
        match ($request->input('sort', 'recent')) {
            'oldest' => $query->oldest(),
            'most_voted' => $query->orderByDesc('votes_count'),
            default => $query->latest(),
        };

        $reports = $query->paginate($perPage);

        // Agregar has_voted eficientemente
        $userId = $request->user()?->id;
        if ($userId) {
            $reportIds = $reports->pluck('id')->toArray();
            $votedIds = \App\Models\Vote::where('user_id', $userId)
                ->whereIn('report_id', $reportIds)
                ->pluck('report_id')
                ->flip()
                ->toArray();

            $reports->getCollection()->transform(function ($report) use ($votedIds) {
                $report->has_voted = isset($votedIds[$report->id]);
                return $report;
            });
        }

        return (new ReportCollection($reports))
            ->additional(['success' => true])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Crear nueva denuncia.
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $report = $this->denunciaService->crearDenuncia($data);
        $report->load(['category:id,name,priority', 'user:id,name', 'media']);
        $report->loadCount(['votes', 'comments']);

        // Forzar has_voted=false porque acabamos de crearlo
        $report->has_voted = false;

        return response()->json([
            'success' => true,
            'message' => 'Denuncia registrada exitosamente.',
            'data' => new ReportResource($report),
        ], 201);
    }

    /**
     * Detalle completo de una denuncia (con comentarios recientes).
     */
    public function show(Request $request, Report $report): JsonResponse
    {
        // Normal users, moderators and admins can view denuncias. Auth is guaranteed by middleware.

        $report->load([
            'category:id,name,priority',
            'user:id,name',
            'media',
            'comments' => fn ($q) => $q->with('user:id,name')->latest()->limit(20),
        ]);
        $report->loadCount(['votes', 'comments']);
        $report->has_voted = $report->hasVotedBy($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => new ReportResource($report),
        ]);
    }

    /**
     * Actualizar denuncia (solo dueño o admin).
     */
    public function update(UpdateReportRequest $request, Report $report): JsonResponse
    {
        $report->update($request->validated());
        $report->load(['category:id,name,priority', 'user:id,name', 'media']);
        $report->loadCount(['votes', 'comments']);

        return response()->json([
            'success' => true,
            'message' => 'Denuncia actualizada correctamente.',
            'data' => new ReportResource($report),
        ]);
    }

    /**
     * Eliminar denuncia (solo dueño o admin).
     */
    public function destroy(Request $request, Report $report): JsonResponse
    {
        $user = $request->user();

        if ($user->id !== $report->user_id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta denuncia.',
            ], 403);
        }

        // Eliminar media asociada del storage
        foreach ($report->media as $media) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
        }

        $report->comments()->delete();
        $report->votes()->delete();
        $report->media()->delete();
        $report->notifications()->delete();
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Denuncia eliminada correctamente.',
        ]);
    }

    /**
     * Cambiar estado de una denuncia (admin/moderador).
     */
    public function updateStatus(UpdateReportStatusRequest $request, Report $report): JsonResponse
    {
        $oldStatus = $report->status;
        $newStatus = $request->validated()['status'];

        $this->denunciaService->actualizarEstado($report, $newStatus);

        // Guardar comentario admin si lo envió
        if ($adminComment = $request->validated()['admin_comment'] ?? null) {
            $report->update([
                'admin_comment' => $adminComment,
                'responded_at' => now(),
                'responded_by' => $request->user()->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado.',
            'data' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'admin_comment' => $adminComment,
            ],
        ]);
    }

    /**
     * Subir evidencias (imágenes) a una denuncia.
     */
    public function uploadMedia(UploadMediaRequest $request, Report $report): JsonResponse
    {
        // Verificar límite total de media
        $currentCount = $report->media()->count();
        $newCount = count($request->file('media'));

        if (($currentCount + $newCount) > 10) {
            return response()->json([
                'success' => false,
                'message' => "Solo puedes tener máximo 10 imágenes. Actualmente tienes {$currentCount}.",
            ], 422);
        }

        $uploaded = [];
        foreach ($request->file('media') as $file) {
            $path = $file->store('evidencias/' . date('Y/m'), 'public');
            $uploaded[] = $report->media()->create(['file_path' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' imagen(es) subida(s).',
            'data' => MediaResource::collection(collect($uploaded)),
        ], 201);
    }

    /**
     * Denuncias del usuario autenticado.
     */
    public function myReports(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min((int) $request->input('per_page', 15), 50);

        $reports = Report::query()
            ->where('user_id', $user->id)
            ->with(['category:id,name,priority', 'user:id,name', 'media'])
            ->withCount(['votes', 'comments'])
            ->latest()
            ->paginate($perPage);

        // Agregar has_voted para cada reporte
        if ($reports->isNotEmpty()) {
            $reportIds = $reports->pluck('id')->toArray();
            $votedIds = \App\Models\Vote::where('user_id', $user->id)
                ->whereIn('report_id', $reportIds)
                ->pluck('report_id')
                ->flip()
                ->toArray();

            $reports->getCollection()->transform(function ($report) use ($votedIds) {
                $report->has_voted = isset($votedIds[$report->id]);
                return $report;
            });
        }

        return (new ReportCollection($reports))
            ->additional(['success' => true])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Eliminar una imagen de evidencia.
     */
    public function deleteMedia(Request $request, Report $report, int $mediaId): JsonResponse
    {
        $user = $request->user();

        if ($user->id !== $report->user_id && !($user->isAdmin() || $user->isModerator())) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso.',
            ], 403);
        }

        $media = $report->media()->findOrFail($mediaId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada.',
        ]);
    }
}
