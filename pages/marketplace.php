<!-- pages/marketplace.php -->
<?php
global $connection;

// Get categories
$categories = ['seedlings', 'manure', 'pesticides', 'tools'];

// Get selected category
$category = isset($_GET['category']) ? sanitize_input($_GET['category']) : 'all';

// Get search term
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build query based on filters
$where_clause = "WHERE p.is_active = 1";
$params = [];
$types = "";

if($category !== 'all') {
    $where_clause .= " AND p.category = ?";
    $params[] = $category;
    $types .= "s";
}

if($search) {
    $where_clause .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $types .= "ss";
}

// Prepare statement
$sql = "SELECT p.*, 
               CASE 
                   WHEN p.stock_quantity > 10 THEN 'In Stock' 
                   WHEN p.stock_quantity > 0 THEN 'Low Stock' 
                   ELSE 'Out of Stock' 
               END as stock_status,
               CASE 
                   WHEN p.stock_quantity > 10 THEN 'bg-green-100 text-green-800' 
                   WHEN p.stock_quantity > 0 THEN 'bg-yellow-100 text-yellow-800' 
                   ELSE 'bg-red-100 text-red-800' 
               END as stock_class
        FROM products p 
        {$where_clause} 
        ORDER BY p.name";

$stmt = $connection->prepare($sql);
if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get cart item count
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

// Get messages from session
$message = '';
$error = '';
if(isset($_SESSION['cart_message'])) {
    $message = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']);
}
if(isset($_SESSION['cart_error'])) {
    $error = $_SESSION['cart_error'];
    unset($_SESSION['cart_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    $stmt = $connection->prepare("SELECT id, name, price, stock_quantity FROM products WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        if ($product['stock_quantity'] >= $quantity) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
            $_SESSION['cart_message'] = "{$product['name']} added to cart!";
        } else {
            $_SESSION['cart_error'] = "Not enough stock for {$product['name']}.";
        }
    } else {
        $_SESSION['cart_error'] = "Product not found.";
    }

    // Redirect back to clear POST (avoids resubmission warning)
    header("Location: ?page=marketplace");
    exit;
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Eco Marketplace</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Purchase quality seedlings, organic manure, and eco-friendly products to support your planting efforts.
            </p>
        </div>
        
        <!-- Messages -->
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
        
        <!-- Search and Filters -->
        <div class="mt-10">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <input type="hidden" name="page" value="marketplace">
                <div class="flex-grow">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products..." class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 pr-12 py-3 sm:text-sm border-gray-300 rounded-md">
                        <?php if($search): ?>
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <a href="?page=marketplace&category=<?php echo $category; ?>" class="pr-3 text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Search
                </button>
            </form>
            
            <!-- Category Filter -->
            <div class="mt-6">
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="?page=marketplace&category=all<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($category === 'all') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        All Products
                    </a>
                    <?php foreach($categories as $cat): ?>
                    <a href="?page=marketplace&category=<?php echo $cat; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($category === $cat) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        <?php echo ucfirst($cat); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="mt-12">
            <?php if(empty($products)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m16-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No products found</h3>
                <p class="mt-1 text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                <div class="mt-6">
                    <a href="?page=marketplace" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        View All Products
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if($product['image']): ?>
                        <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500"><?php echo ucfirst($product['category']); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="mt-1 text-sm text-gray-500 capitalize"><?php echo htmlspecialchars($product['category']); ?></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $product['stock_class']; ?>">
                                <?php echo $product['stock_status']; ?>
                            </span>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-600 text-sm"><?php echo substr(htmlspecialchars($product['description']), 0, 80); ?>...</p>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold text-gray-900"><?php echo format_currency($product['price']); ?></span>
                            <?php if($product['stock_quantity'] > 0): ?>
                            <form method="POST" action="?page=add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                                    Add to Cart
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded text-red-700 bg-red-100">
                                Out of Stock
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if($product['planting_tips']): ?>
                        <div class="mt-3">
                            <details class="text-sm text-gray-500">
                                <summary class="cursor-pointer">Planting Tips</summary>
                                <p class="mt-1"><?php echo htmlspecialchars($product['planting_tips']); ?></p>
                            </details>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Cart Summary -->
        <?php if($cart_count > 0): ?>
        <div class="mt-12 bg-gray-50 rounded-lg p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Your Cart</h2>
                    <p class="text-sm text-gray-500">
                        <?php echo $cart_count; ?> item<?php echo $cart_count !== 1 ? 's' : ''; ?> in cart
                    </p>
                </div>
                <a href="?page=cart" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    View Cart
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>