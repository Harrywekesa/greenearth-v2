<!-- pages/user/dashboard.php -->
<?php
// User profile and contribution tracking
?>
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Profile -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16"></div>
                        <div class="ml-4">
                            <h2 class="text-xl font-bold text-gray-900"><?php echo $_SESSION['user_name']; ?></h2>
                            <p class="text-gray-600"><?php echo $_SESSION['user_email']; ?></p>
                        </div>
                    </div>
                    
                    <!-- User Stats -->
                    <div class="mt-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trees Planted</span>
                            <span class="font-bold text-green-600">25</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Events Attended</span>
                            <span class="font-bold text-green-600">8</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Points</span>
                            <span class="font-bold text-green-600">450</span>
                        </div>
                    </div>
                    
                    <!-- Badges -->
                    <div class="mt-6">
                        <h3 class="text-lg font-bold text-gray-900">Achievements</h3>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <div class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                🌱 First Tree
                            </div>
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                🏆 Top Volunteer
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Activity</h2>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Planted 3 trees at Nairobi Reforestation Project</p>
                                <p class="text-sm text-gray-500">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Events -->
                <div class="mt-8 bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900">Upcoming Events</h2>
                    <div class="mt-4 space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-gray-900">Coastal Mangrove Restoration</h3>
                                <span class="text-sm text-gray-500">June 22, 2024</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Learn how to propagate and care for mangrove seedlings</p>
                            <button class="mt-3 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>