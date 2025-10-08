<!-- pages/zones_with_trees.php -->
<?php
global $connection;
include '../includes/config.php';
// Get all active climatic zones
$stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$zones = $result->fetch_all(MYSQLI_ASSOC);

// Get tree species for each zone (this is a simplified approach - in a real system, you'd have a relationship table)
$zone_trees = [];
foreach($zones as $zone) {
    // Get recommended trees for this zone based on category or other criteria
    $stmt = $connection->prepare("SELECT * FROM products WHERE category = 'seedlings' AND is_active = 1 AND name LIKE ? ORDER BY name LIMIT 6");
    $search_term = '%' . substr($zone['name'], 0, 3) . '%'; // Simple search by zone name
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $zone_trees[$zone['id']] = $result->fetch_all(MYSQLI_ASSOC);
    
    // If no trees found by name, get generic trees
    if(empty($zone_trees[$zone['id']])) {
        $stmt = $connection->prepare("SELECT * FROM products WHERE category = 'seedlings' AND is_active = 1 ORDER BY name LIMIT 6");
        $stmt->execute();
        $result = $stmt->get_result();
        $zone_trees[$zone['id']] = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Kenya's Climatic Zones & Recommended Trees</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Discover which tree species thrive in each of Kenya's diverse climatic zones.
            </p>
        </div>
        
        <!-- Zones with Trees -->
        <div class="mt-16">
            <?php foreach($zones as $zone): ?>
            <div class="mt-12">
                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($zone['name']); ?> Zone</h2>
                        <a href="?page=zone_detail&id=<?php echo $zone['id']; ?>" class="text-sm font-medium text-green-600 hover:text-green-500">
                            View Zone Details →
                        </a>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <div class="bg-white rounded-lg shadow p-4">
                                <h3 class="text-lg font-medium text-gray-900">Zone Characteristics</h3>
                                <dl class="mt-2 space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Rainfall Pattern</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($zone['rainfall_pattern']); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Soil Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($zone['soil_type']); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Vegetation Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($zone['vegetation_type']); ?></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900">Recommended Tree Species</h3>
                            <?php if(!empty($zone_trees[$zone['id']])): ?>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach($zone_trees[$zone['id']] as $tree): ?>
                                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
                                    <?php if($tree['image'] && file_exists('uploads/products/' . $tree['image'])): ?>
                                        <img src="uploads/products/<?php echo $tree['image']; ?>" alt="<?php echo htmlspecialchars($tree['name']); ?>" class="h-24 w-full object-cover rounded-md">
                                    <?php else: ?>
                                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-24 flex items-center justify-center">
                                            <span class="text-gray-500 text-xs"><?php echo ucfirst($tree['category']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <h4 class="text-md font-bold text-gray-900"><?php echo htmlspecialchars($tree['name']); ?></h4>
                                        <p class="mt-1 text-sm text-gray-500 capitalize"><?php echo htmlspecialchars($tree['category']); ?></p>
                                        <div class="mt-2 flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-900"><?php echo format_currency($tree['price']); ?></span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                switch($tree['stock_quantity']) {
                                                    case ($tree['stock_quantity'] > 10): echo 'bg-green-100 text-green-800'; break;
                                                    case ($tree['stock_quantity'] > 0): echo 'bg-yellow-100 text-yellow-800'; break;
                                                    default: echo 'bg-red-100 text-red-800';
                                                }
                                                ?>">
                                                <?php echo $tree['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                            </span>
                                        </div>
                                        <?php if($tree['planting_tips']): ?>
                                        <div class="mt-2">
                                            <details class="text-sm text-gray-500">
                                                <summary class="cursor-pointer">Planting Tips</summary>
                                                <p class="mt-1"><?php echo htmlspecialchars(substr($tree['planting_tips'], 0, 80)); ?>...</p>
                                            </details>
                                        </div>
                                        <?php endif; ?>
                                        <div class="mt-3">
                                            <a href="?page=product_detail&id=<?php echo $tree['id']; ?>" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="mt-4 text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m16-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="mt-2 text-lg font-medium text-gray-900">No recommended trees</h3>
                                <p class="mt-1 text-gray-500">No tree species have been recommended for this zone yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="mt-16 bg-gray-50 rounded-lg p-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-900">Want to Plant Trees?</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Purchase quality seedlings and eco-friendly products to support your planting efforts.
                </p>
                <div class="mt-6">
                    <a href="?page=marketplace" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Shop Seedlings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>