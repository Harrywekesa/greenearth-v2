<!-- pages/payment.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if user is logged in
if(!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    $_SESSION['redirect_after_login'] = '?page=payment&order_id=' . (isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0);
    header("Location: ?page=login");
    exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if($order_id > 0) {
    // Get order details
    $stmt = $connection->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
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

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitize_input($_POST['payment_method']);
    $transaction_id = '';
    
    switch($payment_method) {
        case 'mpesa':
            $mpesa_number = sanitize_input($_POST['mpesa_number']);
            if(empty($mpesa_number)) {
                $error = "M-Pesa number is required";
                break;
            }
            // In a real implementation, this would process M-Pesa payment
            $transaction_id = 'MPESA' . strtoupper(uniqid());
            break;
            
        case 'paypal':
            // In a real implementation, this would redirect to PayPal
            $transaction_id = 'PAYPAL' . strtoupper(uniqid());
            break;
            
        case 'card':
            $card_number = sanitize_input($_POST['card_number']);
            $expiry_date = sanitize_input($_POST['expiry_date']);
            $cvv = sanitize_input($_POST['cvv']);
            $cardholder_name = sanitize_input($_POST['cardholder_name']);
            
            if(empty($card_number) || empty($expiry_date) || empty($cvv) || empty($cardholder_name)) {
                $error = "All card details are required";
                break;
            }
            
            // In a real implementation, this would process card payment
            $transaction_id = 'CARD' . strtoupper(uniqid());
            break;
            
        default:
            $error = "Invalid payment method";
    }
    
    if(!$error) {
        // Update order with payment details
        $stmt = $connection->prepare("UPDATE orders SET payment_status = 'paid', transaction_id = ?, payment_method = ? WHERE id = ?");
        $stmt->bind_param("ssi", $transaction_id, $payment_method, $order_id);
        
        if($stmt->execute()) {
            $message = "Payment processed successfully!";
            header("Location: ?page=order_confirmation&order_id=" . $order_id . "&message=" . urlencode($message));
            exit;
        } else {
            $error = "Error processing payment: " . $stmt->error;
        }
    }
}
?>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900">Payment Processing</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Complete your payment for order #<?php echo htmlspecialchars($order['order_number']); ?>
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
                            <h2 class="text-lg font-medium text-gray-900">Payment Method: <?php echo ucfirst($order['payment_method']); ?></h2>
                            
                            <?php switch($order['payment_method']):
                                case 'mpesa': ?>
                                <div class="mt-6">
                                    <div class="bg-blue-50 rounded-lg p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-lg font-medium text-blue-800">M-Pesa Payment</h3>
                                                <p class="mt-1 text-blue-700">Send KES <?php echo number_format($order['total_amount'], 2); ?> to PayBill Number: 123456</p>
                                                <p class="mt-1 text-blue-700">Account Number: <?php echo $order['order_number']; ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6">
                                            <form method="POST" action="">
                                                <input type="hidden" name="payment_method" value="mpesa">
                                                
                                                <div>
                                                    <label for="mpesa_number" class="block text-sm font-medium text-blue-700">M-Pesa Phone Number</label>
                                                    <input type="tel" name="mpesa_number" id="mpesa_number" placeholder="2547XXXXXXXX" required class="mt-1 block w-full border border-blue-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                                
                                                <div class="mt-4">
                                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        Confirm Payment
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php break;
                                
                                case 'paypal': ?>
                                <div class="mt-6">
                                    <div class="bg-blue-50 rounded-lg p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-lg font-medium text-blue-800">PayPal Payment</h3>
                                                <p class="mt-1 text-blue-700">Complete your payment through PayPal</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6">
                                            <form method="POST" action="">
                                                <input type="hidden" name="payment_method" value="paypal">
                                                
                                                <div>
                                                    <label for="paypal_email" class="block text-sm font-medium text-blue-700">PayPal Email</label>
                                                    <input type="email" name="paypal_email" id="paypal_email" placeholder="your-paypal@email.com" required class="mt-1 block w-full border border-blue-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                                
                                                <div class="mt-4">
                                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        Pay with PayPal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php break;
                                
                                case 'card': ?>
                                <div class="mt-6">
                                    <div class="bg-green-50 rounded-lg p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-12 w-12 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-lg font-medium text-green-800">Credit/Debit Card Payment</h3>
                                                <p class="mt-1 text-green-700">Secure payment with your card</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6">
                                            <form method="POST" action="">
                                                <input type="hidden" name="payment_method" value="card">
                                                
                                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                                    <div class="sm:col-span-6">
                                                        <label for="card_number" class="block text-sm font-medium text-green-700">Card Number</label>
                                                        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" required class="mt-1 block w-full border border-green-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                    </div>
                                                    
                                                    <div class="sm:col-span-3">
                                                        <label for="expiry_date" class="block text-sm font-medium text-green-700">Expiry Date</label>
                                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" required class="mt-1 block w-full border border-green-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                    </div>
                                                    
                                                    <div class="sm:col-span-3">
                                                        <label for="cvv" class="block text-sm font-medium text-green-700">CVV</label>
                                                        <input type="text" name="cvv" id="cvv" placeholder="123" required class="mt-1 block w-full border border-green-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                    </div>
                                                    
                                                    <div class="sm:col-span-6">
                                                        <label for="cardholder_name" class="block text-sm font-medium text-green-700">Cardholder Name</label>
                                                        <input type="text" name="cardholder_name" id="cardholder_name" required class="mt-1 block w-full border border-green-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-6">
                                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Process Payment
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php break;
                            endswitch; ?>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                            
                            <div class="mt-4 space-y-4">
                                <?php foreach($order_items as $item): ?>
                                <div class="flex justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="text-sm text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo format_currency($item['total']); ?></p>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="border-t border-gray-200 pt-4">
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
                    
                    <div class="mt-8 bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900">Shipping Information</h2>
                            <div class="mt-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_name']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_county']); ?>, <?php echo htmlspecialchars($order['shipping_subcounty']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_phone']); ?></div>
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['shipping_email']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>