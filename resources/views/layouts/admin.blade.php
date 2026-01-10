<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - DATICAN Admin</title>
    <link
        rel="icon"
        href="{{ asset('images/logo/datican_logo_io.png') }}"
        type="image/png"
    >

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #2C3E50;
            border-right: 4px solid #2C3E50;
        }
        .sidebar-link:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }
        .h-screen-80 {
            height: calc(100vh - 4rem);
        }
    </style>
    
    <!-- Alpine.js for interactive sidebar -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configure Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2C3E50',
                        secondary: '#1A252F',
                        accent: '#E74C3C',
                        
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: window.innerWidth >= 768, mobileSidebarOpen: false }" @resize.window="sidebarOpen = window.innerWidth >= 768">
    <!-- Mobile sidebar backdrop -->
    <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false" 
         class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden" x-cloak>
    </div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside :class="{'translate-x-0': sidebarOpen || mobileSidebarOpen, '-translate-x-full': !sidebarOpen && !mobileSidebarOpen}"
               class="fixed md:relative z-30 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out md:translate-x-0 h-screen">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <img src="{{ asset('images/logo/datican_logo.png') }}" alt="DATICAN Logo" class="w-auto h-10">

                <button @click="sidebarOpen = !sidebarOpen; mobileSidebarOpen = false" 
                        class="md:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- User Profile -->
            <div class="px-4 py-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-primary flex items-center justify-center">
                        <span class="text-white font-bold text-lg">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="px-2 py-4">
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt text-gray-500 w-6"></i>
                            <span class="ml-3 font-medium">Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Registrations -->
                    <li>
                        <a href="{{ route('admin.registrations') }}" 
                           class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.registrations') || request()->routeIs('admin.registration.show') ? 'active' : '' }}">
                            <i class="fas fa-users text-gray-500 w-6"></i>
                            <span class="ml-3 font-medium">Registrations</span>
                            <span class="ml-auto bg-primary text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ App\Models\ConferenceRegistration::count() }}
                            </span>
                        </a>
                    </li>
                    
                    <!-- Export -->
                    <li>
                        <a href="{{ route('admin.export.registrations') }}" 
                           class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-file-export text-gray-500 w-6"></i>
                            <span class="ml-3 font-medium">Export All</span>
                        </a>
                    </li>
                    
                    <!-- Statistics -->
                    <li>
                        <a href="{{ route('conference.registration.stats') }}" 
                           class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors duration-200" target="_blank">
                            <i class="fas fa-chart-bar text-gray-500 w-6"></i>
                            <span class="ml-3 font-medium">Statistics</span>
                            <i class="ml-auto fas fa-external-link-alt text-xs text-gray-400"></i>
                        </a>
                    </li>
                    
                    <!-- Divider -->
                    <li class="pt-4">
                        <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Settings
                        </div>
                    </li>
                    
                    <!-- System -->
                    <li>
                        <a href="#" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-cog text-gray-500 w-6"></i>
                            <span class="ml-3 font-medium">System Settings</span>
                        </a>
                    </li>
                    
                    <!-- Logout -->
                    <li class="pt-4 mt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                <i class="fas fa-sign-out-alt w-6"></i>
                                <span class="ml-3 font-medium">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="absolute bottom-0 w-full px-4 py-3 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    DATICAN Conference 2026<br>
                    Admin Panel v1.0
                </p>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-3">
                    <!-- Left: Menu button for mobile -->
                    <div class="flex items-center">
                        <button @click="mobileSidebarOpen = true" 
                                class="md:hidden text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="ml-4">
                            <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                            <p class="text-sm text-gray-500">@yield('subtitle', 'DATICAN Conference Management')</p>
                        </div>
                    </div>
                    
                    <!-- Right: User actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>
                        
                        
                        
                        <!-- User dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-200 z-10" x-cloak>
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-circle mr-2"></i> My Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
                @endif
                
                @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Initialize tooltips and other UI enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Update sidebar active state based on current URL
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar-link').forEach(link => {
                if (link.href && link.href.includes(currentPath) && currentPath !== '/admin') {
                    link.classList.add('active');
                }
            });
            
            // Toggle mobile sidebar
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    document.querySelector('[x-data]').__x.$data.mobileSidebarOpen = false;
                }
            });
        });
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard: ' + text);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
        
        // Format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
    
    @stack('scripts')
</body>
</html>