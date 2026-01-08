@extends('layouts.app')

@section('title', 'Acknowledgement')

@section('content')
<!-- Hero Section -->
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Acknowledgement</h1>
            </div>
        </div>
    </div>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                
                <!-- Main Content -->
                <div class="bg-white rounded-lg shadow p-8 mb-8">
                    <div class="flex items-center justify-center mb-8">
                        <div class="bg-blue-50 p-3 rounded-full">
                            <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">CMT Acknowledgment</h2>
                    
                    <div class="text-gray-700 text-lg leading-relaxed">
                        <p class="mb-6">
                            The Microsoft CMT service was used for managing the peer-reviewing process for this conference. This service was provided for free by Microsoft and they bore all expenses, including costs for Azure cloud services as well as for software development and support.
                        </p>
                    </div>
                    
                    <!-- Microsoft Badge -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <div class="flex items-center justify-center">
                            <div class="flex items-center bg-gray-50 px-6 py-4 rounded-lg">
                                <svg class="w-8 h-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4z"/>
                                </svg>
                                <div>
                                    <h3 class="font-bold text-gray-800">Microsoft</h3>
                                    <p class="text-sm text-gray-600">Conference Management Tools</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="text-center">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-6 py-3 bg-primary hover:bg-secondary text-white font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        Return to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection