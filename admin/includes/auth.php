<!-- admin/includes/auth.php -->
<?php
// Function to check if admin is logged in
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
}

// Function to require admin authentication
function require_admin_auth() {
    if(!is_admin_logged_in()) {
        header("Location: ?page=login");
        exit;
    }
}

// Function to get admin name
function get_admin_name() {
    return isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
}

// Function to get admin email
function get_admin_email() {
    return isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : 'admin@greenearth.org';
}
?>