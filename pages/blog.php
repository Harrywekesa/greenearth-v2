<!-- pages/blog.php -->
<?php
// Get blog posts
$stmt = $connection->prepare("
    SELECT bp.*, u.name as author_name 
    FROM blog_posts bp 
    LEFT JOIN users u ON bp.author_id = u.id 
    WHERE bp.is_published = 1 
    ORDER BY bp.published_at DESC 
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">GreenEarth Blog</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Insights, tips, and stories about environmental conservation and tree planting in Kenya.
            </p>
        </div>

        <!-- Newsletter Signup -->
        <div class="mt-12 bg-green-50 rounded-lg p-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-900">Stay Updated</h2>
                <p class="mt-2 text-gray-600">
                    Subscribe to our newsletter for the latest environmental news and planting tips.
                </p>
                <form class="mt-6 sm:flex" method="POST" action="?page=subscribe">
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="email" required class="w-full px-5 py-3 placeholder-gray-500 focus:ring-green-500 focus:border-green-500 sm:max-w-xs border-gray-300 rounded-md" placeholder="Enter your email">
                    <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                        <button type="submit" class="w-full flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Subscribe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Blog Posts -->
        <div class="mt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($posts as $post): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if($post['featured_image']): ?>
                        <img src="admin/uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500">Blog Image</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500">
                                <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                            </span>
                            <span class="mx-2 text-gray-300">•</span>
                            <span class="text-sm text-gray-500">
                                <?php echo $post['author_name']; ?>
                            </span>
                        </div>
                        <h3 class="mt-2 text-xl font-bold text-gray-900"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="mt-3 text-gray-600"><?php echo htmlspecialchars($post['excerpt']); ?></p>

                        <div class="mt-4">
                            <a href="?page=blog_post&id=<?php echo $post['id']; ?>" class="text-green-600 hover:text-green-800 font-medium">
                                Read more →
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>