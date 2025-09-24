<!-- pages/zones.php -->
<?php
// Fetch all active climatic zones
$stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$zones = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Kenya's Climatic Zones</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Explore Kenya's diverse ecosystems and discover which trees thrive in each region.
            </p>
        </div>

        <!-- Interactive Map -->
        <div class="mt-10">
            <div id="zones-map" class="h-96 rounded-lg shadow-lg"></div>
        </div>

        <!-- Zones Grid -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">All Climatic Zones</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($zones as $zone): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if($zone['image']): ?>
                        <img src="admin/uploads/zones/<?php echo htmlspecialchars($zone['image']); ?>" 
     alt="<?php echo htmlspecialchars($zone['name']); ?>" 
     class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500">Zone Image</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900"><?php echo $zone['name']; ?></h3>
                        <p class="mt-2 text-gray-600"><?php echo substr($zone['description'], 0, 100); ?>...</p>
                        <div class="mt-4 flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <?php echo $zone['vegetation_type']; ?>
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
// Initialize zones map
document.addEventListener('DOMContentLoaded', function() {
    // Create map centered on Kenya
    var map = L.map('zones-map').setView([-0.0236, 37.9062], 6);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add zone polygons (simplified for demo)
    var zones = [
        {
            name: "Coastal Plain",
            coords: [[-4.5, 39], [-3.5, 39], [-3.5, 41], [-4.5, 41]],
            color: "#16a34a",
            description: "Hot and humid with high rainfall"
        },
        {
            name: "Lowland",
            coords: [[-1, 36], [0, 36], [0, 38], [-1, 38]],
            color: "#22c55e",
            description: "Moderate temperatures with seasonal rains"
        },
        {
            name: "Highland",
            coords: [[0, 35], [1, 35], [1, 37], [0, 37]],
            color: "#bbf7d0",
            description: "Cool temperatures with bimodal rainfall"
        }
    ];
    
    zones.forEach(function(zone) {
        var polygon = L.polygon(zone.coords, {
            color: zone.color,
            fillColor: zone.color,
            fillOpacity: 0.5
        }).addTo(map);
        
        polygon.bindPopup("<b>" + zone.name + "</b><br>" + zone.description);
    });
});
</script>