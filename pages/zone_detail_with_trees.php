<!-- pages/zone_detail_with_trees.php -->
<?php
global $connection;
$zone_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

if($zone_id > 0) {
    // Get zone details
    $stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $zone_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $zone = $result->fetch_assoc();
    
    if(!$zone) {
        header("Location: ?page=zones_with_trees");
        exit;
    }
    
    // Get recommended trees for this zone
    $stmt = $connection->prepare("SELECT * FROM products WHERE category = 'seedlings' AND is_active = 1 AND name LIKE ? ORDER BY name");
    $search_term = '%' . substr($zone['name'], 0, 3) . '%'; // Simple search by zone name
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $trees = $result->fetch_all(MYSQLI_ASSOC);
    
    // If no trees found by name, get all seedlings
    if(empty($trees)) {
        $stmt = $connection->prepare("SELECT * FROM products WHERE category = 'seedlings' AND is_active = 1 ORDER BY name");
        $stmt->execute();
        $result = $stmt->get_result();
        $trees = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get climate data for charts (mock data for demonstration)
    $climate_data = [
        'rainfall' => [50, 40, 60, 120, 180, 80, 30, 20, 40, 100, 150, 90],
        'temperature' => [25, 26, 27, 28, 26, 24, 23, 23, 24, 25, 26, 25]
    ];
} else {
    header("Location: ?page=zones_with_trees");
    exit;
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="?page=home" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            <span class="sr-only">Home</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="?page=zones_with_trees" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Climatic Zones</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500" aria-current="page"><?php echo htmlspecialchars($zone['name']); ?></span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <h1 class="text-3xl font-extrabold text-gray-900"><?php echo htmlspecialchars($zone['name']); ?> Zone</h1>
                    
                    <?php if($zone['image'] && file_exists('uploads/zones/' . $zone['image'])): ?>
                        <img src="uploads/zones/<?php echo $zone['image']; ?>" alt="<?php echo htmlspecialchars($zone['name']); ?>" class="mt-6 w-full rounded-lg">
                    <?php endif; ?>
                    
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900">Description</h2>
                        <div class="mt-4 prose max-w-none text-gray-600">
                            <?php echo nl2br(htmlspecialchars($zone['description'])); ?>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900">Climate Characteristics</h2>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">Rainfall Pattern</h3>
                                <p class="mt-2 text-gray-600"><?php echo htmlspecialchars($zone['rainfall_pattern']); ?></p>
                                <div class="mt-4">
                                    <canvas id="rainfallChart" height="200"></canvas>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">Temperature Trends</h3>
                                <p class="mt-2 text-gray-600">Average temperatures throughout the year</p>
                                <div class="mt-4">
                                    <canvas id="temperatureChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900">Recommended Tree Species</h2>
                        <?php if(!empty($trees)): ?>
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach($trees as $tree): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <?php if($tree['image'] && file_exists('uploads/products/' . $tree['image'])): ?>
                                    <img src="uploads/products/<?php echo $tree['image']; ?>" alt="<?php echo htmlspecialchars($tree['name']); ?>" class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                                        <span class="text-gray-500"><?php echo ucfirst($tree['category']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($tree['name']); ?></h3>
                                            <p class="mt-1 text-sm text-gray-500 capitalize"><?php echo htmlspecialchars($tree['category']); ?></p>
                                        </div>
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
                                    
                                    <div class="mt-4">
                                        <p class="text-gray-600 text-sm"><?php echo substr(htmlspecialchars($tree['description']), 0, 80); ?>...</p>
                                    </div>
                                    
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-xl font-bold text-gray-900"><?php echo format_currency($tree['price']); ?></span>
                                        <?php if($tree['stock_quantity'] > 0): ?>
                                        <form method="POST" action="?page=add_to_cart">
                                            <input type="hidden" name="product_id" value="<?php echo $tree['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                                                Add to Cart
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded text-red-700 bg-red-100">
                                            Out of Stock
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if($tree['planting_tips']): ?>
                                    <div class="mt-3">
                                        <details class="text-sm text-gray-500">
                                            <summary class="cursor-pointer">Planting Tips</summary>
                                            <p class="mt-1"><?php echo htmlspecialchars($tree['planting_tips']); ?></p>
                                        </details>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-4">
                                        <a href="?page=product_detail&id=<?php echo $tree['id']; ?>" class="text-sm font-medium text-green-600 hover:text-green-500">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="mt-6 text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m16-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">No recommended trees</h3>
                            <p class="mt-1 text-gray-500">No tree species have been recommended for this zone yet.</p>
                            <div class="mt-6">
                                <a href="?page=marketplace" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    Browse All Seedlings
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900">Zone Characteristics</h2>
                        <dl class="mt-4 space-y-4">
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
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($zone['location']); ?></dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div class="mt-8 bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900">Planting Guidelines</h2>
                        <div class="mt-4">
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2">Plant during rainy seasons</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2">Water regularly for first 3 months</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2">Protect from livestock</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2">Prune annually for healthy growth</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-8 bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900">Zone Statistics</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <div class="flex justify-between text-sm font-medium text-gray-700">
                                    <span>Trees Planted</span>
                                    <span><?php echo number_format($zone['planted_trees']); ?></span>
                                </div>
                                <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: <?php echo calculate_progress($zone['planted_trees'], $zone['target_trees']); ?>%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm font-medium text-gray-700">
                                    <span>Target Trees</span>
                                    <span><?php echo number_format($zone['target_trees']); ?></span>
                                </div>
                                <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm font-medium text-gray-700">
                                    <span>Volunteers</span>
                                    <span><?php echo number_format($zone['volunteers_count']); ?></span>
                                </div>
                                <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-purple-600 h-2.5 rounded-full" style="width: <?php echo calculate_progress($zone['volunteers_count'], 500); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize climate charts
document.addEventListener('DOMContentLoaded', function() {
    // Rainfall chart
    var rainfallCtx = document.getElementById('rainfallChart').getContext('2d');
    var rainfallChart = new Chart(rainfallCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Rainfall (mm)',
                data: [<?php echo implode(',', $climate_data['rainfall']); ?>],
                backgroundColor: '#22c55e'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Temperature chart
    var tempCtx = document.getElementById('temperatureChart').getContext('2d');
    var tempChart = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Average Temperature (°C)',
                data: [<?php echo implode(',', $climate_data['temperature']); ?>],
                borderColor: '#16a34a',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
});
</script>