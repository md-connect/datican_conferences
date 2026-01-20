@extends('layouts.app')

@section('title', 'User Management - DATICAN Conference')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <div class="flex space-x-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, email, or institution"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                        <option value="reviewer" {{ request('role') == 'reviewer' ? 'selected' : '' }}>Reviewers</option>
                        <option value="author" {{ request('role') == 'author' ? 'selected' : '' }}>Authors</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="ml-4 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">All Users ({{ $users->total() }})</h2>
                    <span class="text-sm text-gray-500">{{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Papers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->affiliation }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->is_admin)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-user-shield mr-1"></i> Admin
                                    </span>
                                    @endif
                                    @if($user->is_reviewer)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-clipboard-check mr-1"></i> Reviewer
                                    </span>
                                    @endif
                                    @if($user->papers()->exists())
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-file-alt mr-1"></i> Author
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->papers()->count() }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($user->papers()->count() > 0)
                                    {{ $user->papers()->where('status', 'accepted')->count() }} accepted
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('users.show', $user) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="mailto:{{ $user->email }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-envelope"></i> Email
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users-slash text-4xl mb-4"></i>
                                <p>No users found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $users->links() }}
            </div>
            @endif
        </div>
        
        <!-- Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-gray-500">Total Users</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-user-shield text-3xl text-red-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('is_admin', true)->count() }}</p>
                    <p class="text-sm text-gray-500">Administrators</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center">
                    <i class="fas fa-clipboard-check text-3xl text-purple-600 mb-3"></i>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('is_reviewer', true)->count() }}</p>
                    <p class="text-sm text-gray-500">Reviewers</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection