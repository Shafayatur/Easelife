<div class="container-fluid">
    <div class="card">
        <div class="card-header card-header-primary">
            <h4 class="card-title">{{ ucfirst($userType) }} Management</h4>
            <p class="card-category">List of all {{ $userType }}s in the system</p>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input 
                        wire:model.debounce.300ms="search" 
                        type="text" 
                        class="form-control" 
                        placeholder="Search {{ $userType }}s by name or email"
                    >
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <a 
                            href="{{ route('admin.users', ['userType' => 'customer']) }}" 
                            class="btn {{ $userType == 'customer' ? 'btn-primary' : 'btn-secondary' }}"
                        >
                            Customers
                        </a>
                        <a 
                            href="{{ route('admin.users', ['userType' => 'service_provider']) }}" 
                            class="btn {{ $userType == 'service_provider' ? 'btn-primary' : 'btn-secondary' }}"
                        >
                            Service Providers
                        </a>
                    </div>
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
                            <th wire:click="sortBy('created_at')">
                                Registered At 
                                @if($sortField == 'created_at')
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
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button 
                                            wire:click="deleteUser({{ $user->id }})" 
                                            class="btn btn-danger btn-sm"
                                            onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    No {{ $userType }}s found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('userDeleted', function () {
            // Optional: Add any custom JS after user deletion
        });
    });
</script>
@endpush
