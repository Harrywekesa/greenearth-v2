<!-- pages/settings.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=login");
    exit;
}

// Get all settings
$stmt = $connection->prepare("SELECT * FROM settings ORDER BY `key`");
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_all(MYSQLI_ASSOC);

// Convert to associative array for easier access
$settings_array = [];
foreach($settings as $setting) {
    $settings_array[$setting['key']] = $setting['value'];
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle general settings
    $updates = [
        'site_name' => sanitize_input($_POST['site_name']),
        'site_description' => sanitize_input($_POST['site_description']),
        'contact_email' => sanitize_input($_POST['contact_email']),
        'contact_phone' => sanitize_input($_POST['contact_phone']),
        'social_facebook' => sanitize_input($_POST['social_facebook']),
        'social_twitter' => sanitize_input($_POST['social_twitter']),
        'social_instagram' => sanitize_input($_POST['social_instagram']),
        'trees_planted' => (int)$_POST['trees_planted'],
        'volunteers_count' => (int)$_POST['volunteers_count'],
        'partners_count' => (int)$_POST['partners_count']
    ];
    
    $success = true;
    foreach($updates as $key => $value) {
        $stmt = $connection->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        if(!$stmt->execute()) {
            $success = false;
            $error = "Error updating settings: " . $stmt->error;
            break;
        }
    }
    
    if($success) {
        $message = "Settings updated successfully!";
        // Refresh settings
        $stmt = $connection->prepare("SELECT * FROM settings ORDER BY `key`");
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = $result->fetch_all(MYSQLI_ASSOC);
        
        // Convert to associative array for easier access
        $settings_array = [];
        foreach($settings as $setting) {
            $settings_array[$setting['key']] = $setting['value'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - GreenEarth Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-900">Site Settings</h1>
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
                    
                    <div class="mt-8 bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">General Settings</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Configure basic site information and contact details.
                            </p>
                            
                            <form method="POST" class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-6">
                                        <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                                        <input type="text" name="site_name" id="site_name" value="<?php echo htmlspecialchars($settings_array['site_name'] ?? 'GreenEarth'); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-6">
                                        <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                                        <textarea id="site_description" name="site_description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($settings_array['site_description'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                                        <input type="email" name="contact_email" id="contact_email" value="<?php echo htmlspecialchars($settings_array['contact_email'] ?? 'info@greenearth.org'); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                                        <input type="text" name="contact_phone" id="contact_phone" value="<?php echo htmlspecialchars($settings_array['contact_phone'] ?? '+254 700 000 000'); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-6">
                                        <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                        <input type="url" name="social_facebook" id="social_facebook" value="<?php echo htmlspecialchars($settings_array['social_facebook'] ?? 'https://facebook.com/greenearth'); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                                        <input type="url" name="social_twitter" id="social_twitter" value="<?php echo htmlspecialchars($settings_array['social_twitter'] ?? 'https://twitter.com/greenearth'); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                        <input type="url" name="social_instagram" id="social_instagram" value="<?php echo htmlspecialchars($settings_array['social_instagram'] ?? 'https://instagram.com/greenearth'); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-6">
                                    <h2 class="text-lg font-medium text-gray-900">Statistics</h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Update site-wide statistics displayed on the homepage.
                                    </p>
                                    
                                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-2">
                                            <label for="trees_planted" class="block text-sm font-medium text-gray-700">Trees Planted</label>
                                            <input type="number" name="trees_planted" id="trees_planted" min="0" value="<?php echo (int)($settings_array['trees_planted'] ?? 0); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        </div>
                                        
                                        <div class="sm:col-span-2">
                                            <label for="volunteers_count" class="block text-sm font-medium text-gray-700">Volunteers</label>
                                            <input type="number" name="volunteers_count" id="volunteers_count" min="0" value="<?php echo (int)($settings_array['volunteers_count'] ?? 0); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        </div>
                                        
                                        <div class="sm:col-span-2">
                                            <label for="partners_count" class="block text-sm font-medium text-gray-700">Partners</label>
                                            <input type="number" name="partners_count" id="partners_count" min="0" value="<?php echo (int)($settings_array['partners_count'] ?? 0); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-6">
                                    <h2 class="text-lg font-medium text-gray-900">SEO Settings</h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Configure search engine optimization settings.
                                    </p>
                                    
                                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-6">
                                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                                            <textarea id="meta_description" name="meta_description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($settings_array['meta_description'] ?? ''); ?></textarea>
                                        </div>
                                        
                                        <div class="sm:col-span-6">
                                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                                            <input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo htmlspecialchars($settings_array['meta_keywords'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-6">
                                    <h2 class="text-lg font-medium text-gray-900">Advanced Settings</h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Configure advanced site features and integrations.
                                    </p>
                                    
                                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                        <div class="sm:col-span-3">
                                            <label for="google_analytics_id" class="block text-sm font-medium text-gray-700">Google Analytics ID</label>
                                            <input type="text" name="google_analytics_id" id="google_analytics_id" value="<?php echo htmlspecialchars($settings_array['google_analytics_id'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        </div>
                                        
                                        <div class="sm:col-span-3">
                                            <label for="google_maps_api_key" class="block text-sm font-medium text-gray-700">Google Maps API Key</label>
                                            <input type="text" name="google_maps_api_key" id="google_maps_api_key" value="<?php echo htmlspecialchars($settings_array['google_maps_api_key'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        </div>
                                        
                                        <div class="sm:col-span-6">
                                            <label for="custom_css" class="block text-sm font-medium text-gray-700">Custom CSS</label>
                                            <textarea id="custom_css" name="custom_css" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm font-mono"><?php echo htmlspecialchars($settings_array['custom_css'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Settings Backup/Restore -->
                    <div class="mt-8 bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Backup & Restore</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Export or import site settings.
                            </p>
                            
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <h3 class="text-md font-medium text-gray-900">Export Settings</h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Download a backup of all current settings.
                                    </p>
                                    <div class="mt-4">
                                        <a href="?page=settings/export" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            Export Settings
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <h3 class="text-md font-medium text-gray-900">Import Settings</h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Upload a settings backup file to restore.
                                    </p>
                                    <div class="mt-4">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="import_settings">
                                            <input type="file" name="settings_file" id="settings_file" accept=".json" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                            <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm9.707-9.293a1 1 0 010 1.414L9.414 11l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                Import Settings
                                            </button>
                                        </form>
                                    </div>
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