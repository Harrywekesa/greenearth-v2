<?php
// Session already started in index.php, no need to start again
// Config already included in index.php, no need to include again

// Check if admin is logged in (this is handled in index.php but let's be safe)
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=admin/login");
    exit;
}

global $connection;
$message = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $county = sanitize_input($_POST['county']);
    $subcounty = sanitize_input($_POST['subcounty']);
    $role = sanitize_input($_POST['role']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = $_POST['password'];
    
    // Check if email already exists
    $stmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $error = "Email already exists";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $connection->prepare("INSERT INTO users (name, email, phone, password, county, subcounty, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $name, $email, $phone, $hashed_password, $county, $subcounty, $role, $is_active);
        
        if($stmt->execute()) {
            $message = "User created successfully!";
            header("Location: ?page=admin/users/list&message=" . urlencode($message));
            exit;
        } else {
            $error = "Error creating user: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - GreenEarth Admin</title>
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
    <?php include '../components/header.php'; ?>
    
    <div class="admin-layout">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-3xl mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Add User</h1>
                        <a href="?page=admin/users/list" class="text-sm font-medium text-green-600 hover:text-green-500">
                            ← Back to users
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
                        <form method="POST">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="name" id="name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="phone" id="phone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input type="password" name="password" id="password" required minlength="8" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" required minlength="8" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="county" class="block text-sm font-medium text-gray-700">County</label>
                                    <input type="text" name="county" id="county" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="subcounty" class="block text-sm font-medium text-gray-700">Subcounty</label>
                                    <input type="text" name="subcounty" id="subcounty" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                    <select id="role" name="role" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="user">User</option>
                                        <option value="partner">Partner</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_active" name="is_active" type="checkbox" checked class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                                            <p class="text-gray-500">Allow this user to access their account</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Create User
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