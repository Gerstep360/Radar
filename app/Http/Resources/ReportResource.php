<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->user()?->id;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'admin_comment' => $this->when($this->admin_comment, $this->admin_comment),
            'responded_at' => $this->responded_at?->toIso8601String(),

            // Relaciones (solo si cargadas)
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),

            // Contadores
            'votes_count' => $this->when(isset($this->votes_count), $this->votes_count, 0),
            'comments_count' => $this->when(isset($this->comments_count), $this->comments_count),

            // Estado del usuario actual
            'has_voted' => $this->when(
                $userId !== null,
                fn () => $this->relationLoaded('votes')
                    ? $this->votes->contains('user_id', $userId)
                    : ($this->has_voted ?? false)
            ),
            'is_owner' => $this->when($userId !== null, fn () => $this->user_id === $userId),

            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
