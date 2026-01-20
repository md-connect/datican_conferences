@extends('layouts.app')

@section('title', 'Discussion - ' . $discussion->paper->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <a href="{{ route('papers.show', $discussion->paper) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            Paper: {{ $discussion->paper->anonymous_id }}
                        </a>
                    </li>
                    <li>
                        <span class="mx-2">/</span>
                    </li>
                    <li>
                        <a href="{{ route('discussions.paper', $discussion->paper) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            Discussions
                        </a>
                    </li>
                    <li>
                        <span class="mx-2">/</span>
                    </li>
                    <li class="text-gray-500">Discussion</li>
                </ol>
            </nav>
        </div>
        
        <!-- Discussion Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                        <span class="text-lg font-medium text-blue-700">
                            {{ strtoupper(substr($discussion->user->first_name, 0, 1) . substr($discussion->user->last_name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $discussion->user->full_name }}</h1>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($discussion->type === 'review') bg-blue-100 text-blue-800
                                @elseif($discussion->type === 'rebuttal') bg-green-100 text-green-800
                                @elseif($discussion->type === 'decision') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($discussion->type) }}
                            </span>
                            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">
                                {{ $discussion->created_at->format('F d, Y H:i') }}
                            </span>
                            @if($discussion->is_resolved)
                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Resolved
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    @if(auth()->user()->is_admin || auth()->id() === $discussion->user_id)
                    <a href="{{ route('discussions.edit', $discussion) }}" 
                       class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    @endif
                    
                    @if(auth()->user()->is_admin)
                    <form action="{{ route('discussions.resolve', $discussion) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 {{ $discussion->is_resolved ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }} rounded-lg hover:opacity-90">
                            <i class="fas fa-{{ $discussion->is_resolved ? 'undo' : 'check' }} mr-2"></i>
                            {{ $discussion->is_resolved ? 'Re-open' : 'Mark Resolved' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            
            <!-- Discussion Content -->
            <div class="prose max-w-none">
                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-gray-700 whitespace-pre-line">{{ $discussion->content }}</p>
                </div>
            </div>
        </div>
        
        <!-- Replies -->
        <div class="space-y-6">
            <h3 class="text-xl font-semibold text-gray-800">Replies ({{ $discussion->replies->count() }})</h3>
            
            @if($discussion->replies->count() > 0)
            <div class="space-y-4">
                @foreach($discussion->replies as $reply)
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                            <span class="text-sm font-medium text-gray-700">
                                {{ strtoupper(substr($reply->user->first_name, 0, 1) . substr($reply->user->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $reply->user->full_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-line">{{ $reply->content }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 bg-white rounded-xl shadow">
                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No replies yet.</p>
            </div>
            @endif
        </div>
        
        <!-- Reply Form -->
        <div class="mt-8 bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Add a Reply</h3>
            
            <form action="{{ route('discussions.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="paper_id" value="{{ $discussion->paper_id }}">
                <input type="hidden" name="parent_id" value="{{ $discussion->id }}">
                <input type="hidden" name="type" value="general">
                <input type="hidden" name="visibility" value="{{ $discussion->visibility }}">
                
                <div>
                    <textarea name="content" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Write your reply here..." required></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Post Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection