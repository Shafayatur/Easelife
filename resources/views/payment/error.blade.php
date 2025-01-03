@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="bg-red-500 text-white text-center py-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-2xl font-bold">Payment Error</h2>
        </div>
        
        <div class="p-6 text-center">
            <p class="text-gray-700 mb-4 text-lg">
                {{ $message ?? 'An unexpected error occurred during payment processing.' }}
            </p>
            
            @if(isset($booking_id))
            <div class="bg-gray-100 rounded-md p-3 mb-4 inline-block">
                <span class="text-gray-600">Booking Reference: </span>
                <span class="font-semibold text-gray-800">#{{ $booking_id }}</span>
            </div>
            @endif
            
            <div class="flex justify-center space-x-4 mt-6">
                <a href="{{ route('customer.dashboard') }}" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #f3f4f6;
    }
</style>
@endpush
