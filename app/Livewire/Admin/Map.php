<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Report;

class Map extends Component
{
    // Opcionalmente podemos agregar filtros reactivos para el mapa
    public $status = '';
    public $category_id = '';

    protected $listeners = [
        'report-updated' => 'refreshMap',
        'map-reload' => 'refreshMap'
    ];

    public function refreshMap()
    {
        $this->dispatch('map-refresh-data', $this->getLocations());
    }

    public function updated($property)
    {
        $this->dispatch('map-refresh-data', $this->getLocations());
    }

    private function getLocations()
    {
        $query = Report::query()->whereNotNull('latitude')->whereNotNull('longitude');

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return $query->with(['category', 'media'])->latest()->limit(1000)->get()->map(function($r) {
            return [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->description ?? 'Sin descripción',
                'latitude' => $r->latitude,
                'longitude' => $r->longitude,
                'status' => $r->status,
                'category' => $r->category->name ?? 'General',
                'date' => $r->created_at->format('d M, Y H:i'),
                'votes_count' => $r->votes_count ?? 0,
                'image_url' => $r->media->first()->file_path ?? null,
            ];
        })->toArray();
    }

    public function render()
    {
        $categories = \App\Models\Category::all();

        return view('livewire.admin.map', [
            'locations' => $this->getLocations(),
            'categories' => $categories
        ]);
    }
}
