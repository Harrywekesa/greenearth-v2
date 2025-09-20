<!-- includes/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_setting('site_name', 'GreenEarth'); ?></title>
    <meta name="description" content="<?php echo get_setting('site_description', 'Environmental care platform'); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Chart.js for charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #22c55e;
            --dark-green: #166534;
            --light-green: #bbf7d0;
            --earth-brown: #92400e;
        }
        
        .dark-mode {
            --bg-color: #1f2937;
            --text-color: #f9fafb;
            --card-bg: #374151;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
        }
        
        .counter-card {
            transition: transform 0.3s ease;
        }
        
        .counter-card:hover {
            transform: translateY(-5px);
        }
        
        /* Navigation styles */
        .nav-link {
            @apply text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium;
        }
        
        .nav-link.active {
            @apply bg-green-100 text-green-700;
        }
        
        /* Cart badge */
        .cart-badge {
            @apply absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="?page=home" class="text-2xl font-bold text-green-600">🌱 GreenEarth</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="?page=home" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] === 'home') ? 'active' : ''; ?>">Home</a>
                        <a href="?page=zones" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'zones') ? 'active' : ''; ?>">Climatic Zones</a>
                        <a href="?page=initiatives" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'initiatives') ? 'active' : ''; ?>">Initiatives</a>
                        <a href="?page=events" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'events') ? 'active' : ''; ?>">Events</a>
                        <a href="?page=marketplace" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'marketplace') ? 'active' : ''; ?>">Marketplace</a>
                        <a href="?page=blog" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'blog') ? 'active' : ''; ?>">Blog</a>
                        <a href="?page=contact" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'contact') ? 'active' : ''; ?>">Contact</a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <!-- Cart Button -->
                    <?php 
                    $cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
                    ?>
                    <a href="?page=cart" class="relative p-2 rounded-full text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <?php if($cart_count > 0): ?>
                        <span class="cart-badge">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="p-2 rounded-full text-gray-400 hover:text-gray-500 ml-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path id="darkModeIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    
                    <!-- User Menu -->
                    <?php if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                    <div class="ml-3 relative">
                        <div>
                            <button class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-green-800 font-medium"><?php echo substr($_SESSION['user_name'], 0, 1); ?></span>
                                </div>
                            </button>
                        </div>
                        
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" id="user-menu" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                            <a href="?page=profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                            <a href="?page=orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Orders</a>
                            <a href="?page=logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="?page=login" class="ml-4 bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">Login</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button id="mobile-menu-button" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="?page=home" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] === 'home') ? 'active' : ''; ?> block">Home</a>
                <a href="?page=zones" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'zones') ? 'active' : ''; ?> block">Climatic Zones</a>
                <a href="?page=initiatives" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'initiatives') ? 'active' : ''; ?> block">Initiatives</a>
                <a href="?page=events" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'events') ? 'active' : ''; ?> block">Events</a>
                <a href="?page=marketplace" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'marketplace') ? 'active' : ''; ?> block">Marketplace</a>
                <a href="?page=blog" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'blog') ? 'active' : ''; ?> block">Blog</a>
                <a href="?page=contact" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] === 'contact') ? 'active' : ''; ?> block">Contact</a>
                
                <!-- Mobile Cart -->
                <a href="?page=cart" class="relative nav-link block">
                    Cart
                    <?php if($cart_count > 0): ?>
                    <span class="cart-badge">
                        <?php echo $cart_count; ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <?php if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                <a href="?page=profile" class="nav-link block">Profile</a>
                <a href="?page=orders" class="nav-link block">Orders</a>
                <a href="?page=logout" class="nav-link block">Sign out</a>
                <?php else: ?>
                <a href="?page=login" class="nav-link block">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
    
    // User menu toggle
    if(document.getElementById('user-menu-button')) {
        document.getElementById('user-menu-button').addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });
    }
    
    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        // Close user menu
        if(document.getElementById('user-menu') && !document.getElementById('user-menu').classList.contains('hidden')) {
            if(!event.target.closest('#user-menu-button') && !event.target.closest('#user-menu')) {
                document.getElementById('user-menu').classList.add('hidden');
            }
        }
        
        // Close mobile menu
        if(!document.getElementById('mobile-menu').classList.contains('hidden')) {
            if(!event.target.closest('#mobile-menu-button') && !event.target.closest('#mobile-menu')) {
                document.getElementById('mobile-menu').classList.add('hidden');
            }
        }
    });
    
    // Dark mode toggle
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        var icon = document.getElementById('darkModeIcon');
        if(document.body.classList.contains('dark-mode')) {
            icon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z');
        } else {
            icon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
        }
    });
    </script>
</body>
</html>