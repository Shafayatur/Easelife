<div class="booking-wrapper">
    <div class="booking-container">
        <div class="booking-card">
            <div class="booking-header">
                <h2>Booking Information</h2>
            </div>
            
            @if($bookingMessage)
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ $bookingMessage }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($existingPendingRequest)
                <div class="alert alert-warning" role="alert">
                    <strong>Pending Booking Request</strong>
                    <p>You have a pending booking request for 
                        {{ $existingPendingRequest->service->name }} 
                        with {{ $existingPendingRequest->serviceProvider->name }}.
                    </p>
                    <p>Current Status: 
                        <span class="badge bg-{{ $existingPendingRequest->status_color }}">
                            {{ $existingPendingRequest->status_label }}
                        </span>
                    </p>
                    <p>Scheduled for: {{ \Carbon\Carbon::parse($existingPendingRequest->date)->format('d M Y') }}
                        at {{ \Carbon\Carbon::parse($existingPendingRequest->time)->format('h:i A') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="createBooking" class="booking-form">
                <div class="form-group">
                    <label for="serviceId">Select Service</label>
                    <select 
                        wire:model="serviceId" 
                        class="form-control @error('serviceId') is-invalid @enderror" 
                        required
                    >
                        <option value="">Choose a service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->name }} - ${{ number_format($service->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('serviceId')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Full Address</label>
                    <input 
                        type="text" 
                        wire:model="address" 
                        class="form-control @error('address') is-invalid @enderror" 
                        placeholder="Enter full address"
                        required
                    >
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="city">City</label>
                        <input 
                            type="text" 
                            wire:model="city" 
                            class="form-control @error('city') is-invalid @enderror" 
                            placeholder="Enter city"
                            required
                        >
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="postal_code">Postal Code</label>
                        <input 
                            type="text" 
                            wire:model="postal_code" 
                            class="form-control @error('postal_code') is-invalid @enderror" 
                            placeholder="Enter postal code"
                            required
                        >
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="booking_date">Booking Date</label>
                        <input 
                            type="date" 
                            wire:model="booking_date" 
                            class="form-control @error('booking_date') is-invalid @enderror"
                            required
                        >
                        @error('booking_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="booking_time">Booking Time</label>
                        <input 
                            type="time" 
                            wire:model="booking_time" 
                            class="form-control @error('booking_time') is-invalid @enderror"
                            required
                        >
                        @error('booking_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="additional_description">Additional Description (Optional)</label>
                    <textarea 
                        wire:model="additional_description" 
                        class="form-control @error('additional_description') is-invalid @enderror" 
                        rows="3" 
                        placeholder="Provide any additional information about your service request"
                    ></textarea>
                    @error('additional_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">
                        Submit Booking Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

.booking-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    width: 100%;
    background-color: #f4f4f4;
}

.booking-container {
    width: 100%;
    max-width: 500px;
    padding: 20px;
}

.booking-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 30px;
}

.booking-header {
    text-align: center;
    margin-bottom: 25px;
}

.booking-header h2 {
    color: #333;
    margin-bottom: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.form-row {
    display: flex;
    margin: 0 -10px;
}

.form-row .form-group {
    flex: 1;
    padding: 0 10px;
}

.btn-primary {
    width: 100%;
    padding: 10px;
}
</style>
