@extends('layouts.app')

@section('title', 'Select Paper - Upload Revised Abstract')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
                <h1 class="text-xl font-bold text-white">Select Paper for Revised Abstract Upload</h1>
                <p class="text-gray-100 text-sm mt-1">Choose which paper you want to upload the revised abstract for</p>
            </div>
            
            <div class="p-6">
                @if($papers->count() > 0)
                    <div class="space-y-4">
                        @foreach($papers as $paper)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $paper->title }}</h3>
                                    <div class="flex items-center space-x-3 mt-2">
                                        <span class="text-sm text-gray-500">{{ $paper->anonymous_id }}</span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $paper->decision == 'accept_with_minor_revision' ? 'Minor Revision Required' : 'Major Revision Required' }}
                                        </span>
                                    </div>
                                    <div class="mt-3 text-sm text-gray-600">
                                        <p><strong>Deadline:</strong> May 1, 2026</p>
                                        <p><strong>Required Format:</strong> MS Word (.doc or .docx)</p>
                                    </div>
                                </div>
                                <a href="{{ route('author.revised-abstract.upload', $paper) }}" 
                                   class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                                    <i class="fas fa-upload mr-2"></i> Upload
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                        <p class="text-gray-600">No papers require revised abstract upload at this time.</p>
                        <p class="text-sm text-gray-500 mt-2">All your accepted papers have been uploaded or are pending review.</p>
                        <a href="{{ route('author.dashboard') }}" class="mt-4 inline-block text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection