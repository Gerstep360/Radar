<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Category;
use App\Http\Requests\StoreDenunciaRequest;
use App\Http\Requests\UpdateDenunciaRequest;
use App\Services\DenunciaService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

/**
 * DenunciaController - Controlador para gestión de denuncias/reportes.
 * 
 * Responsabilidades:
 * - CRUD de denuncias (crear, ver, editar)
 * - Listar denuncias del usuario
 * - NO maneja: mapa (MapController), votos (VoteController)
 */
class DenunciaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DenunciaService $denunciaService
    ) {}

    /**
     * Vista principal del Radar (Mapa + Bottom Sheet)
     */
    public function index()
    {
        abort_unless(auth()->user()->can('ver denuncias'), 403, 'No tienes permiso para ver denuncias.');

        $userId = Auth::id();

        // Obtener denuncias paginadas para el Bottom Sheet
        $denuncias = $this->denunciaService->obtenerDenuncias(auth()->user());
        $denuncias->load(['category', 'user', 'media']);
        
        // Agregar info de votos (count + has_voted)
        $denuncias->getCollection()->transform(function ($denuncia) use ($userId) {
            $denuncia->votes_count = $denuncia->votes()->count();
            $denuncia->has_voted = $denuncia->hasVotedBy($userId);
            return $denuncia;
        });
        
        // Categorías para el modal de crear
        $categories = Category::orderBy('priority', 'desc')->get();

        return view('radar.index', compact('denuncias', 'categories'));
    }

    /**
     * Vista para crear nueva denuncia
     */
    public function create()
    {
        abort_unless(auth()->user()->can('crear denuncias'), 403, 'No tienes permiso para crear denuncias.');
        
        $categories = Category::orderBy('priority', 'desc')->orderBy('name')->get();
        
        $nearbyPins = Report::select('id', 'latitude', 'longitude', 'title', 'category_id')
            ->with('category:id,name')
            ->whereNotNull('latitude')
            ->where('status', '!=', 'atendido')
            ->latest()
            ->limit(50)
            ->get();
        
        return view('radar.create', compact('categories', 'nearbyPins')); 
    }

    /**
     * Guardar nueva denuncia
     */
    public function store(StoreDenunciaRequest $request)
    {
        abort_unless(auth()->user()->can('crear denuncias'), 403, 'No tienes permiso para crear denuncias.');

        $report = $this->denunciaService->crearDenuncia($request->validated());

        // Si es AJAX, responder JSON (para agregar marcador sin recargar)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Denuncia registrada exitosamente.',
                'report' => [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'status' => $report->status,
                    'votes_count' => 0,
                    'category' => [
                        'id' => $report->category?->id,
                        'name' => $report->category?->name,
                    ],
                    'created_at' => $report->created_at->diffForHumans(),
                ]
            ]);
        }

        return redirect()->route('denuncias.index')
            ->with('success', 'Denuncia registrada. La estamos procesando.');
    }

    /**
     * Ver detalle de una denuncia
     */
    public function show(Report $denuncia)
    {
        abort_unless(auth()->user()->can('ver denuncias'), 403, 'No tienes permiso para ver esta denuncia.');
        
        $denuncia->loadCount('votes');
        $denuncia->has_voted = $denuncia->hasVotedBy(Auth::id());

        return view('radar.show', compact('denuncia'));
    }

    /**
     * Actualizar denuncia
     */
    public function update(UpdateDenunciaRequest $request, Report $denuncia)
    {
        abort_unless(auth()->user()->can('gestionar denuncias'), 403, 'No tienes permiso para actualizar denuncias.');
        
        $denuncia->update($request->validated());

        return back()->with('success', 'Denuncia actualizada correctamente.');
    }

    /**
     * Cambiar estado de una denuncia (solo admin/funcionarios).
     */
    public function updateStatus(Report $denuncia)
    {
        // La autorización se maneja en el middleware de la ruta (can:cambiarEstado,denuncia)
        
        $validated = request()->validate([
            'status' => 'required|in:pendiente,en_proceso,atendido,rechazado'
        ]);

        $this->denunciaService->actualizarEstado($denuncia, $validated['status']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $validated['status']
            ]);
        }

        return back()->with('success', 'Estado actualizado a: ' . $validated['status']);
    }
}