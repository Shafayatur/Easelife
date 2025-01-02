<?php

use App\Http\Controllers\PaymentController;
use App\Http\Livewire\Admin\AdminDashboardComponent;
use App\Http\Livewire\Admin\AdminServiceCategoryComponent;
use App\Http\Livewire\BookServiceComponent;
use App\Http\Livewire\Customer\CustomerDashboardComponent;
use App\Http\Livewire\Customer\ServiceProvidersComponent;
use App\Http\Livewire\HomeComponent;
use App\Http\Livewire\ServiceCategoriesComponent;
use App\Http\Livewire\Sprovider\SproviderDashboardComponent;
use App\Http\Livewire\ServiceProvider\DashboardComponent;
use App\Http\Livewire\ServiceProvider\NotificationComponent;
use App\Http\Livewire\ServiceProvider\ServiceProviderDashboardComponent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/',HomeComponent::class)->name('home');
Route::get('/service-categories',ServiceCategoriesComponent::class)->name('home.service_categories');
Route::get('/book-service/{category_id}', BookServiceComponent::class)->name('book.service');

Route::middleware(['auth:sanctum', 'verified'])->group(function(){
    Route::get('/customer/dashboard',CustomerDashboardComponent::class)->name('customer.dashboard');
    // Service Providers Route
    Route::get('/service-providers/{categoryId}', \App\Http\Livewire\Customer\ServiceProvidersComponent::class)
        ->name('customer.service_providers');
    // Book Service Route with Provider
    Route::get('/book-service/{categoryId}/{providerId}', \App\Http\Livewire\Customer\BookServiceComponent::class)
        ->name('customer.book_service');
});

Route::middleware(['auth:sanctum','verified','authsprovider'])->group(function(){
    Route::get('/sprovider/dashboard',SproviderDashboardComponent::class)->name('sprovider.dashboard');
});

Route::middleware(['auth:sanctum','verified','authadmin'])->group(function(){
    Route::get('/admin/dashboard',AdminDashboardComponent::class)->name('admin.dashboard');
    Route::get('/admin/service-categories',AdminServiceCategoryComponent::class)->name('admin.service_categories');
    // Admin NID Verification Routes
    Route::get('/admin/nid-verification', \App\Http\Livewire\Admin\NidVerificationComponent::class)
        ->name('admin.nid-verification');
    // Admin User Management Route
    Route::get('/admin/users/{userType?}', function($userType = 'customer') {
        // Log the incoming request
        \Log::info('User Management Route Debug', [
            'requested_user_type' => $userType,
            'available_user_types' => \App\Models\User::distinct('user_type')->pluck('user_type')->toArray(),
            'total_users_by_type' => \App\Models\User::groupBy('user_type')
                ->select('user_type', DB::raw('count(*) as count'))
                ->get()
                ->toArray()
        ]);

        // Validate and normalize user type
        $validUserTypes = ['customer', 'sp'];
        $userType = $userType === 'service_provider' ? 'sp' : $userType;
        
        if (!in_array($userType, $validUserTypes)) {
            $userType = 'customer';
        }

        return app()->call(\App\Http\Livewire\Admin\UserManagementComponent::class, ['userType' => $userType]);
    })
        ->name('admin.users')
        ->middleware(['authadmin']);
});

Route::middleware(['auth:sanctum', 'verified', 'sp'])->group(function () {
    Route::get('/service-provider/dashboard', \App\Http\Livewire\ServiceProvider\DashboardComponent::class)
        ->name('service_provider.dashboard');

    Route::get('/service-provider/notifications', \App\Http\Livewire\ServiceProvider\NotificationComponent::class)
        ->name('service_provider.notifications');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Existing routes...

    // Payment routes
    Route::get('/booking/{bookingId}/payment', [PaymentController::class, 'generatePaymentForm'])->name('payment.form');
    Route::post('/booking/{bookingId}/pay', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/success/{bookingId}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel/{bookingId}', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->user_type === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->user_type === 'sprovider') {
        return redirect()->route('sprovider.dashboard');
    } else {
        return redirect()->route('customer.dashboard');
    }
})->name('dashboard');
