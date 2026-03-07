<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\VoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API VoteController — Votos en denuncias (REST para Tauri).
 */
class VoteController extends Controller
{
    public function __construct(
        protected VoteService $voteService
    ) {}

    /**
     * Toggle voto en un reporte (dar/quitar).
     */
    public function toggle(Request $request, Report $report): JsonResponse
    {
        // All authenticated users can vote

        $result = $this->voteService->toggleVote($report, $request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Consultar si el usuario ya votó un reporte.
     */
    public function check(Request $request, Report $report): JsonResponse
    {
        $userId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => [
                'voted' => $report->hasVotedBy($userId),
                'votes_count' => $report->votes()->count(),
            ],
        ]);
    }
}
