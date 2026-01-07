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
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                <!-- Replace the D icon with your actual logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo/datican_logo.png') }}" alt="DATICAN Logo" class="w-auto h-10">
                    <div>
                    <span class="bg-primary text-white px-4 py-1 rounded-full text-sm font-semibold">Conference 2026</span>
                    </div>
                </a>
            </div>


                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary' : '' }}">Home</a>
                    <a href="{{ route('call-for-papers') }}" class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('call-for-papers') ? 'text-primary border-b-2 border-primary' : '' }}">Call for Papers</a>
                    <a href="{{ route('committees') }}" class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('committees') ? 'text-primary border-b-2 border-primary' : '' }}">Committee</a>
                    <a href="{{ route('register') }}" class="bg-accent text-white px-6 py-2 rounded-lg hover:bg-red-600 font-medium">Register</a>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('home') ? 'text-primary' : '' }}">Home</a>
                    <a href="{{ route('call-for-papers') }}" class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('call-for-papers') ? 'text-primary' : '' }}">Call for Papers</a>
                    <a href="{{ route('committees') }}" class="text-gray-700 hover:text-primary font-medium {{ request()->routeIs('committees') ? 'text-primary' : '' }}">Committee</a>
                    <a href="{{ route('register') }}" class="bg-accent text-white px-6 py-2 rounded-lg hover:bg-red-600 font-medium text-center">Register</a>
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
                    <p class="text-gray-300 mb-2"><i class="fas fa-envelope mr-2"></i> info@datican.org</p>
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
        });
    </script>
</body>
</html>