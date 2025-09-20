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
$total_amount = 0;
if(!empty($cart_items)) {
    $placeholders = str_repeat('?,', count($cart_items) - 1) . '?';
    $types = str_repeat('i', count($cart_items));
    $ids = array_keys($cart_items);
    
    $stmt = $connection->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_products = $result->fetch_all(MYSQLI_ASSOC);
    
    // Merge cart quantities with product data and calculate total
    foreach($cart_products as &$product) {
        $product['cart_quantity'] = $cart_items[$product['id']]['quantity'];
        $total_amount += $product['price'] * $product['cart_quantity'];
    }
}

// Get user info for pre-filling
$stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get Kenyan counties
$counties = [
    'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Nyeri', 'Kisii', 'Meru', ' Machakos', 
    'Kakamega', 'Siaya', 'Nyandarua', 'Kilifi', 'Kajiado', 'Embu', 'Bomet', 'Busia', 
    'Tharaka-Nithi', 'Trans Nzoia', 'Turkana', 'West Pokot', 'Uasin Gishu', 'Vihiga', 
    'Wajir', 'Migori', 'Kitui', 'Laikipia', 'Kericho', 'Kakamega', 'Nandi', 'Homa Bay', 
    'Garissa', 'Taita-Taveta', 'Marsabit', 'Isiolo', 'Mandera', 'Samburu', 'Murang\'a', 
    'Kirinyaga', 'Kiambu', 'Lamu', 'Baringo', 'Bungoma', 'Elgeyo-Marakwet'
];

// Get subcounties based on selected county
$subcounties = [];
$selected_county = isset($_POST['county']) ? sanitize_input($_POST['county']) : $user['county'];

if($selected_county) {
    // In a real implementation, you would have a subcounties table
    // For now, we'll use predefined subcounties for major counties
    $subcounty_data = [
        'Nairobi' => ['Westlands', 'Karen', 'Parklands', 'Dagoretti', 'Langata', 'Kibera', 'Madaraka', 'Kangemi'],
        'Mombasa' => ['Mvita', 'Changamwe', 'Jomvu', 'Kisauni', 'Nyali', 'Likoni', 'Mombasa Island'],
        'Kisumu' => ['Kisumu East', 'Kisumu West', 'Kisumu Central', 'Seme', 'Nyando', 'Muhoroni', 'Nyakach'],
        'Nakuru' => ['Nakuru East', 'Nakuru West', 'Kuresoi North', 'Kuresoi South', 'Bahati', 'Rongai'],
        'Eldoret' => ['Eldoret East', 'Eldoret West', 'Eldoret South', 'Kesses', 'Moiben', 'Soy'],
        'Machakos' => ['Machakos Town', 'Mwala', 'Kangundo', 'Matungulu', 'Kathiani', 'Mavoko']
    ];
    
    $subcounties = isset($subcounty_data[$selected_county]) ? $subcounty_data[$selected_county] : [];
}

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
        // FIX: Proper parameter binding - removed extra parameter
        $stmt = $connection->prepare("INSERT INTO orders (user_id, order_number, total_amount, payment_method, shipping_name, shipping_phone, shipping_email, shipping_county, shipping_subcounty, shipping_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsisssss", $user_id, $order_number, $total_amount, $payment_method, $shipping_name, $shipping_phone, $shipping_email, $shipping_county, $shipping_subcounty, $shipping_address);
        
        if(!$stmt->execute()) {
            throw new Exception("Error creating order: " . $stmt->error);
        }
        
        $order_id = $connection->insert_id;
        
        // Insert order items
        foreach($cart_products as $product) {
            $stmt = $connection->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $item_total = $product['price'] * $product['cart_quantity'];
            $stmt->bind_param("iiidd", $order_id, $product['id'], $product['cart_quantity'], $product['price'], $item_total);
            
            if(!$stmt->execute()) {
                throw new Exception("Error adding order item: " . $stmt->error);
            }
            
            // Update product stock
            $stmt = $connection->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->bind_param("ii", $product['cart_quantity'], $product['id']);
            
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
        
        <div class="mt-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Shipping Information</h2>
                            
                            <form method="POST" class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-6">
                                        <label for="shipping_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <input type="text" name="shipping_name" id="shipping_name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="shipping_email" id="shipping_email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                        <input type="text" name="shipping_phone" id="shipping_phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_county" class="block text-sm font-medium text-gray-700">County</label>
                                        <select id="shipping_county" name="shipping_county" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                            <option value="">Select a county</option>
                                            <?php foreach($counties as $county): ?>
                                            <option value="<?php echo htmlspecialchars($county); ?>" <?php echo $selected_county === $county ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($county); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="shipping_subcounty" class="block text-sm font-medium text-gray-700">Subcounty</label>
                                        <select id="shipping_subcounty" name="shipping_subcounty" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                            <option value="">Select a subcounty</option>
                                            <?php foreach($subcounties as $subcounty): ?>
                                            <option value="<?php echo htmlspecialchars($subcounty); ?>" <?php echo (isset($user['subcounty']) && $user['subcounty'] === $subcounty) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($subcounty); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="sm:col-span-6">
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700">Delivery Instructions</label>
                                        <textarea id="shipping_address" name="shipping_address" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                        <p class="mt-1 text-sm text-gray-500">You will collect your items from the nearest G4S office in your subcounty</p>
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
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                            
                            <div class="mt-4 space-y-4">
                                <?php foreach($cart_products as $product): ?>
                                <div class="flex justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></p>
                                        <p class="text-sm text-gray-500">Qty: <?php echo $product['cart_quantity']; ?></p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo format_currency($product['price'] * $product['cart_quantity']); ?></p>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-medium text-gray-900"><?php echo format_currency($total_amount); ?></span>
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
                                        <span class="text-base font-medium text-gray-900"><?php echo format_currency($total_amount); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 bg-green-50 rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900">Delivery Information</h2>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">
                                Your items will be delivered to the nearest G4S office in your selected subcounty. 
                                You'll receive a notification when your items are ready for pickup.
                            </p>
                            <div class="mt-4 flex items-center">
                                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-sm text-green-700">Free delivery to all G4S locations</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dynamic subcounty loading based on county selection
document.getElementById('shipping_county').addEventListener('change', function() {
    const county = this.value;
    const subcountySelect = document.getElementById('shipping_subcounty');
    
    // Clear existing options
    subcountySelect.innerHTML = '<option value="">Select a subcounty</option>';
    
    // Define subcounty data
    const subcountyData = {
        'Nairobi': ['Westlands', 'Karen', 'Parklands', 'Dagoretti', 'Langata', 'Kibera', 'Madaraka', 'Kangemi'],
        'Mombasa': ['Mvita', 'Changamwe', 'Jomvu', 'Kisauni', 'Nyali', 'Likoni', 'Mombasa Island'],
        'Kisumu': ['Kisumu East', 'Kisumu West', 'Kisumu Central', 'Seme', 'Nyando', 'Muhoroni', 'Nyakach'],
        'Nakuru': ['Nakuru East', 'Nakuru West', 'Kuresoi North', 'Kuresoi South', 'Bahati', 'Rongai'],
        'Eldoret': ['Eldoret East', 'Eldoret West', 'Eldoret South', 'Kesses', 'Moiben', 'Soy'],
        'Machakos': ['Machakos Town', 'Mwala', 'Kangundo', 'Matungulu', 'Kathiani', 'Mavoko']
    };
    
    // Populate subcounties if available
    if(county && subcountyData[county]) {
        subcountyData[county].forEach(function(subcounty) {
            const option = document.createElement('option');
            option.value = subcounty;
            option.textContent = subcounty;
            subcountySelect.appendChild(option);
        });
    }
    
    // Show delivery info
    if(county) {
        document.getElementById('delivery-info').classList.remove('hidden');
    } else {
        document.getElementById('delivery-info').classList.add('hidden');
    }
});
</script>