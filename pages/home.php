<div class="hero-gradient text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight">
                <span class="block">Protect Our Planet</span>
                <span class="block mt-2 text-green-200">One Tree at a Time</span>
            </h1>
            <p class="mt-6 max-w-lg mx-auto text-xl text-green-100">
                Join our mission to plant trees, protect ecosystems, and build a sustainable future for Kenya.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <a href="?page=initiatives" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-600 bg-white hover:bg-green-50 md:py-4 md:text-lg md:px-10">
                    Join Initiative
                </a>
                <a href="#" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-700 hover:bg-green-800 md:py-4 md:text-lg md:px-10">
                    Donate Now
                </a>
                <a href="?page=marketplace" class="px-8 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-green-600 md:py-4 md:text-lg md:px-10">
                    Buy Seedlings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Counters -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                <div class="text-5xl font-bold text-green-600"><?php echo get_setting('trees_planted', '0'); ?></div>
                <div class="mt-2 text-lg font-medium text-gray-900">Trees Planted</div>
            </div>
            <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                <div class="text-5xl font-bold text-green-600"><?php echo get_setting('volunteers_count', '0'); ?></div>
                <div class="mt-2 text-lg font-medium text-gray-900">Volunteers</div>
            </div>
            <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                <div class="text-5xl font-bold text-green-600"><?php echo get_setting('partners_count', '0'); ?></div>
                <div class="mt-2 text-lg font-medium text-gray-900">Partners</div>
            </div>
        </div>
    </div>
</div>

<!-- Kenya Climatic Zones Preview -->
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Kenya's Climatic Zones</h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Explore Kenya's diverse ecosystems and discover which trees thrive in each region.
            </p>
        </div>

        <div class="mt-10">
            <div id="kenya-map" class="h-96 rounded-lg shadow-lg"></div>
        </div>
    </div>
</div>

<!-- Highlights Section -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Highlights</h2>
        </div>

        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Upcoming Events -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Upcoming Events</h3>
                    <ul class="space-y-4">
                        <li class="border-b pb-4">
                            <div class="font-medium">Nairobi Tree Planting</div>
                            <div class="text-sm text-gray-500">June 15, 2024 - Uhuru Park</div>
                        </li>
                        <li class="border-b pb-4">
                            <div class="font-medium">Coastal Reforestation</div>
                            <div class="text-sm text-gray-500">June 22, 2024 - Mombasa</div>
                        </li>
                        <li>
                            <div class="font-medium">Lake Victoria Cleanup</div>
                            <div class="text-sm text-gray-500">June 29, 2024 - Kisumu</div>
                        </li>
                    </ul>
                </div>

                <!-- Featured Partners -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Featured Partners</h3>
                    <div class="grid grid-cols-2 gap-4">
    <div class="text-center">
        <img src="admin/uploads/products/wwf.png" alt="WWF Kenya" class="w-16 h-16 mx-auto object-contain rounded-xl">
        <div class="mt-2 text-sm font-medium">WWF Kenya</div>
    </div>
    <div class="text-center">
        <img src="admin/uploads/products/unep.jpg" alt="UNEP" class="w-16 h-16 mx-auto object-contain rounded-xl">
        <div class="mt-2 text-sm font-medium">UNEP</div>
    </div>
    <div class="text-center">
        <img src="admin/uploads/products/kefri.jpg" alt="KEFRI" class="w-16 h-16 mx-auto object-contain rounded-xl">
        <div class="mt-2 text-sm font-medium">KEFRI</div>
    </div>
    <div class="text-center">
        <img src="admin/uploads/products/greenbelt.jpg" alt="Green Belt Movement" class="w-16 h-16 mx-auto object-contain rounded-xl">
        <div class="mt-2 text-sm font-medium">Green Belt Movement</div>
    </div>
</div>
                </div>

                <!-- Featured Products -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Featured Products</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
    <img src="admin/uploads/products/seeds.jpg" alt="Indigenous Seedlings Pack" class="w-16 h-16 rounded-xl object-cover">
    <div class="ml-4">
        <div class="font-medium">Indigenous Seedlings Pack</div>
        <div class="text-green-600 font-bold">KES 500</div>
    </div>
</div>

<div class="flex items-center">
    <img src="admin/uploads/products/manure.jpg" alt="Organic Manure" class="w-16 h-16 rounded-xl object-cover">
    <div class="ml-4">
        <div class="font-medium">Organic Manure</div>
        <div class="text-green-600 font-bold">KES 300</div>
    </div>
</div>

<div class="flex items-center">
    <img src="admin/uploads/products/treekit.png" alt="Tree Care Kit" class="w-16 h-16 rounded-xl object-cover">
    <div class="ml-4">
        <div class="font-medium">Tree Care Kit</div>
        <div class="text-green-600 font-bold">KES 800</div>
    </div>
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Initialize Leaflet map
document.addEventListener('DOMContentLoaded', function() {
    // Create map
    var map = L.map('kenya-map').setView([-0.0236, 37.9062], 6);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add Kenya outline (simplified)
    var kenyaOutline = L.polygon([
        [-4.692, 33.908],
        [5.202, 33.908],
        [5.202, 41.906],
        [-4.692, 41.906]
    ], {
        color: '#22c55e',
        fillColor: '#bbf7d0',
        fillOpacity: 0.3
    }).addTo(map);
    
    // Add climatic zones (simplified)
    var zones = [
        {name: "Coastal", coords: [[-4, 39], [-3, 40], [-4, 41]], color: "#16a34a"},
        {name: "Lowland", coords: [[-1, 36], [0, 37], [-1, 38]], color: "#22c55e"},
        {name: "Highland", coords: [[0, 35], [1, 36], [0, 37]], color: "#bbf7d0"}
    ];
    
    zones.forEach(function(zone) {
        var polygon = L.polygon(zone.coords, {
            color: zone.color,
            fillColor: zone.color,
            fillOpacity: 0.5
        }).addTo(map);
        
        polygon.bindPopup("<b>" + zone.name + " Zone</b><br>Click for details");
    });
});

// Dark mode toggle
document.getElementById('darkModeToggle').addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    var icon = document.getElementById('darkModeIcon');
    if (document.body.classList.contains('dark-mode')) {
        icon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
    } else {
        icon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
    }
});
</script>