<!-- pages/zones/edit.php -->
<?php
// Session already started in index.php, no need to start again
// Config already included in index.php, no need to include again

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=login");
    exit;
}

global $connection;
$zone_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

if($zone_id > 0) {
    $stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE id = ?");
    $stmt->bind_param("i", $zone_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $zone = $result->fetch_assoc();
    
    if(!$zone) {
        header("Location: ?page=zones/list");
        exit;
    }
} else {
    header("Location: ?page=zones/list");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $rainfall_pattern = sanitize_input($_POST['rainfall_pattern']);
    $soil_type = sanitize_input($_POST['soil_type']);
    $vegetation_type = sanitize_input($_POST['vegetation_type']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $slug = create_slug($name);
    
    // Handle image upload
    $image = $zone['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/zones/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Delete old image if exists
        if($zone['image'] && file_exists($upload_dir . $zone['image'])) {
            unlink($upload_dir . $zone['image']);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $file_extension;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
    }
    
    $stmt = $connection->prepare("UPDATE climatic_zones SET name = ?, slug = ?, description = ?, rainfall_pattern = ?, soil_type = ?, vegetation_type = ?, image = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $name, $slug, $description, $rainfall_pattern, $soil_type, $vegetation_type, $image, $is_active, $zone_id);
    
    if($stmt->execute()) {
        $message = "Climatic zone updated successfully!";
        // Refresh zone data
        $stmt = $connection->prepare("SELECT * FROM climatic_zones WHERE id = ?");
        $stmt->bind_param("i", $zone_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $zone = $result->fetch_assoc();
    } else {
        $error = "Error updating climatic zone: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Climatic Zone - GreenEarth Admin</title>
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
                <div class="max-w-3xl mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Edit Climatic Zone</h1>
                        <a href="?page=zones/list" class="text-sm font-medium text-green-600 hover:text-green-500">
                            ← Back to zones
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
                    
                    <div class="mt-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Zone Name</label>
                                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($zone['name']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($zone['description']); ?></textarea>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="rainfall_pattern" class="block text-sm font-medium text-gray-700">Rainfall Pattern</label>
                                    <select id="rainfall_pattern" name="rainfall_pattern" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="Bimodal" <?php echo $zone['rainfall_pattern'] === 'Bimodal' ? 'selected' : ''; ?>>Bimodal</option>
                                        <option value="Unimodal" <?php echo $zone['rainfall_pattern'] === 'Unimodal' ? 'selected' : ''; ?>>Unimodal</option>
                                        <option value="Arid" <?php echo $zone['rainfall_pattern'] === 'Arid' ? 'selected' : ''; ?>>Arid</option>
                                        <option value="Semi-arid" <?php echo $zone['rainfall_pattern'] === 'Semi-arid' ? 'selected' : ''; ?>>Semi-arid</option>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="soil_type" class="block text-sm font-medium text-gray-700">Soil Type</label>
                                    <input type="text" name="soil_type" id="soil_type" value="<?php echo htmlspecialchars($zone['soil_type']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="vegetation_type" class="block text-sm font-medium text-gray-700">Vegetation Type</label>
                                    <input type="text" name="vegetation_type" id="vegetation_type" value="<?php echo htmlspecialchars($zone['vegetation_type']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="image" class="block text-sm font-medium text-gray-700">Zone Image</label>
                                    <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                    <?php if($zone['image'] && file_exists('uploads/zones/' . $zone['image'])): ?>
                                    <div class="mt-2">
                                        <img src="uploads/zones/<?php echo $zone['image']; ?>" alt="Current image" class="h-20 w-20 object-cover rounded-md">
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_active" name="is_active" type="checkbox" <?php echo $zone['is_active'] ? 'checked' : ''; ?> class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                                            <p class="text-gray-500">Make this zone visible to users</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Update Zone
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>