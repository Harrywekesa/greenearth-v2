<!-- pages/home.php -->
<?php
// Fetch completed initiatives for marquee
$stmt = $connection->prepare("
    SELECT i.title, i.planted_trees, p.name as partner_name 
    FROM initiatives i 
    LEFT JOIN partners p ON i.partner_id = p.id 
    WHERE i.status = 'completed' 
    ORDER BY i.end_date DESC 
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
$completed_initiatives = $result->fetch_all(MYSQLI_ASSOC);

// Fetch recent blog posts
$stmt = $connection->prepare("
    SELECT id, title, excerpt 
    FROM blog_posts 
    WHERE is_published = 1 
    ORDER BY published_at DESC 
    LIMIT 3
");
$stmt->execute();
$result = $stmt->get_result();
$recent_posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="hero-gradient text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight">
                <span class="block">Protect Our Planet</span>
                <span class="block mt-2 text-green-200">One Tree at a Time</span>
            </h1>
            <p class="mt-6 max-w-lg mx-auto text-xl text-green-100">
                Join our mission to plant trees, protect ecosystems, and build a sustainable future for Kenya.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <a href="?page=initiatives" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-600 bg-white hover:bg-green-50 md:py-4 md:text-lg md:px-10">
                    Join Initiative
                </a>
                <a href="#" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-700 hover:bg-green-800 md:py-4 md:text-lg md:px-10">
                    Donate Now
                </a>
                <a href="?page=marketplace" class="px-8 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-green-600 md:py-4 md:text-lg md:px-10">
                    Buy Seedlings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Success Marquee -->
<?php if (!empty($completed_initiatives)): ?>
<div class="bg-green-600 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <span class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white text-green-600 mr-4">
                🎉 Success Stories
            </span>
            <div class="overflow-hidden">
                <div class="animate-marquee whitespace-nowrap">
                    <?php foreach($completed_initiatives as $initiative): ?>
                        <span class="mx-4 text-white">
                            <?php echo htmlspecialchars($initiative['title']); ?>: 
                            <?php echo number_format($initiative['planted_trees']); ?> trees planted with 
                            <?php echo htmlspecialchars($initiative['partner_name']); ?> •
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Dynamic Counters -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                <div class="text-5xl font-bold text-green-600"><?php echo number_format((int)get_setting('trees_planted', '0')); ?></div>
                <div class="mt-2 text-lg font-medium text-gray-900">Trees Planted</div>
            </div>
            <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                <div class="text-5xl font-bold text-green-600"><?php echo number_format((int)get_setting('volunteers_count', '0')); ?></div>
                <div class="mt-2 text-lg font-medium text-gray-900">Volunteers</div>
            </div>
            <div class="counter-card bg-green-50 rounded-lg p-6 text-center">
                <div class="text-5xl font-bold text-green-600"><?php echo number_format((int)get_setting('partners_count', '0')); ?></div>
                <div class="mt-2 text-lg font-medium text-gray-900">Partners</div>
            </div>
        </div>
    </div>
</div>

<!-- Kenya Climatic Zones Preview -->
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Kenya's Climatic Zones</h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Explore Kenya's diverse ecosystems and discover which trees thrive in each region.
            </p>
        </div>

        <div class="mt-10">
            <div id="kenya-map" class="h-96 rounded-lg shadow-lg"></div>
        </div>
    </div>
</div>

<!-- Highlights Section -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Highlights</h2>
        </div>

        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Upcoming Events -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Upcoming Events</h3>
                    <ul class="space-y-4">
                        <?php 
                        $stmt = $connection->prepare("
                            SELECT e.title, e.event_date, e.location 
                            FROM events e 
                            WHERE e.status = 'upcoming' AND e.event_date >= CURDATE() 
                            ORDER BY e.event_date ASC 
                            LIMIT 3
                        ");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $events = $result->fetch_all(MYSQLI_ASSOC);
                        
                        foreach($events as $event): ?>
                        <li class="border-b pb-4">
                            <div class="font-medium"><?php echo htmlspecialchars($event['title']); ?></div>
                            <div class="text-sm text-gray-500">
                                <?php echo date('M j, Y', strtotime($event['event_date'])); ?> - <?php echo htmlspecialchars($event['location']); ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Featured Partners -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Featured Partners</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php 
                        $stmt = $connection->prepare("SELECT name, logo FROM partners WHERE is_active = 1 AND sponsorship_level IN ('gold', 'platinum') ORDER BY name LIMIT 4");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $partners = $result->fetch_all(MYSQLI_ASSOC);
                        
                        foreach($partners as $partner): ?>
                        <div class="text-center">
                            <?php if($partner['logo']): ?>
                                <img src="uploads/partners/<?php echo $partner['logo']; ?>" alt="<?php echo $partner['name']; ?>" class="mx-auto h-16 object-contain">
                            <?php else: ?>
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mx-auto flex items-center justify-center">
                                    <span class="text-gray-500 text-xs"><?php echo substr($partner['name'], 0, 10); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="mt-2 text-sm font-medium"><?php echo $partner['name']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Blog Posts -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Latest from Blog</h3>
                    <div class="space-y-4">
                        <?php foreach($recent_posts as $post): ?>
                        <div>
                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($post['title']); ?></div>
                            <div class="mt-1 text-sm text-gray-600"><?php echo htmlspecialchars(substr($post['excerpt'], 0, 80)); ?>...</div>
                            <a href="?page=blog_post&id=<?php echo $post['id']; ?>" class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">
                                Read more →
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Leaflet map
document.addEventListener('DOMContentLoaded', function() {
    // Create map
    var map = L.map('kenya-map').setView([-0.0236, 37.9062], 6);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add Kenya outline (simplified)
    var kenyaOutline = L.polygon([
        [-4.692, 33.908],
        [5.202, 33.908],
        [5.202, 41.906],
        [-4.692, 41.906]
    ], {
        color: '#22c55e',
        fillColor: '#bbf7d0',
        fillOpacity: 0.3
    }).addTo(map);
    
    // Add climatic zones (simplified)
    var zones = [
        {name: "Coastal", coords: [[-4, 39], [-3, 40], [-4, 41]], color: "#16a34a"},
        {name: "Lowland", coords: [[-1, 36], [0, 37], [-1, 38]], color: "#22c55e"},
        {name: "Highland", coords: [[0, 35], [1, 36], [0, 37]], color: "#bbf7d0"}
    ];
    
    zones.forEach(function(zone) {
        var polygon = L.polygon(zone.coords, {
            color: zone.color,
            fillColor: zone.color,
            fillOpacity: 0.5
        }).addTo(map);
        
        polygon.bindPopup("<b>" + zone.name + " Zone</b><br>Click for details");
    });
});

// Dark mode toggle
document.getElementById('darkModeToggle').addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    var icon = document.getElementById('darkModeIcon');
    if (document.body.classList.contains('dark-mode')) {
        icon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
    } else {
        icon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
    }
});

// Marquee animation
document.addEventListener('DOMContentLoaded', function() {
    const marquee = document.querySelector('.animate-marquee');
    if (marquee) {
        marquee.style.animation = 'marquee 30s linear infinite';
    }
});
</script>

<style>
@keyframes marquee {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
.animate-marquee {
    display: inline-block;
    padding-left: 100%;
}
</style>