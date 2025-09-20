<!-- pages/admin/orders/view.php -->
<?php
global $connection;
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

if($order_id > 0) {
    // Get order with user info
    $stmt = $connection->prepare("
        SELECT o.*, u.name as customer_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if(!$order) {
        header("Location: ?page=admin/orders/list");
        exit;
    }
    
    // Get order items
    $stmt = $connection->prepare("
        SELECT oi.*, p.name as product_name 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_items = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: ?page=admin/orders/list");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = sanitize_input($_POST['status']);
    $payment_status = sanitize_input($_POST['payment_status']);
    
    $stmt = $connection->prepare("UPDATE orders SET status = ?, payment_status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $payment_status, $order_id);
    
    if($stmt->execute()) {
        $message = "Order updated successfully!";
        // Refresh order data
        $stmt = $connection->prepare("
            SELECT o.*, u.name as customer_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
    } else {
        $error = "Error updating order: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - GreenEarth Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include '../components/header.php'; ?>
    
    <div class="admin-layout">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content pb-8">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <a href="?page=admin/orders/list" class="text-sm font-medium text-green-600 hover:text-green-500">
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
                    
                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Order Details -->
                        <div class="lg:col-span-2">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="space-y-4">
                                        <?php foreach($order_items as $item): ?>
                                        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                                            <div>
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                <div class="text-sm text-gray-500">Quantity: <?php echo $item['quantity']; ?></div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-medium text-gray-900"><?php echo format_currency($item['total']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo format_currency($item['price']); ?> each</div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="mt-6 pt-4 border-t border-gray-200">
                                        <div class="flex justify-between text-lg font-medium text-gray-900">
                                            <div>Total</div>
                                            <div><?php echo format_currency($order['total_amount']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Summary</h3>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <dl class="space-y-4">
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                                            <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($order['order_number']); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                            <dd class="text-sm text-gray-900"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">Customer</dt>
                                            <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                                            <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_email']); ?></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                            <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_phone']); ?></dd>
                                        </div>
                                    </dl>
                                    
                                    <form method="POST" class="mt-6 space-y-4">
                                        <div>
                                            <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                                            <select id="status" name="status" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                                            <select id="payment_status" name="payment_status" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="paid" <?php echo $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                            </select>
                                        </div>
                                        
                                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Update Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Shipping Address -->
                            <div class="mt-6 bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Shipping Address</h3>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <address class="text-sm text-gray-900 not-italic">
                                        <div><?php echo htmlspecialchars($order['shipping_name']); ?></div>
                                        <div><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                                        <div><?php echo htmlspecialchars($order['shipping_county']); ?>, <?php echo htmlspecialchars($order['shipping_subcounty']); ?></div>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>