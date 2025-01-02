<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Service Provider Dashboard</h4>
                </div>
                <div class="card-body">
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button wire:click="showMyServices" class="btn btn-{{ $activeSection === 'my_services' ? 'success' : 'primary' }} btn-block">
                                My Services
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button wire:click="showBookings('all')" class="btn btn-{{ $activeSection === 'bookings' ? 'success' : 'primary' }} btn-block">
                                Bookings 
                                @if($bookingCounts['pending'] > 0)
                                    <span class="badge bg-danger ml-2">{{ $bookingCounts['pending'] }}</span>
                                @endif
                            </button>
                        </div>
                    </div>

                    @if($activeSection === 'my_services')
                        <div class="services-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>My Services</h5>
                                <button 
                                    wire:click="addServiceCategory" 
                                    class="btn btn-primary btn-sm rounded-pill px-4 py-2" 
                                    data-toggle="modal" 
                                    data-target="#addServiceModal">
                                    Add Services
                                </button>
                            </div>

                            <!-- Add Service Modal -->
                            <div wire:ignore.self class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form wire:submit.prevent="saveNewService">
                                                <div class="form-group">
                                                    <label for="selectedCategory">Select Service Category</label>
                                                    <select 
                                                        wire:model="selectedCategory" 
                                                        class="form-control" 
                                                        id="selectedCategory"
                                                        required
                                                    >
                                                        <option value="">Choose a category</option>
                                                        @foreach($serviceCategories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('selectedCategory')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="servicePrice">Price</label>
                                                    <input 
                                                        type="number" 
                                                        wire:model="servicePrice" 
                                                        step="0.01" 
                                                        class="form-control" 
                                                        id="servicePrice" 
                                                        placeholder="Enter service price" 
                                                        required
                                                    >
                                                    @error('servicePrice') 
                                                        <span class="text-danger">{{ $message }}</span> 
                                                    @enderror
                                                </div>

                                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 py-2">Save Service</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($services->isEmpty())
                                <div class="alert alert-info text-center">
                                    No services added yet. Click "Add Service" to get started.
                                </div>
                            @else
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($services as $service)
                                            <tr>
                                                <td>{{ $service->name }}</td>
                                                <td>{{ $service->category->name ?? 'Uncategorized' }}</td>
                                                <td>{{ number_format($service->price, 2) }}</td>
                                                <td>
                                                    @if($editingServiceId === $service->id)
                                                        <div class="input-group">
                                                            <input 
                                                                type="number" 
                                                                class="form-control form-control-sm" 
                                                                wire:model="editingServicePrice" 
                                                                step="0.01" 
                                                                min="0"
                                                            >
                                                            <div class="input-group-append">
                                                                <button 
                                                                    class="btn btn-sm btn-success" 
                                                                    wire:click="updateServicePrice()"
                                                                >
                                                                    <i class="material-icons">save</i>
                                                                </button>
                                                                <button 
                                                                    class="btn btn-sm btn-secondary" 
                                                                    wire:click="cancelEditPrice()"
                                                                >
                                                                    <i class="material-icons">close</i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <button 
                                                            wire:click="editServicePrice({{ $service->id }})" 
                                                            class="btn btn-sm btn-info mr-2"
                                                        >
                                                            Edit Price
                                                        </button>
                                                        <button 
                                                            wire:click="deleteService({{ $service->id }})" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="confirm('Are you sure you want to delete this service?') || event.stopImmediatePropagation()"
                                                        >
                                                            Delete
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @elseif($activeSection === 'bookings')
                        <div class="bookings-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>My Bookings</h5>
                                <div class="btn-group">
                                    <button wire:click="showBookings('all')" class="btn btn-sm btn-outline-primary">All</button>
                                    <button wire:click="showBookings('pending')" class="btn btn-sm btn-outline-warning">Pending ({{ $bookingCounts['pending'] }})</button>
                                    <button wire:click="showBookings('accepted')" class="btn btn-sm btn-outline-success">Accepted ({{ $bookingCounts['accepted'] }})</button>
                                    <button wire:click="showBookings('completed')" class="btn btn-sm btn-outline-info">Completed ({{ $bookingCounts['completed'] }})</button>
                                    <button wire:click="showBookings('rejected')" class="btn btn-sm btn-outline-danger">Rejected ({{ $bookingCounts['rejected'] }})</button>
                                </div>
                            </div>

                            @if($bookings->isEmpty())
                                <div class="alert alert-info text-center">
                                    No bookings found.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Customer</th>
                                                <th>Date & Time</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bookings as $booking)
                                                <tr>
                                                    <td>
                                                        {{ $booking->service->name }}<br>
                                                        <small class="text-muted">Price: ${{ number_format($booking->total_price, 2) }}</small>
                                                    </td>
                                                    <td>
                                                        {{ $booking->customer->name }}<br>
                                                        <small class="text-muted">{{ $booking->customer->email }}</small>
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}<br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $booking->status_color }}">
                                                            {{ $booking->status_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($booking->status === App\Models\Booking::STATUS_PENDING)
                                                            <button 
                                                                wire:click="acceptBooking({{ $booking->id }})" 
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Are you sure you want to accept this booking?')"
                                                            >
                                                                Accept
                                                            </button>
                                                            <button 
                                                                wire:click="rejectBooking({{ $booking->id }})" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure you want to reject this booking?')"
                                                            >
                                                                Reject
                                                            </button>
                                                        @elseif($booking->status === App\Models\Booking::STATUS_ACCEPTED)
                                                            <button 
                                                                wire:click="completeBooking({{ $booking->id }})" 
                                                                class="btn btn-sm btn-info"
                                                                onclick="return confirm('Are you sure you want to mark this booking as completed?')"
                                                            >
                                                                Mark Complete
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if($booking->additional_description)
                                                    <tr class="table-light">
                                                        <td colspan="5">
                                                            <strong>Additional Notes:</strong><br>
                                                            {{ $booking->additional_description }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if($booking->status === App\Models\Booking::STATUS_REJECTED && $booking->rejection_reason)
                                                    <tr class="table-danger">
                                                        <td colspan="5">
                                                            <strong>Rejection Reason:</strong><br>
                                                            {{ $booking->rejection_reason }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="profile-section mt-5 pt-5 text-center">
                <!-- Profile Picture -->
                <div class="profile-picture-section mb-4">
                    <h4 class="text-center mb-3">Profile Picture</h4>
                    
                    <div class="text-center mb-3">
                        <img 
                            src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                            alt="Profile Picture" 
                            class="rounded-circle" 
                            style="width: 200px; height: 200px; object-fit: cover;"
                        >
                    </div>

                    <form wire:submit.prevent="uploadProfilePicture" class="text-center">
                        <div class="form-group mb-3">
                            <input 
                                type="file" 
                                wire:model.live="profilePicture"
                                accept="image/jpeg,image/png,image/gif"
                                class="form-control @error('profilePicture') is-invalid @enderror"
                            >
                            @error('profilePicture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button 
                            type="submit" 
                            class="btn btn-primary" 
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Upload Profile Picture</span>
                            <span wire:loading>Uploading...</span>
                        </button>
                    </form>

                    @if(session()->has('error'))
                        <div class="alert alert-danger mt-3 text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session()->has('message'))
                        <div class="alert alert-success mt-3 text-center">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>

                <!-- User Information -->
                <div class="user-info mb-4">
                    <h4 class="text-center mb-3">User Details</h4>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" class="w-25">Name</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Phone</th>
                                <td>{{ $user->phone ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Registered On</th>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Last Updated</th>
                                <td>{{ $user->updated_at->format('d M Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- NID Verification -->
                <div class="nid-verification mt-4">
                    <h4 class="text-center mb-3">NID Verification</h4>
                    
                    @if($nidVerificationStatus == 'not_verified')
                        <form wire:submit.prevent="verifyNID" class="needs-validation" novalidate>
                            <div class="form-group mb-3">
                                <label for="nidNumber">NID Number</label>
                                <input 
                                    type="text" 
                                    wire:model="nidNumber" 
                                    id="nidNumber"
                                    placeholder="Enter NID Number" 
                                    class="form-control @error('nidNumber') is-invalid @enderror"
                                    required
                                >
                                @error('nidNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="nidFile">NID Document</label>
                                <input 
                                    type="file" 
                                    wire:model="nidFile"
                                    id="nidFile"
                                    accept="image/*"
                                    class="form-control @error('nidFile') is-invalid @enderror"
                                    required
                                >
                                @error('nidFile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button 
                                type="submit" 
                                class="btn btn-primary btn-block" 
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>Submit for Verification</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </form>
                    @elseif($nidVerificationStatus == 'pending')
                        <div class="alert alert-warning text-center">
                            NID Verification Pending
                        </div>
                    @elseif($nidVerificationStatus == 'verified')
                        <div class="alert alert-success text-center">
                            NID Verified
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session()->has('message'))
                        <div class="alert alert-success mt-3">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
        $('#addServiceModal').on('hidden.bs.modal', function () {
            @this.call('resetForm');
        });
    });
</script>
