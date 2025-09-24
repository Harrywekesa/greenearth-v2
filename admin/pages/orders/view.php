<!-- pages/orders/view.php -->
<?php
global $connection;
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: ?page=login");
    exit;
}

if($order_id > 0) {
    // Get order details
    $stmt = $connection->prepare("
        SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if(!$order) {
        header("Location: ?page=orders/list");
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
    header("Location: ?page=orders/list");
    exit;
}

// Handle order updates
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'update_status':
            $status = sanitize_input($_POST['status']);
            $payment_status = sanitize_input($_POST['payment_status']);
            
            $stmt = $connection->prepare("UPDATE orders SET status = ?, payment_status = ? WHERE id = ?");
            $stmt->bind_param("ssi", $status, $payment_status, $order_id);
            
            if($stmt->execute()) {
                $message = "Order status updated successfully!";
                // Refresh order data
                $stmt = $connection->prepare("
                    SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE o.id = ?
                ");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $order = $result->fetch_assoc();
            } else {
                $error = "Error updating order status: " . $stmt->error;
            }
            break;
            
        case 'delete_order':
            // Delete order items first
            $stmt = $connection->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            
            // Delete order
            $stmt = $connection->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            
            if($stmt->execute()) {
                $message = "Order deleted successfully!";
                header("Location: ?page=orders/list&message=" . urlencode($message));
                exit;
            } else {
                $error = "Error deleting order: " . $stmt->error;
            }
            break;
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
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 256px;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 20;
            background-color: #fff;
            border-right-width: 1px;
            border-color: #e5e7eb;
        }
        .main-content {
            flex: 1;
            margin-left: 0;
            padding-top: 64px;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
        }
        @media (max-width: 767px) {
            .sidebar {
                display: none;
            }
        }
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'components/header.php'; ?>
    
    <div class="admin-layout">
        <?php include 'components/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="px-4 md:px-10 py-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <a href="?page=orders/list" class="text-sm font-medium text-green-600 hover:text-green-500">
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
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-2">
                                <div class="bg-white rounded-lg shadow p-6">
                                    <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
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
                                                <span class="text-base font-medium text-gray-900">Total</span>
                                                <span class="text-base font-medium text-gray-900"><?php echo format_currency($order['total_amount']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="lg:col-span-1">
                                <div class="bg-white rounded-lg shadow p-6">
                                    <h2 class="text-lg font-medium text-gray-900">Order Details</h2>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Order Number</div>
                                            <div class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($order['order_number']); ?></div>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Order Date</div>
                                            <div class="mt-1 text-sm text-gray-900"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></div>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Customer</div>
                                            <div class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Shipping Address</div>
                                            <div class="mt-1 text-sm text-gray-900">
                                                <?php echo htmlspecialchars($order['shipping_name']); ?><br>
                                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                                <?php echo htmlspecialchars($order['shipping_county']); ?>, <?php echo htmlspecialchars($order['shipping_subcounty']); ?><br>
                                                <?php echo htmlspecialchars($order['shipping_phone']); ?><br>
                                                <?php echo htmlspecialchars($order['shipping_email']); ?>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Payment Method</div>
                                            <div class="mt-1 text-sm text-gray-900 capitalize"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Transaction ID</div>
                                            <div class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($order['transaction_id']); ?></div>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Status</div>
                                            <div class="mt-1">
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
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-500">Payment Status</div>
                                            <div class="mt-1">
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
                                    
                                    <!-- Update Order Status Form -->
                                    <div class="mt-6">
                                        <h3 class="text-md font-medium text-gray-900">Update Order Status</h3>
                                        <form method="POST" class="mt-4 space-y-4">
                                            <input type="hidden" name="action" value="update_status">
                                            
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
                                            
                                            <div class="flex justify-end">
                                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Update Status
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <!-- Delete Order Form -->
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <h3 class="text-md font-medium text-gray-900">Danger Zone</h3>
                                        <form method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                            <input type="hidden" name="action" value="delete_order">
                                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Delete Order
                                            </button>
                                        </form>
                                    </div>
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