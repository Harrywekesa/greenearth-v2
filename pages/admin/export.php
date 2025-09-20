<!-- pages/admin/export.php -->
<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

if(isset($_GET['type'])) {
    $type = $_GET['type'];
    
    switch($type) {
        case 'volunteers':
            // Export volunteers data
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="volunteers.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Name', 'Email', 'Phone', 'County', 'Events Attended', 'Trees Planted']);
            
            $stmt = $connection->prepare("
                SELECT u.name, u.email, u.phone, u.county, 
                       COUNT(DISTINCT vs.event_id) as events_attended,
                       COUNT(tp.id) as trees_planted
                FROM users u
                LEFT JOIN volunteer_signups vs ON u.id = vs.user_id AND vs.status = 'confirmed'
                LEFT JOIN tree_plantings tp ON u.id = tp.user_id
                WHERE u.role = 'user'
                GROUP BY u.id
                ORDER BY u.name
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
            
        case 'donations':
            // Export donations data
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="donations.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Name', 'Email', 'Amount', 'Date', 'Initiative', 'Payment Status']);
            
            $stmt = $connection->prepare("
                SELECT d.name, d.email, d.amount, d.created_at, i.title as initiative, d.payment_status
                FROM donations d
                LEFT JOIN initiatives i ON d.initiative_id = i.id
                ORDER BY d.created_at DESC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
            
        case 'tree_plantings':
            // Export tree plantings data
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="tree_plantings.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Tree Type', 'Latitude', 'Longitude', 'Planting Date', 'Status', 'User', 'Event']);
            
            $stmt = $connection->prepare("
                SELECT tp.tree_type, tp.latitude, tp.longitude, tp.planting_date, tp.status,
                       u.name as user_name, e.title as event_title
                FROM tree_plantings tp
                LEFT JOIN users u ON tp.user_id = u.id
                LEFT JOIN events e ON tp.event_id = e.id
                ORDER BY tp.planting_date DESC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Export - GreenEarth Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'components/header.php'; ?>
    
    <div class="flex">
        <?php include 'components/sidebar.php'; ?>
        
        <main class="flex-1 pb-8">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-900">Data Export</h1>
                    
                    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
                                            <dd class="mt-1 flex items-baseline justify-between">
                                                <div class="text-lg font-semibold text-gray-900">CSV Export</div>
                                                <a href="?type=volunteers" class="text-sm font-medium text-green-600 hover:text-green-500">
                                                    Download
                                                </a>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm text-gray-500">
                                    Export volunteer data including names, contact info, and participation stats
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
                                            <dd class="mt-1 flex items-baseline justify-between">
                                                <div class="text-lg font-semibold text-gray-900">CSV Export</div>
                                                <a href="?type=donations" class="text-sm font-medium text-green-600 hover:text-green-500">
                                                    Download
                                                </a>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm text-gray-500">
                                    Export donation records with donor info and payment details
                                </div>
                            </div>
                        </div>
                        
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
                                            <dt class="text-sm font-medium text-gray-500 truncate">Tree Plantings</dt>
                                            <dd class="mt-1 flex items-baseline justify-between">
                                                <div class="text-lg font-semibold text-gray-900">CSV Export</div>
                                                <a href="?type=tree_plantings" class="text-sm font-medium text-green-600 hover:text-green-500">
                                                    Download
                                                </a>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm text-gray-500">
                                    Export GPS coordinates and details of all planted trees
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>