<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DATICAN Conference') - Improving Medical Diagnostics in Nigeria Using AI and Data Science</title>
    <link
        rel="icon"
        href="{{ asset('images/logo/datican_logo_io.png') }}"
        type="image/png"
    >
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2C3E50',
                        secondary: '#1A252F',
                        accent: '#E74C3C',
                        blue: {
                            50: '#eff6ff',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                    
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Additional custom styles */
        .gradient-bg {
            background: linear-gradient(135deg, #2C3E50 0%, #1A252F 100%);
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Image Slider with 100% Zoom & Perfect Switch */
        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            z-index: 1;
            overflow: hidden;
        }

        .slide.active {
            opacity: 1;
            z-index: 2;
        }

        .zoom-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1);
            transition: transform 8s linear; /* Slower zoom (8 seconds) */
        }

        .slide.active .zoom-image {
            transform: scale(1.2); /* Zoom to 120% (or adjust as needed) */
        }

        /* Optional: Very subtle continuous breathing effect */
        @keyframes subtleBreathing {
            0%, 100% {
                transform: scale(1.2);
            }
            50% {
                transform: scale(1.21); /* Very subtle pulse */
            }
        }

        .slide.active .zoom-image {
            animation: subtleBreathing 4s ease-in-out infinite;
            animation-delay: 8s; /* Start after zoom completes */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .relative.h-\[500px\].md\:h-\[700px\] {
                height: 400px !important;
            }
            
            .slide.active .zoom-image {
                transform: scale(1.15); /* Less zoom on mobile */
            }
            
            /* Mobile logo adjustments */
            .mobile-logo {
                max-width: 120px;
            }
            
            .mobile-logo-text {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
        }
        
        @media (max-width: 640px) {
            .mobile-logo {
                max-width: 100px;
            }
            
            .mobile-logo-text {
                font-size: 0.6rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo - Responsive for mobile -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo/datican_logo.png') }}" 
                         alt="DATICAN Logo" 
                         class="w-auto h-8 md:h-10 mobile-logo">
                    <div class="hidden sm:block">
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-xs md:text-sm font-semibold mobile-logo-text">
                            Conference 2026
                        </span>
                    </div>
                </a>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-6 lg:space-x-8 items-center">
                    <!-- Common links -->
                    <a href="{{ route('home') }}" 
                    class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('call-for-papers') }}" 
                    class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('call-for-papers') ? 'text-primary border-b-2 border-primary' : '' }}">
                        Call for Papers
                    </a>
                    <a href="{{ route('committees') }}" 
                    class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('committees') ? 'text-primary border-b-2 border-primary' : '' }}">
                        Committee
                    </a>
                    <a href="https://datican.org/about.php" 
                    class="text-gray-700 hover:text-primary font-medium {{ str_contains(request()->path(), 'about.php') ? 'text-primary border-b-2 border-primary' : '' }}">
                        About DATICAN
                    </a>
                    
                    <!-- Auth Links -->
                    @auth
                        @php
                            $hasConferenceRegistration = \App\Models\ConferenceRegistration::where('user_id', auth()->id())->exists();
                        @endphp
                        
                        <!-- Unified Dashboard Link -->
                        <a href="{{ route('dashboard') }}" 
                        class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('dashboard*') ? 'text-primary border-b-2 border-primary' : '' }}">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        
                        <!-- Optional: Quick access links based on role -->
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('assignments.index') }}" 
                            class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('assignments*') ? 'text-primary border-b-2 border-primary' : '' }}">
                                <i class="fas fa-tasks mr-1"></i> Assignments
                            </a>
                        @endif
                        
                        <!-- User dropdown with Alpine.js -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    @click.away="open = false"
                                    class="flex items-center space-x-2 text-gray-700 hover:text-primary focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-700">
                                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <span class="hidden lg:inline">{{ auth()->user()->first_name }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200" 
                                    :class="{ 'rotate-180': open }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border z-50">
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    @if($hasConferenceRegistration)
                                        <span class="inline-flex items-center mt-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Conference Registered
                                        </span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('dashboard') }}" 
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt w-5 mr-2"></i> My Dashboard
                                </a>
                                <a href="{{ route('profile') }}" 
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user w-5 mr-2"></i> Profile
                                </a>
                                
                                @if(auth()->user()->is_admin)
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <p class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</p>
                                        <a href="{{ route('admin.conference.dashboard') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-calendar-check w-5 mr-2"></i> Conference Dashboard
                                        </a>
                                        <a href="{{ route('assignments.index') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-tasks w-5 mr-2"></i> Assignments
                                        </a>
                                        <a href="{{ route('users.index') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-users-cog w-5 mr-2"></i> User Management
                                        </a>
                                    </div>
                                @endif
                                
                                @if(!auth()->user()->is_admin)
                                    <!-- Show papers to all non-admin users -->
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <a href="{{ route('papers.index') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-file-alt w-5 mr-2"></i> My Papers
                                        </a>
                                        
                                        <!-- Show bidding and reviews only to reviewers -->
                                        @if(auth()->user()->is_reviewer)
                                            <a href="{{ route('bidding.index') }}" 
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-hand-paper w-5 mr-2"></i> Bidding
                                            </a>
                                            <a href="{{ route('reviews.my') }}" 
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-clipboard-check w-5 mr-2"></i> My Reviews
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Only show Conference Registration link if user hasn't registered yet -->
                                @if(!$hasConferenceRegistration)
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <a href="{{ route('conference.registration') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-calendar-plus w-5 mr-2"></i> Register for Conference
                                        </a>
                                    </div>
                                @else
                                    <!-- If already registered, show a link to view registration -->
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <a href="{{ route('conference.registration.view') }}" 
                                        class="flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                            <i class="fas fa-calendar-check w-5 mr-2"></i> View Registration
                                        </a>
                                    </div>
                                @endif
                                
                                <div class="border-t border-gray-200">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt w-5 mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('register') }}" 
                        class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('register') ? 'text-primary border-b-2 border-primary' : '' }}">
                            Register
                        </a>
                        <a href="{{ route('login') }}" 
                        class="bg-accent text-white px-4 lg:px-6 py-2 rounded-lg hover:bg-red-600 font-medium">
                            Login
                        </a>
                    @endauth
                </div>

                <!-- Mobile Navigation -->
                <div id="mobile-menu" class="hidden md:hidden py-4 border-t">
                    <div class="flex flex-col space-y-3">
                        <a href="{{ route('home') }}" 
                        class="text-gray-700 hover:text-primary font-medium py-2 {{ request()->routeIs('home') ? 'text-primary' : '' }}">
                            Home
                        </a>
                        <a href="{{ route('call-for-papers') }}" 
                        class="text-gray-700 hover:text-primary font-medium py-2 {{ request()->routeIs('call-for-papers') ? 'text-primary' : '' }}">
                            Call for Papers
                        </a>
                        <a href="{{ route('committees') }}" 
                        class="text-gray-700 hover:text-primary font-medium py-2 {{ request()->routeIs('committees') ? 'text-primary' : '' }}">
                            Committee
                        </a>
                        <a href="https://datican.org/about.php" target="_blank" rel="noopener"
                        class="text-gray-700 hover:text-primary font-medium {{ str_contains(request()->path(), 'about.php') ? 'text-primary border-b-2 border-primary' : '' }}">
                            About DATICAN
                        </a>
                        
                        @auth
                            @php
                                $hasConferenceRegistration = \App\Models\ConferenceRegistration::where('user_id', auth()->id())->exists();
                            @endphp
                            
                            <a href="{{ route('dashboard') }}" 
                            class="text-gray-700 hover:text-primary font-medium py-2 {{ request()->routeIs('dashboard*') ? 'text-primary' : '' }}">
                                <i class="fas fa-tachometer-alt mr-2"></i> My Dashboard
                            </a>
                            
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('assignments.index') }}" 
                                class="text-gray-700 hover:text-primary font-medium py-2 {{ request()->routeIs('assignments*') ? 'text-primary' : '' }}">
                                    <i class="fas fa-tasks mr-2"></i> Assignments
                                </a>
                            @else
                                <a href="{{ route('papers.index') }}" 
                                class="text-gray-700 hover:text-primary font-medium py-2">
                                    <i class="fas fa-file-alt mr-2"></i> My Papers
                                </a>
                                
                                <!-- Show bidding and reviews only to reviewers in mobile menu -->
                                @if(auth()->user()->is_reviewer)
                                    <a href="{{ route('bidding.index') }}" 
                                    class="text-gray-700 hover:text-primary font-medium py-2">
                                        <i class="fas fa-hand-paper mr-2"></i> Bidding
                                    </a>
                                    <a href="{{ route('reviews.my') }}" 
                                    class="text-gray-700 hover:text-primary font-medium py-2">
                                        <i class="fas fa-clipboard-check mr-2"></i> My Reviews
                                    </a>
                                @endif
                            @endif
                            
                            <a href="{{ route('profile') }}" 
                            class="text-gray-700 hover:text-primary font-medium py-2">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            
                            <!-- Only show Conference Registration link if user hasn't registered yet (Mobile) -->
                            @if(!$hasConferenceRegistration)
                                <a href="{{ route('conference.registration') }}" 
                                class="text-gray-700 hover:text-primary font-medium py-2">
                                    <i class="fas fa-calendar-plus mr-2"></i> Conference Registration
                                </a>
                            @else
                                <!-- If already registered, show a link to view registration (Mobile) -->
                                <a href="{{ route('conference.registration.view') }}" 
                                class="text-green-700 hover:text-green-800 font-medium py-2">
                                    <i class="fas fa-calendar-check mr-2"></i> View Registration
                                </a>
                                <div class="ml-6 text-xs text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> Conference Registered
                                </div>
                            @endif
                            
                            <div class="pt-4 border-t">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left text-gray-700 hover:text-primary font-medium py-2">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="space-y-3 pt-2">
                                <a href="{{ route('register') }}" 
                                class="block text-center bg-accent text-white px-6 py-3 rounded-lg hover:bg-red-600 font-medium">
                                    Register
                                </a>
                                <a href="{{ route('login') }}" 
                                class="block text-center border border-accent text-accent px-6 py-3 rounded-lg hover:bg-red-50 font-medium">
                                    Login
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">DATICAN Conference</h3>
                    <p class="text-gray-300">Improving Medical Diagnostics in Nigeria Using AI and Data Science</p>
                    <div class="mt-6 flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-linkedin text-xl"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="{{ route('call-for-papers') }}" class="text-gray-300 hover:text-white">Call for Papers</a></li>
                        <li><a href="{{ route('committees') }}" class="text-gray-300 hover:text-white">Committees</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-white">Registration</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <p class="text-gray-300 mb-2"><i class="fas fa-envelope mr-2"></i> manager.datican@gmail.com</p>
                    <p class="text-gray-300"><i class="fas fa-calendar-alt mr-2"></i> 13th - 14th May, 2026</p>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2026 DATICAN Conference. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            
            // Change icon
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Image Slider with 100% Zoom & Perfect Switch
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('.slide');
            const images = document.querySelectorAll('.zoom-image');
            let current = 0;
            const zoomDuration = 8000; // 8 seconds to match CSS
            
            function showSlide(index) {
                // Reset all slides
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    slide.style.opacity = '0';
                    slide.style.zIndex = '1';
                    
                    // Reset zoom for all images
                    images[i].style.transform = 'scale(1)';
                    images[i].style.transition = 'none';
                });
                
                // Get current slide and image
                const currentSlide = slides[index];
                const currentImage = images[index];
                
                // Activate current slide
                currentSlide.classList.add('active');
                    currentSlide.style.opacity = '1';
                    currentSlide.style.zIndex = '2';
                    
                    // Force reflow to reset animation
                    void currentImage.offsetWidth;
                    
                    // Start zoom animation
                    currentImage.style.transition = `transform ${zoomDuration}ms linear`;
                    currentImage.style.transform = 'scale(1.2)';
                    
                    // Schedule next slide at exact moment zoom reaches 100%
                    setTimeout(() => {
                        // Switch to next slide
                        current = (current + 1) % slides.length;
                        showSlide(current);
                    }, zoomDuration); // Switch EXACTLY when zoom completes
                }
                
                // Start the slider
                if (slides.length > 0) {
                    showSlide(current);
                }
                
                // Optional: Add keyboard navigation (space/arrows for manual control)
                document.addEventListener('keydown', (e) => {
                    if (e.code === 'Space' || e.code === 'ArrowRight') {
                        current = (current + 1) % slides.length;
                        showSlide(current);
                    } else if (e.code === 'ArrowLeft') {
                        current = (current - 1 + slides.length) % slides.length;
                        showSlide(current);
                    }
                });
            });
    </script>
</body>
</html>