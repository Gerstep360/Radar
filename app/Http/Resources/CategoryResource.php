<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->when($this->description, $this->description),
            'icon' => $this->when($this->icon, $this->icon),
            'priority' => $this->priority,
            'reports_count' => $this->when(isset($this->reports_count), $this->reports_count),
        ];
    }
}
