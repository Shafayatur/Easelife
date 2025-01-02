<div class="container booking-container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="material-icons align-middle mr-2">bookmarks</i>
                            Book {{ $category->name }} Service
                            @if($provider)
                                with {{ $provider->name }}
                            @endif
                        </h3>
                        <span class="badge bg-light text-primary">
                            <i class="material-icons align-middle mr-1">category</i>
                            {{ $category->name }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($provider && $services->count() > 0)
                        <div class="mb-4 service-selection-list">
                            <h4 class="mb-3">
                                <i class="material-icons align-middle mr-2">list</i>
                                Select a Service from {{ $provider->name }}
                            </h4>
                            <div class="list-group">
                                @foreach($services as $service)
                                    <button 
                                        wire:click="selectService({{ $service->id }})" 
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedService && $selectedService->id === $service->id ? 'active' : '' }}"
                                    >
                                        <span>
                                            <i class="material-icons align-middle mr-2">design_services</i>
                                            {{ $service->name }}
                                        </span>
                                        <span class="badge bg-success">
                                            ${{ number_format($service->price, 2) }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($selectedService || !$provider)
                        <form wire:submit.prevent="bookService" class="booking-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="material-icons align-middle mr-2">person</i>
                                            Full Name
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control @error('name') is-invalid @enderror" 
                                            wire:model="name" 
                                            placeholder="Enter your full name"
                                        >
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="material-icons align-middle mr-2">email</i>
                                            Email Address
                                        </label>
                                        <input 
                                            type="email" 
                                            class="form-control @error('email') is-invalid @enderror" 
                                            wire:model="email" 
                                            placeholder="Enter your email"
                                        >
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="material-icons align-middle mr-2">phone</i>
                                            Phone Number
                                        </label>
                                        <input 
                                            type="tel" 
                                            class="form-control @error('phone') is-invalid @enderror" 
                                            wire:model="phone" 
                                            placeholder="Enter your phone number"
                                        >
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="material-icons align-middle mr-2">location_on</i>
                                            Address
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control @error('address') is-invalid @enderror" 
                                            wire:model="address" 
                                            placeholder="Enter your address"
                                        >
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="material-icons align-middle mr-2">description</i>
                                    Additional Description (Optional)
                                </label>
                                <textarea 
                                    class="form-control @error('description') is-invalid @enderror" 
                                    wire:model="description" 
                                    placeholder="Any additional details about your service request"
                                    rows="3"
                                ></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($selectedService)
                                <div class="alert alert-info d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="material-icons align-middle mr-2">check_circle</i>
                                        Selected Service: {{ $selectedService->name }}
                                    </span>
                                    <span class="badge bg-primary">
                                        Price: ${{ number_format($selectedService->price, 2) }}
                                    </span>
                                </div>
                            @endif

                            <div class="text-center mt-4">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary btn-lg" 
                                    {{ !$selectedService ? 'disabled' : '' }}
                                >
                                    <i class="material-icons align-middle mr-2">bookmark_add</i>
                                    Book Service
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
