<?php

namespace App\Http\Livewire\Sprovider;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use App\Models\Booking;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

class SproviderDashboardComponent extends Component
{
    use WithFileUploads;

    public $activeSection = null;
    public $activeTab = 'services';
    public Collection $services;
    public $bookingCounts = [
        'pending' => 0,
        'accepted' => 0,
        'completed' => 0,
        'rejected' => 0
    ];
    public $selectedCategory = null;
    public $servicePrice = '';
    public $user;
    public $profilePicture;
    public $nidVerificationStatus;
    public $nidFile;
    public $nidNumber;
    public $editingServiceId = null;
    public $editingServicePrice = null;
    public $bookings;

    public function mount()
    {
        $this->user = auth()->user();
        $this->services = collect([]);
        $this->showMyServices();
        $this->loadBookingCounts();
        $this->loadProfileDetails();
    }

    public function showMyServices()
    {
        $this->activeSection = 'my_services';

        // Only attempt to fetch services if the table exists
        if (Schema::hasTable('services')) {
            $this->services = Service::where('service_provider_id', auth()->id() ?? 0)->get();
        } else {
            $this->services = collect([]);
        }
    }

    public function showBookings($status = null)
    {
        $this->activeSection = 'bookings';
        $this->loadBookingCounts(); // Reload booking counts each time bookings are shown
        
        $query = Booking::where('service_provider_id', auth()->id())
            ->with(['customer', 'service']);

        // Log the query details for debugging
        \Log::info('Booking Query Details', [
            'service_provider_id' => auth()->id(),
            'status' => $status,
            'pending_count' => $this->bookingCounts['pending']
        ]);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $this->bookings = $query->latest()->get();

        // Log the bookings for debugging
        \Log::info('Loaded Bookings', [
            'count' => $this->bookings->count(),
            'bookings' => $this->bookings->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'customer_name' => $booking->customer->name ?? 'Unknown',
                    'service_name' => $booking->service->name ?? 'Unknown',
                    'status' => $booking->status
                ];
            })->toArray()
        ]);
    }

    protected function loadBookingCounts()
    {
        $providerId = auth()->id() ?? 0;
        $this->bookingCounts = [
            'pending' => Booking::where('service_provider_id', $providerId)
                ->where('status', Booking::STATUS_PENDING)
                ->count(),
            'accepted' => Booking::where('service_provider_id', $providerId)
                ->where('status', Booking::STATUS_ACCEPTED)
                ->count(),
            'completed' => Booking::where('service_provider_id', $providerId)
                ->where('status', Booking::STATUS_COMPLETED)
                ->count(),
            'rejected' => Booking::where('service_provider_id', $providerId)
                ->where('status', Booking::STATUS_REJECTED)
                ->count()
        ];

        // Log booking counts for debugging
        \Log::info('Booking Counts', $this->bookingCounts);
    }

    public function loadProfileDetails()
    {
        $this->profilePicture = $this->user->profile_picture ?? 'default-profile.png';
        $this->nidVerificationStatus = $this->user->nid_verification_status ?? 'not_verified';
    }

    public function addServiceCategory()
    {
        $this->selectedCategory = null;
        $this->servicePrice = '';
    }

    public function resetForm()
    {
        $this->selectedCategory = null;
        $this->servicePrice = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedSelectedCategory()
    {
        // Optional: Any additional logic when category is updated
    }

    public function updatedServicePrice()
    {
        // Optional: Any additional logic when price is updated
    }

    public function saveNewService()
    {
        // Validate inputs
        $this->validate([
            'servicePrice' => 'required|numeric|min:0',
            'selectedCategory' => 'required|exists:service_categories,id'
        ]);

        // Create service
        DB::beginTransaction();
        try {
            $category = ServiceCategory::findOrFail($this->selectedCategory);

            // Check if table exists
            if (!Schema::hasTable('services')) {
                throw new \Exception('Services table does not exist');
            }

            // Check for existing service
            $existingService = Service::where('service_provider_id', auth()->id())
                ->where('category_id', $category->id)
                ->where('name', $category->name)
                ->first();

            if ($existingService) {
                session()->flash('error', 'This service has already been added.');
                DB::rollBack();
                return;
            }

            $service = Service::create([
                'name' => $category->name,
                'price' => $this->servicePrice,
                'service_provider_id' => auth()->id(),
                'category_id' => $category->id,
                'is_active' => true,
                'description' => null // Add this to match the migration
            ]);

            DB::commit();

            // Refresh services list
            $this->showMyServices();

            // Reset form
            $this->resetForm();

            session()->flash('message', 'Service added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service creation error: ' . $e->getMessage());
            session()->flash('error', 'Failed to add service: ' . $e->getMessage());
        }
    }

    public function uploadProfilePicture()
    {
        // Extensive debugging
        \Log::info('Profile Picture Upload Attempt - Detailed', [
            'profilePicture' => [
                'type' => gettype($this->profilePicture),
                'class' => is_object($this->profilePicture) ? get_class($this->profilePicture) : 'not an object',
                'value' => $this->profilePicture,
                'methods_available' => is_object($this->profilePicture) ? get_class_methods($this->profilePicture) : 'N/A'
            ]
        ]);

        // Check if file exists and is valid
        if (!$this->profilePicture) {
            \Log::error('No profile picture uploaded');
            session()->flash('error', 'Please select a profile picture.');
            return;
        }

        try {
            // Attempt to get file details
            if (method_exists($this->profilePicture, 'getClientOriginalName')) {
                $fileDetails = [
                    'original_name' => $this->profilePicture->getClientOriginalName(),
                    'mime_type' => method_exists($this->profilePicture, 'getMimeType') ? $this->profilePicture->getMimeType() : 'Unknown',
                    'extension' => method_exists($this->profilePicture, 'getClientOriginalExtension') ? $this->profilePicture->getClientOriginalExtension() : 'Unknown',
                    'size' => method_exists($this->profilePicture, 'getSize') ? $this->profilePicture->getSize() : 'Unknown'
                ];
                
                \Log::info('File Details', $fileDetails);
            }

            // Manual validation
            if (!$this->profilePicture instanceof \Illuminate\Http\UploadedFile && 
                !$this->profilePicture instanceof \Livewire\TemporaryUploadedFile) {
                throw new \Exception('Invalid file type');
            }

            // Validate file manually
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            $fileMimeType = $this->profilePicture->getMimeType();
            $fileSize = $this->profilePicture->getSize();

            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                throw new \Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
            }

            if ($fileSize > $maxFileSize) {
                throw new \Exception('File is too large. Maximum size is 2MB.');
            }

            // Generate unique filename
            $filename = 'profile_' . auth()->id() . '_' . uniqid() . '.' . $this->profilePicture->getClientOriginalExtension();
            
            // Store file
            $path = $this->profilePicture->storeAs('profile_pictures', $filename, 'public');

            // Update user's profile picture
            $user = auth()->user();
            $user->profile_picture = $path;
            $user->save();

            // Flash success message
            session()->flash('message', 'Profile picture uploaded successfully.');
            
            // Reset file input
            $this->reset('profilePicture');

        } catch (\Exception $e) {
            // Log the full error for debugging
            \Log::error('Profile Picture Upload Error', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'file_details' => isset($fileDetails) ? $fileDetails : 'No file details available'
            ]);

            // Flash error message
            session()->flash('error', $e->getMessage());
        }
    }

    public function updatedProfilePicture($value)
    {
        // Log when file is selected
        Log::info('Profile Picture Selected', [
            'value_type' => gettype($value),
            'value_class' => is_object($value) ? get_class($value) : 'not an object',
            'original_name' => $value ? $value->getClientOriginalName() : 'N/A'
        ]);
    }

    public function verifyNID()
    {
        // Validate NID number and file
        $this->validate([
            'nidNumber' => 'required|string|min:10|max:20',
            'nidFile' => 'required|image|max:2048' // Image file, max 2MB
        ], [
            'nidNumber.required' => 'NID number is required.',
            'nidNumber.min' => 'NID number must be at least 10 characters.',
            'nidNumber.max' => 'NID number cannot be more than 20 characters.',
            'nidFile.required' => 'NID document image is required.',
            'nidFile.image' => 'NID document must be an image.',
            'nidFile.max' => 'NID document image must be less than 2MB.'
        ]);

        try {
            // Generate a unique filename for the NID document
            $filename = 'nid_' . auth()->id() . '_' . uniqid() . '.' . $this->nidFile->getClientOriginalExtension();
            
            // Store the NID file
            $nidDocumentPath = $this->nidFile->storeAs('nid_documents', $filename, 'public');
            $relativePath = 'nid_documents/' . $filename;

            // Update user's NID information
            $user = auth()->user();
            $user->update([
                'nid_number' => $this->nidNumber,
                'nid_document' => $relativePath,
                'nid_verification_status' => 'pending'
            ]);

            // Flash success message
            session()->flash('message', 'NID verification request submitted successfully.');

            // Reset NID fields
            $this->reset(['nidNumber', 'nidFile']);

            // Update local NID verification status
            $this->nidVerificationStatus = 'pending';

        } catch (\Exception $e) {
            // Log the error
            \Log::error('NID Verification Error: ' . $e->getMessage());

            // Flash error message
            session()->flash('error', 'Failed to submit NID verification: ' . $e->getMessage());
        }
    }

    public function getServiceCategoriesProperty()
    {
        return ServiceCategory::all();
    }

    public function deleteService($serviceId)
    {
        DB::beginTransaction();
        try {
            // Find the service
            $service = Service::findOrFail($serviceId);

            // Ensure the service belongs to the current service provider
            if ($service->service_provider_id !== auth()->id()) {
                session()->flash('error', 'You are not authorized to delete this service.');
                return;
            }

            // Check if the service has any active bookings
            $activeBookings = Booking::where('service_id', $serviceId)
                ->whereIn('status', ['new', 'pending'])
                ->count();

            if ($activeBookings > 0) {
                session()->flash('error', 'Cannot delete service with active bookings.');
                return;
            }

            // Delete the service
            $service->delete();

            DB::commit();

            // Refresh services list
            $this->showMyServices();

            session()->flash('message', 'Service deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service deletion error: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }

    public function editServicePrice($serviceId)
    {
        // Find the service
        $service = Service::findOrFail($serviceId);

        // Validate ownership
        if ($service->service_provider_id !== auth()->id()) {
            session()->flash('error', 'You are not authorized to edit this service.');
            return;
        }

        // Set editing state
        $this->editingServiceId = $serviceId;
        $this->editingServicePrice = $service->price;
    }

    public function updateServicePrice()
    {
        // Validate inputs
        $this->validate([
            'editingServicePrice' => 'required|numeric|min:0'
        ]);

        // Ensure a service is being edited
        if (!$this->editingServiceId) {
            session()->flash('error', 'No service selected for editing.');
            return;
        }

        try {
            // Update the service
            $service = Service::findOrFail($this->editingServiceId);
            $service->price = $this->editingServicePrice;
            $service->save();

            // Refresh services list
            $this->showMyServices();

            // Reset editing state
            $this->editingServiceId = null;
            $this->editingServicePrice = null;

            session()->flash('message', 'Service price updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Service price update error: ' . $e->getMessage());
            session()->flash('error', 'Failed to update service price: ' . $e->getMessage());
        }
    }

    public function cancelEditPrice()
    {
        $this->editingServiceId = null;
        $this->editingServicePrice = null;
    }

    public function acceptBooking($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->service_provider_id !== auth()->id()) {
                throw new \Exception('Unauthorized action');
            }

            if ($booking->status !== Booking::STATUS_PENDING) {
                throw new \Exception('Booking can only be accepted when in pending status');
            }

            $booking->update([
                'status' => Booking::STATUS_ACCEPTED
            ]);

            // Create notification for customer
            Notification::createBookingStatusNotification($booking, 'Your booking has been accepted.');

            $this->loadBookingCounts();
            session()->flash('message', 'Booking accepted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to accept booking: ' . $e->getMessage());
        }
    }

    public function rejectBooking($bookingId, $reason = null)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->service_provider_id !== auth()->id()) {
                throw new \Exception('Unauthorized action');
            }

            if ($booking->status !== Booking::STATUS_PENDING) {
                throw new \Exception('Booking can only be rejected when in pending status');
            }

            $booking->update([
                'status' => Booking::STATUS_REJECTED,
                'rejection_reason' => $reason
            ]);

            // Create notification for customer
            Notification::createBookingStatusNotification($booking, 'Your booking has been rejected.');

            $this->loadBookingCounts();
            session()->flash('message', 'Booking rejected successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject booking: ' . $e->getMessage());
        }
    }

    public function completeBooking($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->service_provider_id !== auth()->id()) {
                throw new \Exception('Unauthorized action');
            }

            if ($booking->status !== Booking::STATUS_ACCEPTED) {
                throw new \Exception('Only accepted bookings can be marked as completed');
            }

            $booking->update([
                'status' => Booking::STATUS_COMPLETED
            ]);

            // Create notification for customer
            Notification::createBookingStatusNotification($booking, 'Your booking has been completed.');

            $this->loadBookingCounts();
            session()->flash('message', 'Booking marked as completed successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to complete booking: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Ensure the latest profile picture is loaded
        $this->profilePicture = auth()->user()->profile_picture ?? 'default-profile.png';

        $serviceCategories = ServiceCategory::all();
        
        return view('livewire.sprovider.sprovider-dashboard-component', [
            'bookingCounts' => $this->bookingCounts,
            'services' => $this->services,
            'user' => auth()->user(),
            'profilePicture' => auth()->user()->profile_picture ?? 'default-profile.png',
            'nidVerificationStatus' => $this->nidVerificationStatus,
            'serviceCategories' => $serviceCategories,
            'activeTab' => $this->activeTab,
            'bookings' => $this->bookings ?? collect([])
        ])->layout('layouts.base');
    }
}
