<!-- pages/initiatives/edit.php -->
<?php
global $connection;
$initiative_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=login");
    exit;
}

if($initiative_id > 0) {
    $stmt = $connection->prepare("SELECT * FROM initiatives WHERE id = ?");
    $stmt->bind_param("i", $initiative_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $initiative = $result->fetch_assoc();
    
    if(!$initiative) {
        header("Location: ?page=initiatives/list");
        exit;
    }
} else {
    header("Location: ?page=initiatives/list");
    exit;
}

// Get partners for dropdown
$stmt = $connection->prepare("SELECT id, name FROM partners WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$partners = $result->fetch_all(MYSQLI_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $objectives = sanitize_input($_POST['objectives']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = sanitize_input($_POST['location']);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    $partner_id = (int)$_POST['partner_id'];
    $target_trees = (int)$_POST['target_trees'];
    $planted_trees = (int)$_POST['planted_trees'];
    $status = sanitize_input($_POST['status']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $slug = create_slug($title);
    
    // Handle image upload
    $image = $initiative['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/initiatives/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Delete old image if exists
        if($initiative['image'] && file_exists($upload_dir . $initiative['image'])) {
            unlink($upload_dir . $initiative['image']);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . strtolower($file_extension);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
    }
    
    $stmt = $connection->prepare("UPDATE initiatives SET title = ?, slug = ?, description = ?, objectives = ?, start_date = ?, end_date = ?, location = ?, latitude = ?, longitude = ?, partner_id = ?, target_trees = ?, planted_trees = ?, status = ?, image = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssssssssdiiddsii", $title, $slug, $description, $objectives, $start_date, $end_date, $location, $latitude, $longitude, $partner_id, $target_trees, $planted_trees, $status, $image, $is_active, $initiative_id);
    
    if($stmt->execute()) {
        $message = "Initiative updated successfully!";
        // Refresh initiative data
        $stmt = $connection->prepare("SELECT * FROM initiatives WHERE id = ?");
        $stmt->bind_param("i", $initiative_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $initiative = $result->fetch_assoc();
    } else {
        $error = "Error updating initiative: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Initiative - GreenEarth Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-900">Edit Initiative</h1>
                        <a href="?page=initiatives/list" class="text-sm font-medium text-green-600 hover:text-green-500">
                            ← Back to initiatives
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
                                    <label for="title" class="block text-sm font-medium text-gray-700">Initiative Title</label>
                                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($initiative['title']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($initiative['description']); ?></textarea>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="objectives" class="block text-sm font-medium text-gray-700">Objectives</label>
                                    <textarea id="objectives" name="objectives" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($initiative['objectives']); ?></textarea>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" value="<?php echo $initiative['start_date']; ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" name="end_date" id="end_date" value="<?php echo $initiative['end_date']; ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-4">
                                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                    <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($initiative['location']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="text" name="latitude" id="latitude" value="<?php echo $initiative['latitude']; ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="text" name="longitude" id="longitude" value="<?php echo $initiative['longitude']; ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="partner_id" class="block text-sm font-medium text-gray-700">Partner</label>
                                    <select id="partner_id" name="partner_id" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="">Select a partner</option>
                                        <?php foreach($partners as $partner): ?>
                                        <option value="<?php echo $partner['id']; ?>" <?php echo $partner['id'] == $initiative['partner_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($partner['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="status" name="status" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="upcoming" <?php echo $initiative['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                        <option value="ongoing" <?php echo $initiative['status'] === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                        <option value="completed" <?php echo $initiative['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="target_trees" class="block text-sm font-medium text-gray-700">Target Trees</label>
                                    <input type="number" name="target_trees" id="target_trees" min="1" value="<?php echo $initiative['target_trees']; ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="planted_trees" class="block text-sm font-medium text-gray-700">Planted Trees</label>
                                    <input type="number" name="planted_trees" id="planted_trees" min="0" value="<?php echo $initiative['planted_trees']; ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="image" class="block text-sm font-medium text-gray-700">Initiative Image</label>
                                    <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                    <?php if($initiative['image'] && file_exists('../uploads/initiatives/' . $initiative['image'])): ?>
                                    <div class="mt-2">
                                        <img src="../uploads/initiatives/<?php echo $initiative['image']; ?>" alt="Current image" class="h-20 w-20 object-cover rounded-md">
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_active" name="is_active" type="checkbox" <?php echo $initiative['is_active'] ? 'checked' : ''; ?> class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                                            <p class="text-gray-500">Make this initiative visible to users</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Update Initiative
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