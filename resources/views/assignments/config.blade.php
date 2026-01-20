@extends('layouts.app')

@section('title', 'Auto-Assign Configuration - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Auto-Assignment Configuration</h1>
        
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Configuration Settings</h2>
            <p class="text-gray-600 mb-6">Configure the automatic assignment algorithm parameters.</p>
            
            <form method="POST" action="{{ route('assignments.auto') }}">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="min_reviews" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Reviews per Paper
                        </label>
                        <input type="number" 
                               id="min_reviews" 
                               name="min_reviews" 
                               value="3" 
                               min="1" 
                               max="10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Each paper will get at least this many reviewers.</p>
                    </div>
                    
                    <div>
                        <label for="max_reviews" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Reviews per Paper
                        </label>
                        <input type="number" 
                               id="max_reviews" 
                               name="max_reviews" 
                               value="5" 
                               min="1" 
                               max="20"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">No paper will get more than this many reviewers.</p>
                    </div>
                    
                    <div>
                        <label for="max_papers" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Papers per Reviewer
                        </label>
                        <input type="number" 
                               id="max_papers" 
                               name="max_papers" 
                               value="10" 
                               min="1" 
                               max="50"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">No reviewer will be assigned more than this many papers.</p>
                    </div>
                    
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            Conference Year
                        </label>
                        <select id="year" name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                            <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t">
                    <a href="{{ route('assignments.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-save mr-2"></i>Save Configuration & Run Auto-Assign
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Important Notes</h3>
            <ul class="list-disc pl-5 space-y-2 text-yellow-700">
                <li>The auto-assignment algorithm will respect bid preferences and conflict declarations.</li>
                <li>Reviewers with high expertise match scores will be prioritized.</li>
                <li>The system will balance reviewer workloads as much as possible.</li>
                <li>You can always make manual adjustments after auto-assignment.</li>
                <li>Running auto-assignment multiple times may create duplicate assignments.</li>
            </ul>
        </div>
    </div>
</div>
@endsection