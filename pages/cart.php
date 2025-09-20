<!-- pages/cart.php -->
<?php
global $connection;

// Get cart items with updated info
$cart_items = [];
$cart_total = 0;
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $product_id => $item) {
        $stmt = $connection->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if($product) {
            $cart_items[$product_id] = array_merge($item, $product);
            $cart_total += $product['price'] * $item['quantity'];
        } else {
            // Remove inactive product from cart
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Get cart messages if any
$cart_message = '';
$cart_error = '';
if(isset($_GET['message'])) {
    $cart_message = sanitize_input($_GET['message']);
}
if(isset($_GET['error'])) {
    $cart_error = sanitize_input($_GET['error']);
}

// Process cart actions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'update_quantity':
                $product_id = (int)$_POST['product_id'];
                $quantity = (int)$_POST['quantity'];
                
                if($quantity <= 0) {
                    // Remove item
                    unset($_SESSION['cart'][$product_id]);
                    $cart_message = "Item removed from cart!";
                } else {
                    // Update quantity - check stock
                    if(isset($_SESSION['cart'][$product_id])) {
                        $stmt = $connection->prepare("SELECT stock_quantity FROM products WHERE id = ? AND is_active = 1");
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $product = $result->fetch_assoc();
                        
                        if($product && $quantity <= $product['stock_quantity']) {
                            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                            $cart_message = "Cart updated successfully!";
                        } else {
                            $cart_error = "Not enough stock available.";
                        }
                    }
                }
                break;
                
            case 'remove_item':
                $product_id = (int)$_POST['product_id'];
                unset($_SESSION['cart'][$product_id]);
                $cart_message = "Item removed from cart!";
                break;
                
            case 'clear_cart':
                $_SESSION['cart'] = [];
                $cart_message = "Cart cleared!";
                break;
        }
        
        // Redirect to refresh cart
        $redirect_url = "?page=cart";
        if($cart_message) {
            $redirect_url .= "&message=" . urlencode($cart_message);
        }
        if($cart_error) {
            $redirect_url .= "&error=" . urlencode($cart_error);
        }
        header("Location: " . $redirect_url);
        exit;
    }
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Shopping Cart</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Review your items before checkout
            </p>
        </div>
        
        <!-- Messages -->
        <?php if($cart_message): ?>
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        <?php echo $cart_message; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if($cart_error): ?>
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        <?php echo $cart_error; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(empty($cart_items)): ?>
        <div class="mt-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13h10M5 21h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-1 text-gray-500">Start adding some eco-friendly products to your cart.</p>
            <div class="mt-6">
                <a href="?page=marketplace" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Browse Products
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Cart Items</h2>
                            
                            <div class="mt-4 space-y-6">
                                <?php foreach($cart_items as $item): ?>
                                <div class="flex items-center border-b border-gray-200 pb-6">
                                    <?php if($item['image']): ?>
                                        <img src="uploads/products/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="h-24 w-24 object-cover rounded-md">
                                    <?php else: ?>
                                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-24 h-24 flex items-center justify-center">
                                            <span class="text-gray-500 text-xs"><?php echo ucfirst(substr($item['category'], 0, 10)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                                                <p class="mt-1 text-sm text-gray-500 capitalize"><?php echo htmlspecialchars($item['category']); ?></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-medium text-gray-900"><?php echo format_currency($item['price'] * $item['quantity']); ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 flex items-center">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="update_quantity">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                
                                                <button type="button" onclick="changeQuantity(<?php echo $item['id']; ?>, -1)" class="text-gray-500 hover:text-gray-700">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                
                                                <input type="number" name="quantity" id="quantity-<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" class="mx-2 w-16 border border-gray-300 rounded-md shadow-sm py-1 px-2 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                
                                                <button type="button" onclick="changeQuantity(<?php echo $item['id']; ?>, 1)" class="text-gray-500 hover:text-gray-700">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V6a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                
                                                <button type="submit" class="ml-2 text-sm font-medium text-green-600 hover:text-green-500">
                                                    Update
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="ml-4">
                                                <input type="hidden" name="action" value="remove_item">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-6 flex justify-between">
                                <form method="POST">
                                    <input type="hidden" name="action" value="clear_cart">
                                    <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                        Clear Cart
                                    </button>
                                </form>
                                <a href="?page=marketplace" class="text-sm font-medium text-green-600 hover:text-green-500">
                                    Continue Shopping →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                        
                        <div class="mt-4 space-y-4">
                            <?php foreach($cart_items as $item): ?>
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $item['quantity']; ?> × <?php echo format_currency($item['price']); ?></p>
                                </div>
                                <p class="text-sm font-medium text-gray-900"><?php echo format_currency($item['price'] * $item['quantity']); ?></p>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium text-gray-900"><?php echo format_currency($cart_total); ?></span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-medium text-gray-900">Free</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="font-medium text-gray-900"><?php echo format_currency(0); ?></span>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4 flex justify-between">
                                    <span class="text-base font-medium text-gray-900">Total</span>
                                    <span class="text-base font-medium text-gray-900"><?php echo format_currency($cart_total); ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <?php if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                                <a href="?page=checkout" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Proceed to Checkout
                                </a>
                                <?php else: ?>
                                <a href="?page=login&redirect=<?php echo urlencode('?page=checkout'); ?>" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Login to Checkout
                                </a>
                                <p class="mt-2 text-center text-sm text-gray-500">
                                    Don't have an account? <a href="?page=register" class="font-medium text-green-600 hover:text-green-500">Register</a>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function changeQuantity(productId, change) {
    const input = document.getElementById('quantity-' + productId);
    let currentValue = parseInt(input.value) || 1;
    let newValue = currentValue + change;
    
    // Ensure value is within valid range
    if(newValue < 1) newValue = 1;
    // In a real implementation, you'd also check against max stock
    
    input.value = newValue;
}
</script>