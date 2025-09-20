<!-- pages/admin/reports.php -->
<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

// Get report data
$report_data = [
    'total_trees_planted' => 0,
    'total_volunteers' => 0,
    'total_donations' => 0,
    'total_orders' => 0,
    'top_counties' => [],
    'monthly_growth' => []
];

// Total trees planted
$stmt = $connection->prepare("SELECT SUM(planted_trees) as total FROM initiatives");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$report_data['total_trees_planted'] = $row['total'] ?? 0;

// Total volunteers
$stmt = $connection->prepare("SELECT COUNT(DISTINCT user_id) as total FROM volunteer_signups WHERE status = 'confirmed'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$report_data['total_volunteers'] = $row['total'] ?? 0;

// Total donations
$stmt = $connection->prepare("SELECT COUNT(*) as total, SUM(amount) as amount FROM donations WHERE payment_status = 'completed'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$report_data['total_donations'] = [
    'count' => $row['total'] ?? 0,
    'amount' => $row['amount'] ?? 0
];

// Total orders
$stmt = $connection->prepare("SELECT COUNT(*) as total FROM orders");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$report_data['total_orders'] = $row['total'] ?? 0;

// Top counties by tree planting
$stmt = $connection->prepare("
    SELECT u.county, COUNT(tp.id) as tree_count 
    FROM tree_plantings tp 
    JOIN users u ON tp.user_id = u.id 
    GROUP BY u.county 
    ORDER BY tree_count DESC 
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
$report_data['top_counties'] = $result->fetch_all(MYSQLI_ASSOC);

// Monthly growth
$stmt = $connection->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
    FROM user_contributions 
    WHERE type = 'tree_planted' 
    GROUP BY month 
    ORDER BY month
");
$stmt->execute();
$result = $stmt->get_result();
$report_data['monthly_growth'] = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - GreenEarth Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'components/header.php'; ?>
    
    <div class="flex">
        <?php include 'components/sidebar.php'; ?>
        
        <main class="flex-1 pb-8">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                    
                    <!-- Key Metrics -->
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Trees Planted</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($report_data['total_trees_planted']); ?></div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Volunteers</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($report_data['total_volunteers']); ?></div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Donations</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($report_data['total_donations']['count']); ?></div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Orders</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($report_data['total_orders']); ?></div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts -->
                    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900">Monthly Growth</h3>
                            <div class="mt-4">
                                <canvas id="growthChart" height="300"></canvas>
                            </div>
                        </div>
                        
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900">Top Counties by Tree Planting</h3>
                            <div class="mt-4">
                                <canvas id="countiesChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Options -->
                    <div class="mt-8 bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900">Export Reports</h3>
                        <div class="mt-4 flex space-x-4">
                            <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Export to Excel
                            </a>
                            <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Monthly Growth Chart
        var growthCtx = document.getElementById('growthChart').getContext('2d');
        var growthChart = new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    $months = [];
                    foreach($report_data['monthly_growth'] as $item) {
                        $months[] = "'{$item['month']}'";
                    }
                    echo implode(',', $months);
                ?>],
                datasets: [{
                    label: 'Trees Planted',
                    data: [<?php 
                        $counts = [];
                        foreach($report_data['monthly_growth'] as $item) {
                            $counts[] = $item['count'];
                        }
                        echo implode(',', $counts);
                    ?>],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
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
        
        // Top Counties Chart
        var countiesCtx = document.getElementById('countiesChart').getContext('2d');
        var countiesChart = new Chart(countiesCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    $counties = [];
                    foreach($report_data['top_counties'] as $item) {
                        $counties[] = "'{$item['county']}'";
                    }
                    echo implode(',', $counties);
                ?>],
                datasets: [{
                    label: 'Trees Planted',
                    data: [<?php 
                        $tree_counts = [];
                        foreach($report_data['top_counties'] as $item) {
                            $tree_counts[] = $item['tree_count'];
                        }
                        echo implode(',', $tree_counts);
                    ?>],
                    backgroundColor: '#10b981'
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
    </script>
</body>
</html>