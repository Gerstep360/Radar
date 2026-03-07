<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class ReportCollection extends ResourceCollection
{
    public $collects = ReportResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'] ?? null,
                'last_page' => $paginated['last_page'] ?? null,
                'per_page' => $paginated['per_page'] ?? null,
                'total' => $paginated['total'] ?? null,
                'from' => $paginated['from'] ?? null,
                'to' => $paginated['to'] ?? null,
            ],
            'links' => [
                'first' => $paginated['first_page_url'] ?? null,
                'last' => $paginated['last_page_url'] ?? null,
                'prev' => $paginated['prev_page_url'] ?? null,
                'next' => $paginated['next_page_url'] ?? null,
            ],
        ];
    }
}
