<!-- pages/logout.php -->
<?php
// Session already started in index.php, no need to start again
// Config already included in index.php, no need to include again

// Destroy user session
if(isset($_SESSION['user_logged_in'])) {
    unset($_SESSION['user_logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_phone']);
    unset($_SESSION['user_role']);
}

// Clear cart
$_SESSION['cart'] = [];

// Redirect to home page
header("Location: ?page=home&message=" . urlencode("You have been logged out successfully!"));
exit;
?>