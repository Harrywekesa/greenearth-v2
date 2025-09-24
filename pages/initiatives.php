<!-- pages/initiatives.php -->
<?php
global $connection;

// Get all active initiatives with partner info
$stmt = $connection->prepare("
    SELECT i.*, p.name as partner_name, p.logo as partner_logo 
    FROM initiatives i 
    LEFT JOIN partners p ON i.partner_id = p.id 
    WHERE i.status IN ('upcoming', 'ongoing')
    ORDER BY i.start_date DESC
");
$stmt->execute();
$result = $stmt->get_result();
$initiatives = $result->fetch_all(MYSQLI_ASSOC);

// Get completed initiatives for statistics
$stmt = $connection->prepare("SELECT COUNT(*) as count, SUM(target_trees) as total_target, SUM(planted_trees) as total_planted FROM initiatives WHERE status = 'completed'");
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Our Initiatives</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Join our environmental conservation efforts across Kenya
            </p>
        </div>
        
        <!-- Stats Section -->
        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-4xl font-bold text-green-600"><?php echo number_format($stats['count'] ?? 0); ?></div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Completed Initiatives</div>
                </div>
                <div class="bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-4xl font-bold text-green-600"><?php echo number_format($stats['total_target'] ?? 0); ?></div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Target Trees</div>
                </div>
                <div class="bg-green-50 rounded-lg p-6 text-center">
                    <div class="text-4xl font-bold text-green-600"><?php echo number_format($stats['total_planted'] ?? 0); ?></div>
                    <div class="mt-2 text-lg font-medium text-gray-900">Trees Planted</div>
                </div>
            </div>
        </div>
        
        <!-- Initiatives Grid -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900">Active Initiatives</h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($initiatives as $initiative): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if($initiative['image']): ?>
                        <img src="admin/uploads/initiatives/<?php echo htmlspecialchars($initiative['image']); ?>" 
     alt="<?php echo htmlspecialchars($initiative['title']); ?>" 
     class="w-full h-48 object-cover">

                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500">Initiative Image</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <div class="flex items-center">
                            <?php if($initiative['partner_logo']): ?>
                                <img src="admin/uploads/partners/<?php echo htmlspecialchars($initiative['partner_logo']); ?>" 
     alt="<?php echo htmlspecialchars($initiative['partner_name']); ?>" 
     class="w-8 h-8 object-cover rounded-full">

                            <?php else: ?>
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-8 h-8 flex items-center justify-center">
                                    <span class="text-gray-500 text-xs">P</span>
                                </div>
                            <?php endif; ?>
                            <div class="ml-2">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($initiative['partner_name']); ?></div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?php 
                                switch($initiative['status']) {
                                    case 'upcoming': echo 'Upcoming'; break;
                                    case 'ongoing': echo 'Ongoing'; break;
                                }
                                ?>
                            </span>
                            <h3 class="mt-2 text-xl font-bold text-gray-900"><?php echo htmlspecialchars($initiative['title']); ?></h3>
                            <p class="mt-2 text-gray-600"><?php echo substr(htmlspecialchars($initiative['description']), 0, 100); ?>...</p>
                            
                            <div class="mt-4">
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span><?php echo date('M j, Y', strtotime($initiative['start_date'])); ?></span>
                                    <span><?php echo htmlspecialchars($initiative['location']); ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: <?php echo calculate_progress($initiative['planted_trees'], $initiative['target_trees']); ?>%"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 mt-1">
                                    <span><?php echo number_format($initiative['planted_trees']); ?> planted</span>
                                    <span><?php echo number_format($initiative['target_trees']); ?> target</span>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <a href="?page=initiative_detail&id=<?php echo $initiative['id']; ?>" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="mt-16 bg-green-50 rounded-lg p-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-900">Want to Get Involved?</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Join our community of environmental champions and make a difference in Kenya's ecosystem.
                </p>
                <div class="mt-6 flex justify-center space-x-4">
                    <a href="?page=contact" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        Contact Us
                    </a>
                    <a href="#" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                        Volunteer Today
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>