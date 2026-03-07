<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($this->isCurrentUser($request), $this->email),
            'initials' => $this->initials(),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'reports_count' => $this->when(isset($this->reports_count), $this->reports_count),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function isCurrentUser(Request $request): bool
    {
        return $request->user()?->id === $this->id;
    }
}
