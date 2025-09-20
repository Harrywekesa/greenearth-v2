<!-- pages/orders.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if user is logged in
if(!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    $_SESSION['redirect_after_login'] = '?page=orders';
    header("Location: ?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user's orders
$stmt = $connection->prepare("
    SELECT o.*, 
           CASE 
               WHEN o.status = 'pending' THEN 'bg-yellow-100 text-yellow-800'
               WHEN o.status = 'processing' THEN 'bg-blue-100 text-blue-800'
               WHEN o.status = 'shipped' THEN 'bg-purple-100 text-purple-800'
               WHEN o.status = 'delivered' THEN 'bg-green-100 text-green-800'
               ELSE 'bg-gray-100 text-gray-800'
           END as status_class,
           CASE 
               WHEN o.payment_status = 'pending' THEN 'bg-yellow-100 text-yellow-800'
               WHEN o.payment_status = 'paid' THEN 'bg-green-100 text-green-800'
               WHEN o.payment_status = 'failed' THEN 'bg-red-100 text-red-800'
               ELSE 'bg-gray-100 text-gray-800'
           END as payment_status_class
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

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
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">My Orders</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Track and manage your orders
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
        
        <?php if(empty($orders)): ?>
        <div class="mt-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No orders yet</h3>
            <p class="mt-1 text-gray-500">Start shopping for eco-friendly products.</p>
            <div class="mt-6">
                <a href="?page=marketplace" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Browse Products
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-12">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Items
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Payment
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['order_number']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php 
                                            // Get item count
                                            $stmt = $connection->prepare("SELECT COUNT(*) as item_count FROM order_items WHERE order_id = ?");
                                            $stmt->bind_param("i", $order['id']);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            $item_count = $result->fetch_assoc()['item_count'];
                                            echo $item_count;
                                            ?> items
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo format_currency($order['total_amount']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $order['status_class']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $order['payment_status_class']; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="?page=order_detail&id=<?php echo $order['id']; ?>" class="text-green-600 hover:text-green-900">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>