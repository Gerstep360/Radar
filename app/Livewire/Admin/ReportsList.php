<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Report;
use App\Models\Category;

class ReportsList extends Component
{
    use WithPagination;

    // Aquí ya NO usamos Url(history: true) para blindar el frontend
    public $search = '';
    public $category_id = '';
    public $status = '';
    public $date_from = '';
    public $date_to = '';
    public $priority = '';

    public function updated($property)
    {
        if (in_array($property, ['search', 'category_id', 'status', 'date_from', 'date_to', 'priority'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category_id', 'status', 'date_from', 'date_to', 'priority']);
        $this->resetPage();
    }

    // Usamos las consultas protegidas de Eloquent (automáticamente parametrizadas)
    public function getBaseQueryProperty()
    {
        $query = Report::query();

        if ($this->search) {
            $query->where(function ($sub) {
                // Laravel PDO bindings previenen inyección SQL aquí:
                $sub->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return $query;
    }

    public function updateReportStatus($reportId, $newStatus)
    {
        $report = Report::find($reportId);
        // Validar que no inyecten un string de estatus malicioso ignorando el select
        if ($report && in_array($newStatus, ['pendiente','en_revision','atendido','desestimado','pending','in_progress','resolved','rejected','en_proceso','resuelto','rechazado'])) {
            $report->status = $newStatus;
            $report->save();
            session()->flash('success', 'Estado actualizado correctamente.');
        }
    }

    public function render()
    {
        $query = clone $this->baseQuery;
        $query->with(['user', 'category', 'media', 'votes']);
        
        if ($this->priority === 'alta') {
            $query->withCount('votes')->orderByDesc('votes_count');
        } else {
            $query->latest();
        }
        $reports = $query->paginate(20);

        $categories = Category::all();

        return view('livewire.admin.reports-list', compact('reports', 'categories'));
    }
}
