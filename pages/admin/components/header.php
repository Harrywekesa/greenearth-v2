<!-- pages/admin/components/header.php -->
<nav class="bg-white shadow fixed w-full z-20" style="top: 0;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center md:ml-64">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-green-600">🌱 GreenEarth Admin</span>
                </div>
            </div>
            <div class="flex items-center">
                <div class="ml-3 relative">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-700">
                            Welcome, <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?>
                        </div>
                        <a href="?page=admin/logout" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Sign out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="pt-16"></div> <!-- Spacer for fixed header -->