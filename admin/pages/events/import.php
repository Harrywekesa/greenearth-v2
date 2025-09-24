<!-- pages/events/import.php -->
<?php
global $connection;
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=login");
    exit;
}

if($event_id > 0) {
    $stmt = $connection->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    
    if(!$event) {
        header("Location: ?page=events/list");
        exit;
    }
} else {
    header("Location: ?page=events/list");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        if($handle) {
            $data = [];
            $header = fgetcsv($handle); // Skip header row
            
            while(($row = fgetcsv($handle)) !== FALSE) {
                $data[] = $row;
            }
            
            fclose($handle);
            
            // Convert to JSON and save to database
            $json_data = json_encode($data);
            
            $stmt = $connection->prepare("UPDATE events SET historical_data = ? WHERE id = ?");
            $stmt->bind_param("si", $json_data, $event_id);
            
            if($stmt->execute()) {
                $message = "Event data imported successfully!";
                // Refresh event data
                $stmt = $connection->prepare("SELECT * FROM events WHERE id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $event = $result->fetch_assoc();
            } else {
                $error = "Error importing event data: " . $stmt->error;
            }
        } else {
            $error = "Error reading CSV file";
        }
    } else {
        $error = "Error uploading file";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Event Data - GreenEarth Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 256px;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 20;
            background-color: #fff;
            border-right-width: 1px;
            border-color: #e5e7eb;
        }
        .main-content {
            flex: 1;
            margin-left: 0;
            padding-top: 64px;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
        }
        @media (max-width: 767px) {
            .sidebar {
                display: none;
            }
        }
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'components/header.php'; ?>
    
    <div class="admin-layout">
        <?php include 'components/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Import Data for <?php echo htmlspecialchars($event['title']); ?></h1>
                        <a href="?page=events/list" class="text-sm font-medium text-green-600 hover:text-green-500">
                            ← Back to events
                        </a>
                    </div>
                    
                    <?php if($message): ?>
                    <div class="mt-4 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    <?php echo $message; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                    <div class="mt-4 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    <?php echo $error; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-medium text-gray-900">Upload Event Data</h2>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    Upload a CSV file with historical data for <?php echo htmlspecialchars($event['title']); ?> event.
                                </p>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" class="mt-6">
                                <div>
                                    <label for="csv_file" class="block text-sm font-medium text-gray-700">Select CSV File</label>
                                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                </div>
                                
                                <div class="mt-6">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Import Data
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="mt-8 bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-medium text-gray-900">CSV Format Instructions</h2>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    The CSV file should contain the following columns:
                                </p>
                                <ul class="mt-2 list-disc list-inside text-sm text-gray-500">
                                    <li>Date (YYYY-MM-DD)</li>
                                    <li>Volunteers Participated</li>
                                    <li>Trees Planted</li>
                                    <li>Funds Raised (KES)</li>
                                    <li>Notes</li>
                                </ul>
                            </div>
                            
                            <div class="mt-6">
                                <h3 class="text-md font-medium text-gray-900">Sample CSV Format</h3>
                                <div class="mt-2 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volunteers</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trees Planted</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Funds Raised (KES)</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1200</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">120000</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Successful planting day</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-02-20</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">38</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1000</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">100000</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Good weather conditions</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <?php if($event['historical_data']): ?>
                        <div class="mt-8 bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-medium text-gray-900">Current Data</h2>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    This event currently has <?php echo count(json_decode($event['historical_data'], true)); ?> records of historical data.
                                </p>
                            </div>
                            
                            <div class="mt-6">
                                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 0h6v-1a6 6 0 015.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Download Data
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>