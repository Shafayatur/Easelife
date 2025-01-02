<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Admin Dashboard Overview</h4>
                    <p class="card-category">Comprehensive statistics of your platform</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-tabs card-header-primary">
                                    <div class="nav-tabs-navigation">
                                        <div class="nav-tabs-wrapper">
                                            <ul class="nav nav-tabs" data-tabs="tabs">
                                                <li class="nav-item">
                                                    <a class="nav-link active" href="#users" data-toggle="tab">
                                                        <i class="material-icons">people</i> User Statistics
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#nid" data-toggle="tab">
                                                        <i class="material-icons">verified_user</i> NID Verification
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#services" data-toggle="tab">
                                                        <i class="material-icons">category</i> Service Categories
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="users">
                                            <table class="table">
                                                <thead class="text-primary">
                                                    <tr>
                                                        <th>User Type</th>
                                                        <th>Total Count</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Customers</td>
                                                        <td>{{ $totalCustomers }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.users', ['userType' => 'customer']) }}" class="btn btn-primary btn-sm">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Service Providers</td>
                                                        <td>{{ $totalServiceProviders }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.users', ['userType' => 'sp']) }}" class="btn btn-success btn-sm">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="nid">
                                            <table class="table">
                                                <thead class="text-primary">
                                                    <tr>
                                                        <th>NID Verification</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>NID Verification Management</td>
                                                        <td>
                                                            <a href="{{ route('admin.nid-verification') }}" class="btn btn-primary btn-sm">
                                                                Manage Verifications
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="services">
                                            <table class="table">
                                                <thead class="text-primary">
                                                    <tr>
                                                        <th>Service Categories</th>
                                                        <th>Total Count</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Total Categories</td>
                                                        <td>{{ $totalServiceCategories }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.service_categories') }}" class="btn btn-info btn-sm">
                                                                Manage Categories
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Optional: Add any custom dashboard interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Animate dashboard cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.classList.add('animate__animated', 'animate__fadeIn');
        });
    });
</script>
@endpush
