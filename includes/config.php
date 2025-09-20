<!-- includes/config.php -->
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'greenearth');

// Site configuration
define('SITE_URL', 'http://localhost/greenearth');
define('SITE_NAME', 'GreenEarth');
define('ADMIN_EMAIL', 'admin@greenearth.org');

// Add to includes/config.php

// Initialize cart session
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart function
function add_to_cart($product_id, $quantity = 1) {
    global $connection;
    
    // Get product info
    $stmt = $connection->prepare("SELECT id, price, stock_quantity FROM products WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if($product) {
        // Check if product already in cart
        if(isset($_SESSION['cart'][$product_id])) {
            // Update quantity (check stock)
            $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
            if($new_quantity <= $product['stock_quantity']) {
                $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
                return true;
            }
        } else {
            // Add new item (check stock)
            if($quantity <= $product['stock_quantity']) {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
                return true;
            }
        }
    }
    return false;
}

// Remove from cart
function remove_from_cart($product_id) {
    if(isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }
    return false;
}

// Update cart quantity
function update_cart_quantity($product_id, $quantity) {
    global $connection;
    
    if($quantity <= 0) {
        return remove_from_cart($product_id);
    }
    
    // Get product stock
    $stmt = $connection->prepare("SELECT stock_quantity FROM products WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if($product && $quantity <= $product['stock_quantity']) {
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            return true;
        }
    }
    return false;
}

// Get cart total
function get_cart_total() {
    $total = 0;
    foreach($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Get cart item count
function get_cart_item_count() {
    $count = 0;
    foreach($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'database.php';
?>