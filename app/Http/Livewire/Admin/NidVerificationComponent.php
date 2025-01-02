<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class NidVerificationComponent extends Component
{
    use WithPagination;

    public $selectedNid = null;
    public $search = '';
    public $filterStatus = 'pending';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'pending']
    ];

    public function mount()
    {
        $this->resetPage();
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

    public function viewNidDetails($userId)
    {
        $this->selectedNid = User::findOrFail($userId);
    }

    public function approveNid($userId)
    {
        $user = User::findOrFail($userId);
        
        // Update NID verification status
        $user->update([
            'nid_verification_status' => 'verified'
        ]);

        // Optional: Send notification to the user
        // You can implement email or system notification here

        session()->flash('message', 'NID verification has been approved.');
        $this->selectedNid = null;
    }

    public function rejectNid($userId)
    {
        $user = User::findOrFail($userId);
        
        // Explicitly check and update the status from 'pending' to 'rejected'
        if ($user->nid_verification_status === 'pending') {
            $user->update([
                'nid_verification_status' => 'rejected',
                'nid_document' => null // Optional: clear the document path
            ]);

            // Optional: Log the rejection
            \Log::info("NID Verification Rejected for User ID: {$userId}");

            session()->flash('message', 'NID verification has been rejected.');
        } else {
            session()->flash('error', 'NID verification is not in a pending state.');
        }

        $this->selectedNid = null;
    }

    public function downloadNidDocument($userId)
    {
        $user = User::findOrFail($userId);

        if (!$user->nid_document) {
            session()->flash('error', 'No NID document found.');
            return;
        }

        // Check if it's a full URL
        if (str_starts_with($user->nid_document, 'http')) {
            return response()->redirectTo($user->nid_document);
        }

        // Assume local file in public storage
        $path = storage_path('app/public/' . $user->nid_document);

        if (!file_exists($path)) {
            session()->flash('error', 'NID document file does not exist.');
            return;
        }

        return response()->download($path, 'nid_document_' . $user->id . '.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    public function render()
    {
        $query = User::where('user_type', 'sp')
            ->where('nid_verification_status', $this->filterStatus)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('nid_number', 'like', '%' . $this->search . '%');
            });

        $users = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.nid-verification-component', [
            'users' => $users,
            'selectedNid' => $this->selectedNid
        ])->layout('layouts.base');
    }
}
