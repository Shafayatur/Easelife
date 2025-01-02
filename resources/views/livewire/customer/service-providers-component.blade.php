<div class="container service-providers-section">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h2 class="section-title">
                {{ $category->name }} Service Providers
            </h2>
            <p class="text-muted">Select a service provider that meets your needs</p>
        </div>
    </div>

    @if($serviceProviders->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="alert alert-light text-center">
                    <i class="material-icons align-middle mr-2 text-info">info_outline</i>
                    No service providers are currently available for this category.
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($serviceProviders as $provider)
                <div class="col-md-4 mb-4">
                    <div class="card provider-card">
                        <div class="card-body text-center">
                            <div class="provider-avatar mb-3">
                                <img 
                                    src="{{ $provider->profile_picture ? asset('storage/'.$provider->profile_picture) : asset('images/default-profile.png') }}" 
                                    alt="{{ $provider->name }}"
                                    class="rounded-circle"
                                >
                            </div>
                            <h4 class="provider-name mb-2">{{ $provider->name }}</h4>
                            
                            <div class="services-list mb-3">
                                @foreach($provider->services->where('category_id', $category->id) as $service)
                                    <div class="service-item d-flex justify-content-between mb-2">
                                        <span>{{ $service->name }}</span>
                                        <span class="text-primary font-weight-bold">
                                            ${{ number_format($service->price, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <a 
                                href="{{ route('customer.book_service', ['categoryId' => $category->id, 'providerId' => $provider->id]) }}" 
                                class="btn btn-primary btn-block"
                            >
                                Book Service
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.service-providers-section {
    background-color: #ffffff;
    padding: 40px 0;
}

.section-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 15px;
}

.provider-card {
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.provider-card:hover {
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    transform: translateY(-5px);
}

.provider-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.provider-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 3px solid #f1f1f1;
}

.provider-name {
    color: #333;
    font-weight: 500;
}

.service-item {
    border-bottom: 1px solid #f1f1f1;
    padding-bottom: 5px;
}

.service-item:last-child {
    border-bottom: none;
}
</style>
