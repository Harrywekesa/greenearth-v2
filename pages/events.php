<!-- pages/events.php -->
<?php
global $connection;

// Get upcoming events with initiative info
$stmt = $connection->prepare("
    SELECT e.*, i.title as initiative_title, p.name as partner_name 
    FROM events e 
    LEFT JOIN initiatives i ON e.initiative_id = i.id 
    LEFT JOIN partners p ON i.partner_id = p.id 
    WHERE e.status = 'upcoming' AND e.event_date >= CURDATE() AND e.is_active = 1
    ORDER BY e.event_date ASC
");
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);

// Get completed events for statistics
$stmt = $connection->prepare("SELECT COUNT(*) as count FROM events WHERE status = 'completed' AND is_active = 1");
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

// Define get_cart_count() if not already defined
if (!function_exists('get_cart_count')) {
    function get_cart_count() {
        // Return 0 or implement logic to count cart items
        return 0;
    }
}
$cart_count = get_cart_count();

// Define is_user_logged_in() if not already defined
if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in() {
        // Example logic: check if user session exists
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Upcoming Events</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Join our tree planting and environmental conservation activities across Kenya.
            </p>
        </div>
        
        <!-- Stats -->
        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-5xl font-bold text-green-600"><?php echo number_format($stats['count'] ?? 0); ?></div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Completed Events</div>
                </div>
                <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-5xl font-bold text-green-600">850</div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Volunteers</div>
                </div>
                <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-5xl font-bold text-green-600">12,500</div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Trees Planted</div>
                </div>
            </div>
        </div>
        
        <!-- Events Grid -->
        <div class="mt-16">
            <?php if(empty($events)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No upcoming events</h3>
                <p class="mt-1 text-gray-500">Check back later for new events.</p>
                <div class="mt-6">
                    <a href="?page=events" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        View All Events
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($events as $event): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if($event['image'] && file_exists('uploads/events/' . $event['image'])): ?>
                        <img src="uploads/events/<?php echo $event['image']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500"><?php echo ucfirst($event['category']); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($event['initiative_title']); ?></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?php echo date('M j', strtotime($event['event_date'])); ?>
                            </span>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-gray-600"><?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...</p>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-gray-500">
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                                <span><?php echo htmlspecialchars($event['partner_name']); ?></span>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: <?php echo calculate_progress($event['current_volunteers'], $event['max_volunteers']); ?>%"></div>
                            </div>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span><?php echo $event['current_volunteers']; ?> volunteers</span>
                                <span><?php echo $event['max_volunteers']; ?> max</span>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="?page=event_detail&id=<?php echo $event['id']; ?>" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                View Event Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="mt-16 bg-green-50 rounded-lg p-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-900">Want to Volunteer?</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Join our community of environmental champions and make a difference in Kenya's ecosystem.
                </p>
                <div class="mt-6">
                    <?php if(is_user_logged_in()): ?>
                    <a href="?page=profile" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        View Your Profile
                    </a>
                    <?php else: ?>
                    <a href="?page=login" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Login to Volunteer
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>