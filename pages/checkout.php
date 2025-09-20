<!-- pages/checkout.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if user is logged in
if(!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    $_SESSION['redirect_after_login'] = '?page=checkout';
    header("Location: ?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if(empty($cart_items)) {
    header("Location: ?page=cart");
    exit;
}

// Get product details for cart items
$cart_products = [];
$cart_total = 0;
if(!empty($cart_items)) {
    foreach($cart_items as $product_id => $item) {
        $stmt = $connection->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if($product) {
            $cart_products[$product_id] = array_merge($item, $product);
            $cart_total += $product['price'] * $item['quantity'];
        } else {
            // Remove inactive product from cart
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Get user info for pre-filling
$stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process checkout
    $shipping_name = sanitize_input($_POST['shipping_name']);
    $shipping_phone = sanitize_input($_POST['shipping_phone']);
    $shipping_email = sanitize_input($_POST['shipping_email']);
    $shipping_county = sanitize_input($_POST['shipping_county']);
    $shipping_subcounty = sanitize_input($_POST['shipping_subcounty']);
    $shipping_address = sanitize_input($_POST['shipping_address']);
    $payment_method = sanitize_input($_POST['payment_method']);
    
    // Generate order number
    $order_number = 'ORD-' . strtoupper(uniqid());
    
    // Start transaction
    $connection->begin_transaction();
    
    try {
        // Insert order
        $stmt = $connection->prepare("INSERT INTO orders (user_id, order_number, total_amount, status, payment_status, payment_method, shipping_name, shipping_phone, shipping_email, shipping_county, shipping_subcounty, shipping_address) VALUES (?, ?, ?, 'pending', 'pending', ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsissssss", $user_id, $order_number, $cart_total, $payment_method, $shipping_name, $shipping_phone, $shipping_email, $shipping_county, $shipping_subcounty, $shipping_address);
        
        if(!$stmt->execute()) {
            throw new Exception("Error creating order: " . $stmt->error);
        }
        
        $order_id = $connection->insert_id;
        
        // Insert order items and update stock
        foreach($cart_products as $product) {
            $stmt = $connection->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $item_total = $product['price'] * $product['quantity'];
            $stmt->bind_param("iiidd", $order_id, $product['id'], $product['quantity'], $product['price'], $item_total);
            
            if(!$stmt->execute()) {
                throw new Exception("Error adding order item: " . $stmt->error);
            }
            
            // Update product stock
            $stmt = $connection->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->bind_param("ii", $product['quantity'], $product['id']);
            
            if(!$stmt->execute()) {
                throw new Exception("Error updating product stock: " . $stmt->error);
            }
        }
        
        // Commit transaction
        $connection->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        // Redirect to payment page
        header("Location: ?page=payment&order_id=" . $order_id);
        exit;
        
    } catch(Exception $e) {
        // Rollback transaction
        $connection->rollback();
        $error = $e->getMessage();
    }
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Checkout</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Complete your order information
            </p>
        </div>
        
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
        
        <div class="mt-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Shipping Information</h2>
                            
                            <form method="POST" class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="shipping_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <input type="text" name="shipping_name" id="shipping_name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                        <input type="text" name="shipping_phone" id="shipping_phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-6">
                                        <label for="shipping_email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="shipping_email" id="shipping_email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_county" class="block text-sm font-medium text-gray-700">County</label>
                                        <input type="text" name="shipping_county" id="shipping_county" value="<?php echo htmlspecialchars($user['county']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_subcounty" class="block text-sm font-medium text-gray-700">Subcounty</label>
                                        <input type="text" name="shipping_subcounty" id="shipping_subcounty" value="<?php echo htmlspecialchars($user['subcounty']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-6">
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700">Address</label>
                                        <textarea id="shipping_address" name="shipping_address" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-6">
                                    <h2 class="text-lg font-medium text-gray-900">Payment Method</h2>
                                    <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-3">
                                        <div>
                                            <input id="mpesa" name="payment_method" type="radio" value="mpesa" required class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                            <label for="mpesa" class="ml-3 block text-sm font-medium text-gray-700">
                                                M-Pesa
                                            </label>
                                        </div>
                                        <div>
                                            <input id="paypal" name="payment_method" type="radio" value="paypal" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                            <label for="paypal" class="ml-3 block text-sm font-medium text-gray-700">
                                                PayPal
                                            </label>
                                        </div>
                                        <div>
                                            <input id="card" name="payment_method" type="radio" value="card" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                            <label for="card" class="ml-3 block text-sm font-medium text-gray-700">
                                                Credit/Debit Card
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Place Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                            
                            <div class="mt-4 space-y-4">
                                <?php foreach($cart_products as $product): ?>
                                <div class="flex justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></p>
                                        <p class="text-sm text-gray-500">Qty: <?php echo $product['quantity']; ?></p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo format_currency($product['price'] * $product['quantity']); ?></p>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>