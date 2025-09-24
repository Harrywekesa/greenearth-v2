<!-- admin/components/header.php -->
<nav class="fixed-header bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-green-600">🌱 GreenEarth Admin</span>
                </div>
            </div>
            <div class="flex items-center">
                <div class="ml-3 relative">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-700">
                            Welcome, <?php echo htmlspecialchars(get_admin_name()); ?>
                        </div>
                        <a href="?page=logout" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Sign out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>