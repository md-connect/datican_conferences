@extends('layouts.app')

@section('title', 'Update Expertise Profile - Reviewer Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Update Expertise Profile</h1>
                <p class="text-gray-600 mt-2">Manage your expertise areas to help match you with relevant papers</p>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div>
                    <p class="font-medium text-blue-800">Why update your expertise?</p>
                    <p class="text-sm text-blue-700 mt-1">
                        Your expertise areas help the system match you with papers that align with your knowledge.
                        The more accurate your profile, the better the paper assignments you'll receive.
                    </p>
                </div>
            </div>
        </div>

        <!-- Add New Expertise Form -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add New Expertise Area</h2>
            
            <form method="POST" action="{{ route('reviewer.expertise.store') }}" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Topic Area -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Topic Area *
                        </label>
                        <select name="topic" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Topic Area</option>
                            <option value="Artificial Intelligence & Machine Learning">Artificial Intelligence & Machine Learning</option>
                            <option value="Data Science & Analytics">Data Science & Analytics</option>
                            <option value="Healthcare Applications">Healthcare Applications</option>
                            <option value="Clinical Decision Support">Clinical Decision Support</option>
                            <option value="Medical Imaging">Medical Imaging</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <!-- Expertise Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Expertise Level *
                        </label>
                        <select name="level" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Level</option>
                            <option value="expert">Expert (I have published extensively in this area)</option>
                            <option value="proficient">Proficient (I have significant experience)</option>
                            <option value="familiar">Familiar (I have working knowledge)</option>
                            <option value="basic">Basic (I have some understanding)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Confidence Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confidence in This Expertise *
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        @for($i = 5; $i >= 1; $i--)
                        <label class="flex items-center justify-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="confidence" value="{{ $i }}" required class="mr-2">
                            <span>{{ $i }}</span>
                        </label>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-500 mt-1">1 = Very Low, 5 = Very High</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Add Expertise Area
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Expertise Areas -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Your Expertise Areas</h2>
                <p class="text-sm text-gray-500 mt-1">These help us match you with relevant papers</p>
            </div>
            
            @if($expertiseAreas->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($expertiseAreas as $expertise)
                <div class="p-6 hover:bg-gray-50 transition duration-150">
                    <form method="POST" action="{{ route('reviewer.expertise.update', $expertise->id) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Topic -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Topic Area</label>
                                <select name="topic" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="Artificial Intelligence & Machine Learning" {{ $expertise->topic == 'Artificial Intelligence & Machine Learning' ? 'selected' : '' }}>
                                        Artificial Intelligence & Machine Learning
                                    </option>
                                    <option value="Data Science & Analytics" {{ $expertise->topic == 'Data Science & Analytics' ? 'selected' : '' }}>
                                        Data Science & Analytics
                                    </option>
                                    <option value="Healthcare Applications" {{ $expertise->topic == 'Healthcare Applications' ? 'selected' : '' }}>
                                        Healthcare Applications
                                    </option>
                                    <option value="Clinical Decision Support" {{ $expertise->topic == 'Clinical Decision Support' ? 'selected' : '' }}>
                                        Clinical Decision Support
                                    </option>
                                    <option value="Medical Imaging" {{ $expertise->topic == 'Medical Imaging' ? 'selected' : '' }}>
                                        Medical Imaging
                                    </option>
                                    <option value="Other" {{ $expertise->topic == 'Other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Level -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Expertise Level</label>
                                <select name="level" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="expert" {{ $expertise->level == 'expert' ? 'selected' : '' }}>Expert</option>
                                    <option value="proficient" {{ $expertise->level == 'proficient' ? 'selected' : '' }}>Proficient</option>
                                    <option value="familiar" {{ $expertise->level == 'familiar' ? 'selected' : '' }}>Familiar</option>
                                    <option value="basic" {{ $expertise->level == 'basic' ? 'selected' : '' }}>Basic</option>
                                </select>
                            </div>
                            
                            <!-- Confidence -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Confidence (1-5)</label>
                                <select name="confidence" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $expertise->confidence == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-save mr-1"></i> Update
                                </button>
                                <a href="{{ route('reviewer.expertise.destroy', $expertise->id) }}" 
                                   onclick="event.preventDefault(); if(confirm('Remove this expertise area?')) document.getElementById('delete-form-{{ $expertise->id }}').submit();"
                                   class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm">
                                    <i class="fas fa-trash mr-1"></i> Remove
                                </a>
                                <form id="delete-form-{{ $expertise->id }}" 
                                      action="{{ route('reviewer.expertise.destroy', $expertise->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-chalkboard-user text-4xl mb-4"></i>
                <p class="text-lg font-medium">No expertise areas added yet</p>
                <p class="text-sm mt-2">Add your first expertise area above to help match you with relevant papers</p>
            </div>
            @endif
        </div>

        <!-- Tips Section -->
        <div class="mt-8 bg-gray-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Tips for Selecting Expertise Areas</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                    <span><strong>Be specific:</strong> Choose the most relevant topic areas for your expertise</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                    <span><strong>Be honest:</strong> Select expertise levels that accurately reflect your knowledge</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                    <span><strong>Keep updated:</strong> Add new areas as you develop expertise in new domains</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                    <span><strong>Multiple areas:</strong> You can add as many expertise areas as you'd like</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection