<!-- pages/products.php -->
<?php
global $connection;

// Get all active products
$stmt = $connection->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Eco Marketplace</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Purchase quality seedlings, manure, and eco-friendly products to support your planting efforts.
            </p>
        </div>
        
        <!-- Category Filter -->
        <div class="mt-10">
            <div class="flex flex-wrap justify-center gap-2">
                <a href="?page=products&category=all" class="px-4 py-2 rounded-full text-sm font-medium <?php echo (!isset($_GET['category']) || $_GET['category'] === 'all') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                    All Products
                </a>
                <a href="?page=products&category=seedlings" class="px-4 py-2 rounded-full text-sm font-medium <?php echo (isset($_GET['category']) && $_GET['category'] === 'seedlings') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                    Seedlings
                </a>
                <a href="?page=products&category=manure" class="px-4 py-2 rounded-full text-sm font-medium <?php echo (isset($_GET['category']) && $_GET['category'] === 'manure') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                    Manure
                </a>
                <a href="?page=products&category=pesticides" class="px-4 py-2 rounded-full text-sm font-medium <?php echo (isset($_GET['category']) && $_GET['category'] === 'pesticides') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                    Eco-Friendly Pesticides
                </a>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="mt-12">
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php 
                                if($product['stock_quantity'] > 10) {
                                    echo 'bg-green-100 text-green-800';
                                } elseif($product['stock_quantity'] > 0) {
                                    echo 'bg-yellow-100 text-yellow-800';
                                } else {
                                    echo 'bg-red-100 text-red-800';
                                }
                                ?>">
                                <?php echo $product['stock_quantity']; ?> in stock
                            </span>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold text-gray-900"><?php echo format_currency($product['price']); ?></span>
                            <?php if($product['stock_quantity'] > 0): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                                Add to Cart
                            </button>
                            <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded text-red-700 bg-red-100">
                                Out of Stock
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div class="mt-12 bg-gray-50 rounded-lg p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Your Cart</h2>
                    <p class="text-sm text-gray-500">
                        <span id="cart-count">0</span> items in cart
                    </p>
                </div>
                <a href="?page=cart" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    View Cart
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize cart
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Update cart count
function updateCartCount() {
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    document.getElementById('cart-count').textContent = totalItems;
}

// Add to cart function
function addToCart(productId) {
    // In a real implementation, this would fetch product details from server
    // For now, we'll simulate adding to cart
    
    // Check if product already in cart
    const existingItem = cart.find(item => item.id === productId);
    
    if(existingItem) {
        existingItem.quantity += 1;
    } else {
        // Simulate fetching product data
        cart.push({
            id: productId,
            name: 'Product ' + productId,
            price: 100,
            quantity: 1
        });
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count
    updateCartCount();
    
    // Show notification
    alert('Product added to cart!');
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
</script>