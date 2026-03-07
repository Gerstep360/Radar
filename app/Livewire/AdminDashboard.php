<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Component
{
    // Pestaña actual por defecto
    public $currentTab = 'overview';

    // Propiedades para el modal de detalle
    public $showReportModal = false;
    public $selectedReport = null;
    public $adminComment = '';
    public $newStatus = '';

    protected $listeners = [
        'open-report-detail' => 'openReportDetails'
    ];

    // Opcional: escuchar parámetros de la URL genéricos si quisieras enlazar pestañas 
    // pero sin exponer atributos de consultas
    protected $queryString = [
        'currentTab' => ['except' => 'overview'],
    ];

    public function setTab($tab)
    {
        $allowedTabs = ['overview', 'reports-list', 'map'];
        if (auth()->user()->isAdmin()) {
            $allowedTabs[] = 'users-list';
        }

        if (in_array($tab, $allowedTabs)) {
            $this->currentTab = $tab;
            
            if ($tab === 'map') {
                // Ensure map catches its new layout dimensions after block display
                $this->dispatch('map-refresh');
            }
        }
    }

    public function openReportDetails($id)
    {
        $this->selectedReport = \App\Models\Report::with(['category', 'user', 'media'])->find($id);

        if ($this->selectedReport) {
            $this->newStatus = $this->selectedReport->status;
            $this->adminComment = $this->selectedReport->admin_comment ?? '';
            $this->showReportModal = true;
        }
    }

    public function saveReportResponse()
    {
        if (!$this->selectedReport) return;

        try {
            $reportId = is_object($this->selectedReport) ? $this->selectedReport->id : $this->selectedReport['id'];
            $report = \App\Models\Report::find($reportId);
            
            if (!$report) {
                session()->flash('error', 'No se pudo encontrar el reporte.');
                return;
            }

            // Actualizar el reporte directamente con los nuevos campos
            $report->update([
                'status' => $this->newStatus,
                'admin_comment' => $this->adminComment,
                'responded_at' => now(),
                'responded_by' => auth()->id() ?? 1
            ]);

            // Refrescar para la UI
            $this->openReportDetails($report->id);

            // Notificar a otros componentes
            $this->dispatch('report-updated', id: $report->id, status: $report->status);
            
            session()->flash('success', '¡Registro actualizado con éxito!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error guardando respuesta de reporte: " . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showReportModal = false;
        $this->selectedReport = null;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.admin-dashboard')->layout('layouts.app');
    }
}
