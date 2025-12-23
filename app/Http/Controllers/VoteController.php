<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\VoteService;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    protected $voteService;

    public function __construct(VoteService $voteService)
    {
        $this->voteService = $voteService;
    }

    /**
     * Votar o quitar voto de un reporte
     */
    public function toggle(Report $report)
    {
        abort_unless(auth()->user()->can('crear denuncias'), 403, 'No tienes permiso para votar.');

        $result = $this->voteService->toggleVote($report, auth()->id());

        if (request()->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }
}
