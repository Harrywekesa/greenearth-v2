<!-- pages/zone_detail.php -->
<?php
global $connection;
$zone_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

if($zone_id > 0) {
    $stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $zone_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $zone = $result->fetch_assoc();
    
    if(!$zone) {
        header("Location: ?page=zones");
        exit;
    }
    
    // Get recommended trees for this zone
    $stmt = $connection->prepare("SELECT * FROM products WHERE category = 'seedlings' AND is_active = 1 LIMIT 6");
    $stmt->execute();
    $result = $stmt->get_result();
    $recommended_trees = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get initiatives in this zone
    $stmt = $connection->prepare("SELECT * FROM initiatives WHERE location LIKE ? AND is_active = 1 ORDER BY start_date DESC LIMIT 3");
    $search_location = '%' . $zone['name'] . '%';
    $stmt->bind_param("s", $search_location);
    $stmt->execute();
    $result = $stmt->get_result();
    $zone_initiatives = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: ?page=zones");
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
                        <a href="?page=zones" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Climatic Zones</a>
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

                    <!-- Climate Data Charts -->
                    <div class="mt-10">
                        <h2 class="text-2xl font-bold text-gray-900">Climate Data</h2>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">Rainfall Pattern</h3>
                                <canvas id="rainfallChart" height="200"></canvas>
                            </div>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">Temperature Trends</h3>
                                <canvas id="temperatureChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Predicted Trends -->
                    <div class="mt-10">
                        <h2 class="text-2xl font-bold text-gray-900">Predicted Trends</h2>
                        <div class="mt-4 bg-green-50 p-6 rounded-lg">
                            <p class="text-gray-700">
                                Based on climate models, this zone is expected to experience:
                            </p>
                            <ul class="mt-2 list-disc pl-5 space-y-1">
                                <li>Increased temperatures by 1.5°C over the next 20 years</li>
                                <li>Reduced rainfall during the long rains season</li>
                                <li>Extended dry periods between rainfall seasons</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-lg p-6">
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
                                <dt class="text-sm font-medium text-gray-500">Vegetation</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($zone['vegetation_type']); ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900">Recommended Trees</h2>
                        <div class="mt-4 space-y-4">
                            <?php foreach($recommended_trees as $tree): ?>
                            <div class="flex items-center">
                                <?php if($tree['image'] && file_exists('uploads/products/' . $tree['image'])): ?>
                                    <img src="uploads/products/<?php echo $tree['image']; ?>" alt="<?php echo htmlspecialchars($tree['name']); ?>" class="h-16 w-16 object-cover rounded-md">
                                <?php else: ?>
                                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs"><?php echo ucfirst(substr($tree['category'], 0, 10)); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($tree['name']); ?></div>
                                    <div class="text-green-600 font-bold"><?php echo format_currency($tree['price']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6">
                            <a href="?page=marketplace" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Shop Seedlings
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-8 bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900">Zone Initiatives</h2>
                        <div class="mt-4 space-y-4">
                            <?php foreach($zone_initiatives as $initiative): ?>
                            <div class="border-b pb-4">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($initiative['title']); ?></div>
                                <div class="text-sm text-gray-500">
                                    <?php echo date('M j, Y', strtotime($initiative['start_date'])); ?> - 
                                    <?php echo htmlspecialchars($initiative['location']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6">
                            <a href="?page=initiatives" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                View All Initiatives
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Climate charts
document.addEventListener('DOMContentLoaded', function() {
    // Rainfall chart
    var rainfallCtx = document.getElementById('rainfallChart').getContext('2d');
    var rainfallChart = new Chart(rainfallCtx, {
        type: 'bar',
         {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Rainfall (mm)',
                 [50, 40, 60, 120, 180, 80, 30, 20, 40, 100, 150, 90],
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
         {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Average Temperature (°C)',
                data: [25, 26, 27, 28, 26, 24, 23, 23, 24, 25, 26, 25],
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