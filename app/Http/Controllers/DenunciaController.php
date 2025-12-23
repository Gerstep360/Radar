<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Http\Requests\StoreDenunciaRequest;
use App\Http\Requests\UpdateDenunciaRequest;
use App\Services\DenunciaService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DenunciaController extends Controller
{
    use AuthorizesRequests;

    protected $denunciaService;

    public function __construct(DenunciaService $denunciaService)
    {
        $this->denunciaService = $denunciaService;
    }

    public function index()
    {
        // 1. Spatie haciendo de portero de boliche
        abort_unless(auth()->user()->can('ver denuncias'), 403, 'No tienes permiso para ver denuncias.');

        // 2. El service hace la magia sucia (traer datos según rol)
        $denuncias = $this->denunciaService->obtenerDenuncias(auth()->user());
        
        // Cargar relaciones necesarias incluyendo votos
        $denuncias->load(['category', 'user', 'media', 'votes']);
        
        // 3. LO NUEVO: Cargar categorías para ese Modal Flotante
        $categories = \App\Models\Category::orderBy('priority', 'desc')->get();

        // 4. Retornamos la vista pasando AMBAS variables
        return view('radar.index', compact('denuncias', 'categories'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('crear denuncias'), 403, 'No tienes permiso para crear denuncias.');
        
        // Obtener todas las categorías para el select
        $categories = \App\Models\Category::orderBy('priority', 'desc')->orderBy('name')->get();
        
        // Obtener pines cercanos para el mapa (últimas 50 denuncias activas)
        $nearbyPins = \App\Models\Report::select('id', 'latitude', 'longitude', 'title', 'category_id')
            ->with('category:id,name')
            ->whereNotNull('latitude')
            ->where('status', '!=', 'atendido') // No mostrar las ya atendidas
            ->latest()
            ->limit(50)
            ->get();
        
        return view('radar.create', compact('categories', 'nearbyPins')); 
    }

    public function store(StoreDenunciaRequest $request)
    {
        abort_unless(auth()->user()->can('crear denuncias'), 403, 'No tienes permiso para crear denuncias.');

        $this->denunciaService->crearDenuncia($request->validated());

        return redirect()->route('denuncias.index')
            ->with('success', 'Denuncia registrada. La estamos procesando.');
    }

    public function show(Denuncia $denuncia)
    {
        abort_unless(auth()->user()->can('ver denuncias'), 403, 'No tienes permiso para ver esta denuncia.');
        return view('radar.show', compact('denuncia'));
    }

    // UPDATE suele usarse más para cambiar estados o añadir info extra en estos sistemas
    public function update(UpdateDenunciaRequest $request, Denuncia $denuncia)
    {
        abort_unless(auth()->user()->can('gestionar denuncias'), 403, 'No tienes permiso para actualizar denuncias.');
        
        // Aquí podrías tener un método específico en el service si solo cambias estado
        // $this->denunciaService->actualizarEstado($denuncia, $request->estado);
        
        // O actualización general:
        $denuncia->update($request->validated());

        return back()->with('success', 'Denuncia actualizada correctamente.');
    }
}