<div class="container-fluid">
    <div class="card">
        <div class="card-header card-header-primary">
            <h4 class="card-title">NID Verification Management</h4>
            <p class="card-category">Manage Service Provider NID Verifications</p>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="btn-group" role="group">
                        <button 
                            wire:click="$set('filterStatus', 'pending')" 
                            class="btn {{ $filterStatus == 'pending' ? 'btn-primary' : 'btn-secondary' }}"
                        >
                            Pending
                        </button>
                        <button 
                            wire:click="$set('filterStatus', 'verified')" 
                            class="btn {{ $filterStatus == 'verified' ? 'btn-primary' : 'btn-secondary' }}"
                        >
                            Verified
                        </button>
                        <button 
                            wire:click="$set('filterStatus', 'rejected')" 
                            class="btn {{ $filterStatus == 'rejected' ? 'btn-primary' : 'btn-secondary' }}"
                        >
                            Rejected
                        </button>
                    </div>
                </div>
                <div class="col-md-8">
                    <input 
                        wire:model.debounce.300ms="search" 
                        type="text" 
                        class="form-control" 
                        placeholder="Search by name, email, or NID number"
                    >
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="text-primary">
                        <tr>
                            <th wire:click="sortBy('id')">
                                ID 
                                @if($sortField == 'id')
                                    <i class="fa fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('name')">
                                Name 
                                @if($sortField == 'name')
                                    <i class="fa fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('email')">
                                Email 
                                @if($sortField == 'email')
                                    <i class="fa fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('nid_number')">
                                NID Number 
                                @if($sortField == 'nid_number')
                                    <i class="fa fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->nid_number ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button 
                                            wire:click="viewNidDetails({{ $user->id }})" 
                                            class="btn btn-info btn-sm"
                                            data-toggle="modal" 
                                            data-target="#nidDetailsModal"
                                        >
                                            View Details
                                        </button>
                                        @if($filterStatus == 'pending')
                                            <button 
                                                wire:click="approveNid({{ $user->id }})" 
                                                class="btn btn-success btn-sm"
                                                onclick="confirm('Are you sure you want to approve this NID?') || event.stopImmediatePropagation()"
                                            >
                                                Approve
                                            </button>
                                            <button 
                                                wire:click="rejectNid({{ $user->id }})" 
                                                class="btn btn-danger btn-sm"
                                                onclick="confirm('Are you sure you want to reject this NID?') || event.stopImmediatePropagation()"
                                            >
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    No NID verification requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- NID Details Modal -->
    @if($selectedNid)
        <div class="modal fade show" id="nidDetailsModal" tabindex="-1" role="dialog" aria-labelledby="nidDetailsModalLabel" style="display: block; padding-right: 17px;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nidDetailsModalLabel">NID Details for {{ $selectedNid->name }}</h5>
                        <button type="button" class="close" wire:click="$set('selectedNid', null)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Personal Information</h6>
                                <p><strong>Name:</strong> {{ $selectedNid->name }}</p>
                                <p><strong>Email:</strong> {{ $selectedNid->email }}</p>
                                <p><strong>NID Number:</strong> {{ $selectedNid->nid_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>NID Document</h6>
                                @if($selectedNid->nid_document)
                                    <img 
                                        src="{{ asset('storage/' . $selectedNid->nid_document) }}" 
                                        alt="NID Document" 
                                        class="img-fluid"
                                    >
                                @else
                                    <p>No NID document uploaded</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('selectedNid', null)">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        // Optional: Add any custom interactions
        Livewire.on('nidVerificationUpdated', function () {
            // Refresh or show a notification
        });
    });
</script>
@endpush
