<!-- pages/order_detail.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if user is logged in
if(!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    $_SESSION['redirect_after_login'] = '?page=order_detail&id=' . (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    header("Location: ?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($order_id > 0) {
    // Get order details
    $stmt = $connection->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if(!$order) {
        header("Location: ?page=orders");
        exit;
    }
    
    // Get order items
    $stmt = $connection->prepare("SELECT oi.*, p.name as product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_items = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: ?page=orders");
    exit;
}

// Get message from URL if exists
if(isset($_GET['message'])) {
    $message = sanitize_input($_GET['message']);
}
if(isset($_GET['error'])) {
    $error = sanitize_input($_GET['error']);
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
            <a href="?page=orders" class="text-sm font-medium text-green-600 hover:text-green-500">
                ← Back to orders
            </a>
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
        
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($order['order_number']); ?></h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Placed on <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php 
                                switch($order['status']) {
                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                    case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                    case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                    case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                }
                                ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                        <div class="mt-4 space-y-6">
                            <?php foreach($order_items as $item): ?>
                            <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div class="text-sm text-gray-500">Quantity: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900"><?php echo format_currency($item['total']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo format_currency($item['price']); ?> each</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium text-gray-900"><?php echo format_currency($order['total_amount']); ?></span>
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
                                    <span class="text-base font-medium text-gray-900">Total Paid</span>
                                    <span class="text-base font-medium text-gray-900"><?php echo format_currency($order['total_amount']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Shipping Information</h3>
                            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_name']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_county']); ?>, <?php echo htmlspecialchars($order['shipping_subcounty']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_phone']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_email']); ?></div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
                            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-900">Method: <?php echo ucfirst($order['payment_method']); ?></div>
                                <div class="text-sm text-gray-900">Transaction ID: <?php echo htmlspecialchars($order['transaction_id']); ?></div>
                                <div class="text-sm text-gray-900">
                                    Status: 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?php 
                                        switch($order['payment_status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'paid': echo 'bg-green-100 text-green-800'; break;
                                            case 'failed': echo 'bg-red-100 text-red-800'; break;
                                        }
                                        ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-4 bg-gray-50 sm:px-6">
                    <div class="flex justify-end">
                        <?php if($order['payment_status'] === 'pending'): ?>
                        <a href="?page=payment&order_id=<?php echo $order['id']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Complete Payment
                        </a>
                        <?php endif; ?>
                        <a href="?page=marketplace" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Next Steps -->
        <div class="mt-12">
            <div class="bg-green-50 rounded-lg p-8">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-2xl font-bold text-gray-900">What Happens Next?</h2>
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <div class="flex justify-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Order Processing</h3>
                            <p class="mt-2 text-sm text-gray-500">We're preparing your order for shipment</p>
                        </div>
                        <div>
                            <div class="flex justify-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m16-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Shipment</h3>
                            <p class="mt-2 text-sm text-gray-500">Your order will be shipped within 3-5 business days</p>
                        </div>
                        <div>
                            <div class="flex justify-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Delivery</h3>
                            <p class="mt-2 text-sm text-gray-500">Track your order and receive delivery updates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>