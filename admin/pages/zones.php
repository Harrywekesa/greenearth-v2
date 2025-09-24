<!-- pages/zones.php -->
<?php
global $connection;

// Get all active climatic zones
$stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$zones = $result->fetch_all(MYSQLI_ASSOC);

// Get zone statistics
$stmt = $connection->prepare("SELECT COUNT(*) as total_zones FROM climatic_zones WHERE is_active = 1");
$stmt->execute();
$result = $stmt->get_result();
$zone_stats = $result->fetch_assoc();

$cart_count = get_cart_count();
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Kenya's Climatic Zones</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Explore Kenya's diverse ecosystems and discover which trees thrive in each region.
            </p>
        </div>
        
        <!-- Zone Statistics -->
        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-5xl font-bold text-green-600"><?php echo number_format($zone_stats['total_zones']); ?></div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Climatic Zones</div>
                </div>
                <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-5xl font-bold text-green-600">8</div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Major Regions</div>
                </div>
                <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-5xl font-bold text-green-600">50+</div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Tree Species</div>
                </div>
            </div>
        </div>
        
        <!-- Interactive Map -->
        <div class="mt-16">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Explore Kenya's Ecosystems</h2>
            </div>
            <div id="kenya-map" class="h-96 rounded-lg shadow-lg"></div>
        </div>
        
        <!-- Zones Grid -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">All Climatic Zones</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($zones as $zone): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if($zone['image'] && file_exists('uploads/zones/' . $zone['image'])): ?>
                        <img src="uploads/zones/<?php echo $zone['image']; ?>" alt="<?php echo htmlspecialchars($zone['name']); ?>" class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500"><?php echo htmlspecialchars($zone['name']); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($zone['name']); ?></h3>
                        <p class="mt-2 text-gray-600"><?php echo substr(htmlspecialchars($zone['description']), 0, 100); ?>...</p>
                        <div class="mt-4 flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <?php echo htmlspecialchars($zone['vegetation_type']); ?>
                            </span>
                            <a href="?page=zone_detail&id=<?php echo $zone['id']; ?>" class="ml-4 text-green-600 hover:text-green-800 font-medium">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

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
</script>