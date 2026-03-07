<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersList extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function changeRole($userId, $newRole)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Prevent admin from removing their own admin role to avoid lockout
        if ($userId == auth()->id() && $newRole !== 'A') {
            session()->flash('error', 'No puedes quitarte tu propio rol de Administrador.');
            return;
        }

        if (in_array($newRole, ['A', 'M', 'U'])) {
            $user = User::find($userId);
            if ($user) {
                $user->role = $newRole;
                $user->save();
                session()->flash('success', "Rol de {$user->name} actualizado a " . $this->getRoleName($newRole));
            }
        }
    }

    private function getRoleName($role)
    {
        return match($role) {
            'A' => 'Administrador',
            'M' => 'Moderador',
            'U' => 'Usuario',
            default => 'Desaparecido'
        };
    }

    public function render()
    {
        $query = User::query()
            ->withCount('reports');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->latest()->paginate(15);

        return view('livewire.admin.users-list', compact('users'));
    }
}
