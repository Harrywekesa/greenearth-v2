<!-- pages/subscribe.php -->
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = sanitize_input($_POST['email']);
    
    // Generate subscription token
    $token = bin2hex(random_bytes(16));
    
    // Check if already subscribed
    $stmt = $connection->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        // Update existing subscription
        $stmt = $connection->prepare("UPDATE newsletter_subscribers SET is_subscribed = 1, subscribed_at = NOW(), subscription_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
    } else {
        // Insert new subscription
        $stmt = $connection->prepare("INSERT INTO newsletter_subscribers (email, subscription_token, subscribed_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $email, $token);
    }
    
    if($stmt->execute()) {
        // Send confirmation email (in a real implementation)
        $_SESSION['message'] = "Thank you for subscribing to our newsletter!";
    } else {
        $_SESSION['error'] = "There was an error processing your subscription. Please try again.";
    }
}

header("Location: ?page=blog");
exit;
?>