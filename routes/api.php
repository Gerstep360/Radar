<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppUpdateController;
use App\Http\Controllers\Api\BroadcastController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DenunciaController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Optimizado para Tauri (Sanctum tokens)
|--------------------------------------------------------------------------
|
| Controladores dedicados bajo App\Http\Controllers\Api\
| separados del monolito web. Respuestas JSON consistentes
| con API Resources, paginación cursor y rate limiting.
|
*/

// ── Auth (público) ─────────────────────────────────────────────
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-2fa', [AuthController::class, 'verify2Fa']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ── App Updates (público) ──────────────────────────────────────
Route::get('/app/latest-version', [AppUpdateController::class, 'latestVersion']);
Route::get('/app/download', [AppUpdateController::class, 'download']);

// ── Broadcasts / Notificaciones masivas (público) ──────────────
Route::get('/broadcasts/latest', [BroadcastController::class, 'latest']);
Route::get('/broadcasts', [BroadcastController::class, 'index']);

// ── Rutas protegidas (Sanctum) ─────────────────────────────────
Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {

    // ─── Auth ───
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ─── Denuncias (Reports) ───
    Route::prefix('reports')->group(function () {
        Route::get('/', [DenunciaController::class, 'index']);
        Route::post('/', [DenunciaController::class, 'store']);
        Route::get('/my', [DenunciaController::class, 'myReports']);
        Route::get('/{report}', [DenunciaController::class, 'show']);
        Route::put('/{report}', [DenunciaController::class, 'update']);
        Route::delete('/{report}', [DenunciaController::class, 'destroy']);
        Route::patch('/{report}/status', [DenunciaController::class, 'updateStatus']);

        // Media (evidencias)
        Route::post('/{report}/media', [DenunciaController::class, 'uploadMedia']);
        Route::delete('/{report}/media/{mediaId}', [DenunciaController::class, 'deleteMedia']);

        // Votos
        Route::post('/{report}/vote', [VoteController::class, 'toggle']);
        Route::get('/{report}/vote', [VoteController::class, 'check']);

        // Comentarios
        Route::get('/{report}/comments', [CommentController::class, 'index']);
        Route::post('/{report}/comments', [CommentController::class, 'store']);
    });

    // Eliminar comentario (fuera del prefix reports)
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // ─── Categorías ───
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    // ─── Mapa ───
    Route::prefix('map')->group(function () {
        Route::get('/points', [MapController::class, 'points']);
        Route::get('/points/{id}', [MapController::class, 'point']);
        Route::get('/bounds', [MapController::class, 'pointsInBounds']);
        Route::get('/clusters', [MapController::class, 'clusters']);
        Route::get('/realtime', [MapController::class, 'realtimeConfig']);
        Route::post('/cache-clear', [MapController::class, 'invalidateCache']);
    });

    // ─── Estadísticas ───
    Route::prefix('stats')->group(function () {
        Route::get('/dashboard', [StatsController::class, 'dashboard']);
        Route::get('/top-users', [StatsController::class, 'topUsuarios']);
        Route::get('/trending', [StatsController::class, 'trending']);
        Route::post('/cache-clear', [StatsController::class, 'invalidateCache']);
    });

    // ─── Notificaciones ───
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    // ─── Perfil ───
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        Route::get('/reports', [ProfileController::class, 'reports']);
    });

    // ─── Web Push Subscriptions ───
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
});
