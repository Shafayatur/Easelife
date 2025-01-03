<div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2 class="mb-0">My Profile</h2>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row" class="w-25">Full Name</th>
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
                                    <th scope="row">User Type</th>
                                    <td>{{ ucfirst($user->user_type) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Registered On</th>
                                    <td>{{ $user->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row mt-3 text-center">
                            <div class="col-sm-12">
                                <a class="btn btn-secondary" href="{{ route('customer.dashboard') }}">Back to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
