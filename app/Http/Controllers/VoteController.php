<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\VoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * VoteController - Gestión de votos en denuncias.
 * 
 * Responsabilidades:
 * - Toggle de votos (dar/quitar voto)
 * - Consultar estado de voto del usuario
 */
class VoteController extends Controller
{
    public function __construct(
        protected VoteService $voteService
    ) {}

    /**
     * Votar o quitar voto de un reporte.
     * 
     * @return JsonResponse|RedirectResponse
     */
    public function toggle(Report $report): JsonResponse|RedirectResponse
    {
        abort_unless(auth()->user()->can('crear denuncias'), 403, 'No tienes permiso para votar.');

        $result = $this->voteService->toggleVote($report, auth()->id());

        if (request()->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Consultar si el usuario ya votó por un reporte.
     */
    public function check(Report $report): JsonResponse
    {
        $hasVoted = $report->hasVotedBy(auth()->id());
        
        return response()->json([
            'voted' => $hasVoted,
            'votes_count' => $report->votes()->count()
        ]);
    }
}
