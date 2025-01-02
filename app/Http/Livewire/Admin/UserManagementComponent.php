<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserManagementComponent extends Component
{
    use WithPagination;

    public $userType = 'customer';
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'userType' => ['except' => 'customer']
    ];

    public function mount($userType = 'customer')
    {
        // Log the user types for debugging
        \Log::info('User Management Debug', [
            'requested_user_type' => $userType,
            'available_user_types' => User::distinct('user_type')->pluck('user_type')->toArray(),
            'total_users_by_type' => User::groupBy('user_type')->select('user_type', DB::raw('count(*) as count'))->get()->toArray()
        ]);

        // Normalize user type
        $userType = $userType === 'service_provider' ? 'sp' : $userType;
        $this->userType = $userType;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $users = User::where('user_type', $this->userType)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.user-management-component', [
            'users' => $users
        ])->layout('layouts.base');
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        session()->flash('message', 'User deleted successfully.');
    }
}
