<!-- pages/add_to_cart.php -->
<?php
global $connection;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if($product_id > 0 && $quantity > 0) {
        // Get product info
        $stmt = $connection->prepare("SELECT id, name, price, stock_quantity FROM products WHERE id = ? AND is_active = 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if($product) {
            // Check stock
            if($quantity <= $product['stock_quantity']) {
                // Initialize cart if not exists
                if(!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                
                // Add to cart
                if(isset($_SESSION['cart'][$product_id])) {
                    // Update quantity (check stock limit)
                    $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
                    if($new_quantity <= $product['stock_quantity']) {
                        $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
                        $_SESSION['cart_message'] = "Updated quantity in cart!";
                    } else {
                        $_SESSION['cart_error'] = "Not enough stock available. Maximum " . $product['stock_quantity'] . " items.";
                    }
                } else {
                    // Add new item
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity
                    ];
                    $_SESSION['cart_message'] = "Product added to cart!";
                }
            } else {
                $_SESSION['cart_error'] = "Not enough stock available. Only " . $product['stock_quantity'] . " items in stock.";
            }
        } else {
            $_SESSION['cart_error'] = "Product not found.";
        }
    } else {
        $_SESSION['cart_error'] = "Invalid product or quantity.";
    }
} else {
    $_SESSION['cart_error'] = "Invalid request method.";
}

// Redirect back to referring page or marketplace
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?page=marketplace';
header("Location: " . $referer);
exit;
?>