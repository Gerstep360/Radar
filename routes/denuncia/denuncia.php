<?php
use App\Http\Controllers\DenunciaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:60,1'])->group(function () {

    // --- 1. CRUD PRINCIPAL (Denuncias) ---
    Route::resource('denuncias', DenunciaController::class)
        // Seguridad por defecto: Solo activamos las rutas que existen en tu Controller.
        // Si intentan DELETE o EDIT (form), les da error de método no permitido (405).
        ->only(['index', 'create', 'store', 'show', 'update'])
        
        // Optimización (Regex): Si el ID no es numérico, Laravel rechaza la petición.
        // El Controller ni se entera, ahorramos RAM.
        ->whereNumber('denuncia')
        
        // Manejo de Error 404: Si el número es válido pero no hay registro en BD.
        ->missing(function () {
            return redirect()->route('denuncias.index')
                ->with('error', 'Esa denuncia no existe en el radar.');
        });


    // --- 2. RUTA ESPECIAL (Cambiar Estado) ---
    // Esta ruta es delicada, así que lleva su propio middleware de permiso.
    Route::patch('/denuncias/{denuncia}/estado', [DenunciaController::class, 'updateStatus'])
        ->name('denuncias.status')
        ->whereNumber('denuncia') // Doble check de ID numérico
        // CADENERO DE SEGURIDAD:
        // Llama a DenunciaPolicy::cambiarEstado($user, $denuncia)
        // Si retorna false, tira error 403 y no ejecuta el método del controller.
        ->middleware('can:cambiarEstado,denuncia');
});