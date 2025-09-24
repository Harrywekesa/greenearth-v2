<!-- pages/zones/list.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=login");
    exit;
}

// Get all climatic zones
$stmt = $connection->prepare("SELECT * FROM climatic_zones ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$zones = $result->fetch_all(MYSQLI_ASSOC);

// Handle actions
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'toggle_active':
            $zone_id = (int)$_POST['zone_id'];
            $is_active = (int)$_POST['is_active'];
            
            $stmt = $connection->prepare("UPDATE climatic_zones SET is_active = ? WHERE id = ?");
            $stmt->bind_param("ii", $is_active, $zone_id);
            
            if($stmt->execute()) {
                $message = "Zone " . ($is_active ? "activated" : "deactivated") . " successfully!";
            } else {
                $error = "Error updating zone: " . $stmt->error;
            }
            break;
            
        case 'delete':
            $zone_id = (int)$_POST['zone_id'];
            
            // Delete associated image
            $stmt = $connection->prepare("SELECT image FROM climatic_zones WHERE id = ?");
            $stmt->bind_param("i", $zone_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $zone = $result->fetch_assoc();
            
            if($zone && $zone['image']) {
                $image_path = 'uploads/zones/' . $zone['image'];
                if(file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            $stmt = $connection->prepare("DELETE FROM climatic_zones WHERE id = ?");
            $stmt->bind_param("i", $zone_id);
            
            if($stmt->execute()) {
                $message = "Zone deleted successfully!";
            } else {
                $error = "Error deleting zone: " . $stmt->error;
            }
            break;
    }
    
    // Refresh zone list
    $stmt = $connection->prepare("SELECT * FROM climatic_zones ORDER BY name");
    $stmt->execute();
    $result = $stmt->get_result();
    $zones = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Climatic Zones - GreenEarth Admin</title>
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
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Climatic Zones</h1>
                        <a href="?page=zones/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Zone
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
                    
                    <div class="mt-8 flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Zone
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Description
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Rainfall
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Soil Type
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Vegetation
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col" class="relative px-6 py-3">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php foreach($zones as $zone): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <?php if($zone['image'] && file_exists('uploads/zones/' . $zone['image'])): ?>
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <img class="h-10 w-10 rounded-md object-cover" src="uploads/zones/<?php echo $zone['image']; ?>" alt="<?php echo htmlspecialchars($zone['name']); ?>">
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 border-2 border-dashed rounded-xl flex items-center justify-center">
                                                                <span class="text-gray-500 text-xs">IMG</span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?php echo htmlspecialchars($zone['name']); ?>
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                <?php echo htmlspecialchars($zone['slug']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900"><?php echo substr(htmlspecialchars($zone['description']), 0, 100); ?>...</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($zone['rainfall_pattern']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($zone['soil_type']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($zone['vegetation_type']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if($zone['is_active']): ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="?page=zones/edit&id=<?php echo $zone['id']; ?>" class="text-green-600 hover:text-green-900">Edit</a>
                                                    <form method="POST" class="inline ml-4">
                                                        <input type="hidden" name="action" value="toggle_active">
                                                        <input type="hidden" name="zone_id" value="<?php echo $zone['id']; ?>">
                                                        <input type="hidden" name="is_active" value="<?php echo $zone['is_active'] ? 0 : 1; ?>">
                                                        <button type="submit" class="text-<?php echo $zone['is_active'] ? 'red' : 'green'; ?>-600 hover:text-<?php echo $zone['is_active'] ? 'red' : 'green'; ?>-900">
                                                            <?php echo $zone['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="inline ml-4">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="zone_id" value="<?php echo $zone['id']; ?>">
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this zone?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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