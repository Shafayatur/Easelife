<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Service Provider Dashboard</h1>
            
            @if(session('status'))
                <div class="alert alert-{{ session('status') === 'pending' ? 'warning' : 'info' }} alert-dismissible fade show" role="alert">
                    <strong>{{ ucfirst(session('status')) }}!</strong> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-header">New Requests</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $bookingCounts['new'] ?? 0 }} Requests</h5>
                            <p class="card-text">Waiting for review</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-header">Pending</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $bookingCounts['pending'] ?? 0 }} Requests</h5>
                            <p class="card-text">In progress</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-header">Completed</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $bookingCounts['completed'] ?? 0 }} Requests</h5>
                            <p class="card-text">Finished services</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-header">Rejected</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $bookingCounts['rejected'] ?? 0 }} Requests</h5>
                            <p class="card-text">Declined requests</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <button wire:click="showMyServices" class="btn btn-primary btn-block">
                        <i class="material-icons">list</i> My Services
                    </button>
                </div>
                <div class="col-md-6">
                    <button wire:click="showBookings('all')" class="btn btn-primary btn-block">
                        <i class="material-icons">calendar_today</i> Bookings
                    </button>
                </div>
            </div>

            <!-- My Services Section -->
            @if($activeSection === 'my_services')
                <div class="card">
                    <div class="card-header card-header-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">My Services</h4>
                            <button class="btn btn-success">
                                <i class="material-icons">add</i> Add Service
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($services->isEmpty())
                            <div class="alert alert-info text-center">
                                No services added yet. Click "Add Service" to get started.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($services as $service)
                                            <tr>
                                                <td>{{ $service->name }}</td>
                                                <td>{{ $service->category->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $service->is_active ? 'success' : 'warning' }}">
                                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" wire:click="editService({{ $service->id }})">
                                                        <i class="material-icons">edit</i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" wire:click="deleteService({{ $service->id }})">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Bookings Section -->
            @if(str_contains($activeSection ?? '', 'bookings_'))
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Bookings Management</h4>
                    </div>
                    <div class="card-body">
                        <!-- Booking Status Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <button wire:click="showBookings('new')" 
                                        class="btn btn-block btn-warning position-relative">
                                    New Requests
                                    @if($bookingCounts['new'] > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $bookingCounts['new'] }}
                                        </span>
                                    @endif
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button wire:click="showBookings('pending')" class="btn btn-block btn-info">
                                    Pending
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button wire:click="showBookings('completed')" class="btn btn-block btn-success">
                                    Completed
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button wire:click="showBookings('rejected')" class="btn btn-block btn-danger">
                                    Rejected
                                </button>
                            </div>
                        </div>

                        <!-- Bookings Table -->
                        @if($bookings->isEmpty())
                            <div class="alert alert-info text-center">
                                No bookings found for the selected status.
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
                                            <th>Payment Received</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $booking)
                                            <tr>
                                                <td>{{ $booking->service->name }}</td>
                                                <td>{{ $booking->customer->name }}</td>
                                                <td>{{ $booking->scheduled_date->format('d M Y H:i') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $booking->status_color }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $payment = $booking->payments()->first();
                                                    @endphp
                                                    @if($payment)
                                                        @if($payment->status == 'completed')
                                                            <span class="badge badge-success">Received</span>
                                                        @else
                                                            <button 
                                                                wire:click="markPaymentReceived({{ $booking->id }})" 
                                                                class="btn btn-sm btn-outline-success">
                                                                Mark as Received
                                                            </button>
                                                        @endif
                                                    @else
                                                        <button 
                                                            wire:click="markPaymentReceived({{ $booking->id }})" 
                                                            class="btn btn-sm btn-outline-warning">
                                                            Mark Payment
                                                        </button>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($booking->status === 'new')
                                                        <button wire:click="acceptBooking({{ $booking->id }})" 
                                                                class="btn btn-sm btn-success">
                                                            <i class="material-icons">check</i>
                                                        </button>
                                                        <button wire:click="rejectBooking({{ $booking->id }})" 
                                                                class="btn btn-sm btn-danger">
                                                            <i class="material-icons">close</i>
                                                        </button>
                                                    @endif
                                                    @if($booking->status === 'pending')
                                                        <button wire:click="completeBooking({{ $booking->id }})" 
                                                                class="btn btn-sm btn-success">
                                                            <i class="material-icons">done_all</i>
                                                        </button>
                                                    @endif
                                                    <button wire:click="viewBookingDetails({{ $booking->id }})" 
                                                            class="btn btn-sm btn-info">
                                                        <i class="material-icons">visibility</i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
