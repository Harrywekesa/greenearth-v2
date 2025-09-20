<!-- pages/blog_post.php -->
<?php
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($post_id > 0) {
    // Get blog post with author info
    $stmt = $connection->prepare("
        SELECT bp.*, u.name as author_name 
        FROM blog_posts bp 
        LEFT JOIN users u ON bp.author_id = u.id 
        WHERE bp.id = ? AND bp.is_published = 1
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    if(!$post) {
        header("Location: ?page=blog");
        exit;
    }
    
    // Update view count
    $stmt = $connection->prepare("UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    
    // Get related posts
    $stmt = $connection->prepare("
        SELECT id, title, excerpt, featured_image, published_at 
        FROM blog_posts 
        WHERE id != ? AND is_published = 1 
        ORDER BY published_at DESC 
        LIMIT 3
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $related_posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: ?page=blog");
    exit;
}
?>

<div class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="?page=home" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            <span class="sr-only">Home</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="?page=blog" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Blog</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500" aria-current="page"><?php echo $post['title']; ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <article class="mt-8">
            <h1 class="text-4xl font-extrabold text-gray-900"><?php echo $post['title']; ?></h1>
            
            <div class="mt-4 flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10"></div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900"><?php echo $post['author_name']; ?></p>
                    <div class="flex space-x-1 text-sm text-gray-500">
                        <time datetime="<?php echo $post['published_at']; ?>">
                            <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                        </time>
                        <span aria-hidden="true">·</span>
                        <span><?php echo $post['view_count']; ?> views</span>
                    </div>
                </div>
            </div>

            <?php if($post['featured_image']): ?>
                <img src="uploads/blog/<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>" class="mt-8 w-full rounded-lg">
            <?php endif; ?>

            <div class="mt-8 prose max-w-none text-gray-600">
                <?php echo $post['content']; ?>
            </div>

            <!-- Social Sharing -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Share this article</h3>
                <div class="mt-4 flex space-x-6">
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($post['title']); ?>&url=<?php echo urlencode(SITE_URL . '?page=blog_post&id=' . $post['id']); ?>" target="_blank" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Share on Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '?page=blog_post&id=' . $post['id']); ?>" target="_blank" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Share on Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '?page=blog_post&id=' . $post['id']); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Share on LinkedIn</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </article>

        <!-- Related Posts -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900">Related Articles</h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach($related_posts as $related): ?>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <?php if($related['featured_image']): ?>
                        <img src="uploads/blog/<?php echo $related['featured_image']; ?>" alt="<?php echo $related['title']; ?>" class="w-full h-32 object-cover">
                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed w-full h-32 flex items-center justify-center">
                            <span class="text-gray-500 text-sm">Related Article</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-900"><?php echo $related['title']; ?></h3>
                        <p class="mt-2 text-sm text-gray-600"><?php echo substr($related['excerpt'], 0, 100); ?>...</p>
                        <div class="mt-3">
                            <a href="?page=blog_post&id=<?php echo $related['id']; ?>" class="text-green-600 hover:text-green-800 text-sm font-medium">
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