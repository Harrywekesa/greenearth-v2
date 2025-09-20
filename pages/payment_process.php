<!-- pages/payment_process.php -->
<?php
global $connection;
$message = '';
$error = '';

// Check if user is logged in
if(!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header("Location: ?page=login");
    exit;
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$payment_method = sanitize_input($_POST['payment_method']);

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
    
    // Process payment based on method
    switch($payment_method) {
        case 'mpesa':
            $mpesa_code = sanitize_input($_POST['mpesa_code']);
            
            // Validate M-Pesa code format (MPESA followed by numbers)
            if(!preg_match('/^MPESA\d+$/', $mpesa_code)) {
                $error = "Invalid M-Pesa transaction code format";
                break;
            }
            
            // Update order with payment details
            $stmt = $connection->prepare("UPDATE orders SET payment_status = 'paid', transaction_id = ?, payment_method = ? WHERE id = ?");
            $stmt->bind_param("ssi", $mpesa_code, $payment_method, $order_id);
            
            if($stmt->execute()) {
                $message = "Payment processed successfully!";
                // Send confirmation email (in a real implementation)
                // send_payment_confirmation_email($order['shipping_email'], $order['order_number']);
                header("Location: ?page=order_confirmation&order_id=" . $order_id . "&message=" . urlencode($message));
                exit;
            } else {
                $error = "Error processing payment: " . $stmt->error;
            }
            break;
            
        case 'paypal':
            // In a real implementation, this would redirect to PayPal
            // For demo purposes, we'll mark as paid
            $transaction_id = 'PAYPAL-' . uniqid();
            
            $stmt = $connection->prepare("UPDATE orders SET payment_status = 'paid', transaction_id = ?, payment_method = ? WHERE id = ?");
            $stmt->bind_param("ssi", $transaction_id, $payment_method, $order_id);
            
            if($stmt->execute()) {
                $message = "Payment processed successfully!";
                header("Location: ?page=order_confirmation&order_id=" . $order_id . "&message=" . urlencode($message));
                exit;
            } else {
                $error = "Error processing payment: " . $stmt->error;
            }
            break;
            
        case 'card':
            // Validate card details (demo only - in real implementation, use payment gateway)
            $card_number = sanitize_input($_POST['card_number']);
            $expiry_date = sanitize_input($_POST['expiry_date']);
            $cvv = sanitize_input($_POST['cvv']);
            $cardholder_name = sanitize_input($_POST['cardholder_name']);
            
            // Basic validation
            if(strlen(str_replace(' ', '', $card_number)) < 16) {
                $error = "Invalid card number";
                break;
            }
            
            if(!preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
                $error = "Invalid expiry date format";
                break;
            }
            
            if(strlen($cvv) < 3) {
                $error = "Invalid CVV";
                break;
            }
            
            // Process payment (demo - in real implementation, use Stripe/PayPal)
            $transaction_id = 'CARD-' . uniqid();
            
            $stmt = $connection->prepare("UPDATE orders SET payment_status = 'paid', transaction_id = ?, payment_method = ? WHERE id = ?");
            $stmt->bind_param("ssi", $transaction_id, $payment_method, $order_id);
            
            if($stmt->execute()) {
                $message = "Payment processed successfully!";
                header("Location: ?page=order_confirmation&order_id=" . $order_id . "&message=" . urlencode($message));
                exit;
            } else {
                $error = "Error processing payment: " . $stmt->error;
            }
            break;
            
        default:
            $error = "Invalid payment method";
    }
} else {
    header("Location: ?page=orders");
    exit;
}

// If there's an error, redirect back to payment page
if($error) {
    header("Location: ?page=payment&order_id=" . $order_id . "&error=" . urlencode($error));
    exit;
}
?>