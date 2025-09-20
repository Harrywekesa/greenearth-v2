<!-- pages/tree-tracking.php -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Tree Tracking</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                See where trees have been planted and track their growth
            </p>
        </div>
        
        <!-- Interactive Map -->
        <div class="mt-10">
            <div id="tree-map" class="h-96 rounded-lg shadow-lg"></div>
        </div>
        
        <!-- Tree Search -->
        <div class="mt-8 max-w-2xl mx-auto">
            <div class="flex">
                <input type="text" placeholder="Search by tree ID, location, or species" class="flex-1 min-w-0 block w-full px-4 py-2 border border-gray-300 rounded-l-md shadow-sm focus:ring-green-500 focus:border-green-500">
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Search
                </button>
            </div>
        </div>
        
        <!-- Recent Plantings -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900">Recently Planted Trees</h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php for($i = 1; $i <= 6; $i++): ?>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16"></div>
                        <div class="ml-4">
                            <h3 class="font-bold text-gray-900">Acacia Tree #<?php echo $i; ?></h3>
                            <p class="text-sm text-gray-600">Planted 3 days ago</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between text-sm">
                        <span class="text-gray-500">Location:</span>
                        <span class="font-medium">Nairobi</span>
                    </div>
                    <div class="mt-1 flex justify-between text-sm">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium text-green-600">Healthy</span>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tree tracking map
document.addEventListener('DOMContentLoaded', function() {
    // Create map
    var map = L.map('tree-map').setView([-1.2921, 36.8219], 12);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add sample tree markers
    var trees = [
        {lat: -1.2921, lng: 36.8219, species: 'Acacia', status: 'Healthy'},
        {lat: -1.2950, lng: 36.8250, species: 'Neem', status: 'Growing'},
        {lat: -1.2890, lng: 36.8180, species: 'Mango', status: 'Healthy'}
    ];
    
    trees.forEach(function(tree) {
        var marker = L.marker([tree.lat, tree.lng]).addTo(map);
        marker.bindPopup(`<b>${tree.species} Tree</b><br>Status: ${tree.status}`);
    });
});
</script>