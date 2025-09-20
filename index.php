<!-- index.php -->
<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Handle admin authentication for admin pages
if (strpos($page, 'admin/') === 0) {
    $admin_page = str_replace('admin/', '', $page);
    
    // Allow login page without authentication
    if ($admin_page === 'login') {
        include 'pages/admin/login.php';
        exit;
    }
    
    // Check authentication for other admin pages
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        header("Location: ?page=admin/login");
        exit;
    }
    
    // Check if file exists
    $admin_file = 'pages/admin/' . $admin_page . '.php';
    if (file_exists($admin_file)) {
        include $admin_file;
    } else {
        // Fallback to admin dashboard if page not found
        include 'pages/admin/dashboard.php';
    }
    exit;
}

// Include header for public pages
include 'includes/header.php';

// Route to appropriate page
switch($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'zones':
        include 'pages/zones.php';
        break;
    case 'zone_detail':
        include 'pages/zone_detail.php';
        break;
    case 'initiatives':
        include 'pages/initiatives.php';
        break;
    case 'initiative_detail':
        include 'pages/initiative_detail.php';
        break;
    case 'event_detail':
        include 'pages/event_detail.php';
        break;
    case 'marketplace':
        include 'pages/marketplace.php';
        break;
    case 'blog':
        include 'pages/blog.php';
        break;
    case 'blog_post':
        include 'pages/blog_post.php';
        break;
    case 'contact':
        include 'pages/contact.php';
        break;
    case 'subscribe':
        include 'pages/subscribe.php';
        break;
    case 'add_to_cart':
        include 'pages/add_to_cart.php';
        break;
    case 'cart':
        include 'pages/cart.php';
        break;
    case 'checkout':
        include 'pages/checkout.php';
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'register':
        include 'pages/register.php';  
        break;
    case 'profile':
        include 'pages/profile.php';
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    case 'payment':
        include 'pages/payment.php';
        break;
    default:
        include 'pages/home.php';
}

ob_end_flush();
?>