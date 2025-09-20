<!-- pages/admin/dashboard.php -->
<?php
// Session already started in index.php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=admin/login");
    exit;
}

// Get dashboard statistics using global connection
global $connection;

$stats = [
    'zones' => 0,
    'partners' => 0,
    'initiatives' => 0,
    'events' => 0,
    'products' => 0,
    'orders' => 0,
    'users' => 0,
    'blog_posts' => 0
];

foreach($stats as $key => $value) {
    $table = $key;
    if($key === 'zones') $table = 'climatic_zones';
    if($key === 'blog_posts') $table = 'blog_posts';
    
    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM " . $table);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats[$key] = $row['count'];
    }
}

// Get recent activities
$stmt = $connection->prepare("
    SELECT 'initiative' as type, title, created_at FROM initiatives 
    UNION ALL 
    SELECT 'event' as type, title, created_at FROM events 
    UNION ALL 
    SELECT 'product' as type, name as title, created_at FROM products 
    UNION ALL 
    SELECT 'blog' as type, title, created_at FROM blog_posts 
    ORDER BY created_at DESC 
    LIMIT 10
");
$recent_activities = [];
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_activities = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GreenEarth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }
    .sidebar {
        width: 256px;
        flex-shrink: 0;
    }
    .main-content {
        flex: 1;
        margin-left: 0;
    }
    @media (min-width: 768px) {
        .main-content {
            margin-left: 256px;
        }
    }
</style>
</head>
<body class="bg-gray-100">
    <?php include 'pages/admin/components/header.php'; ?>
    
    <div class="admin-layout">
        <?php include 'pages/admin/components/sidebar.php'; ?>
        
        <main class="main-content pb-8">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    
                    <!-- Stats -->
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Climatic Zones</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo $stats['zones']; ?></div>
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
                                            <dt class="text-sm font-medium text-gray-500 truncate">Partners</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo $stats['partners']; ?></div>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Initiatives</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo $stats['initiatives']; ?></div>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Events</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900"><?php echo $stats['events']; ?></div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="mt-8">
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Recent Activity
                                </h3>
                            </div>
                            <ul class="divide-y divide-gray-200">
                                <?php foreach($recent_activities as $activity): ?>
                                <li>
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-green-600 truncate">
                                                <?php echo ucfirst($activity['type']); ?>: <?php echo htmlspecialchars($activity['title']); ?>
                                            </p>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <?php echo date('M j, Y', strtotime($activity['created_at'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>