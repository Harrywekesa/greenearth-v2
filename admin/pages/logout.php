<!-- pages/admin/logout.php -->
<?php
// Session already started in index.php, no need to start again
// Config already included in index.php, no need to include again

// Destroy admin session
if(isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_phone']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_county']);
    unset($_SESSION['admin_subcounty']);
}

// Clear cart if it belongs to admin session
$_SESSION['cart'] = [];

// Set logout message
$_SESSION['logout_message'] = "You have been logged out successfully!";

// Redirect to admin login page
header("Location: ?page=login");
exit;
?>