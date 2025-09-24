<!-- admin/index.php -->
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Handle authentication for admin pages
if($page !== 'login' && $page !== 'logout') {
    if(!is_admin_logged_in()) {
        header("Location: ?page=login");
        exit;
    }
}

// Route to appropriate page
switch($page) {
    case 'login':
        include 'pages/login.php';
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'zones/list':
        include 'pages/zones/list.php';
        break;
    case 'zones/create':
        include 'pages/zones/create.php';
        break;
    case 'zones/edit':
        include 'pages/zones/edit.php';
        break;
    case 'zones/import':
        include 'pages/zones/import.php';
        break;
    case 'partners/list':
        include 'pages/partners/list.php';
        break;
    case 'partners/create':
        include 'pages/partners/create.php';
        break;
    case 'partners/edit':
        include 'pages/partners/edit.php';
        break;
    case 'initiatives/list':
        include 'pages/initiatives/list.php';
        break;
    case 'initiatives/create':
        include 'pages/initiatives/create.php';
        break;
    case 'initiatives/edit':
        include 'pages/initiatives/edit.php';
        break;
    case 'events/list':
        include 'pages/events/list.php';
        break;
    case 'events/create':
        include 'pages/events/create.php';
        break;
    case 'events/edit':
        include 'pages/events/edit.php';
        break;
    case 'products/list':
        include 'pages/products/list.php';
        break;
    case 'products/create':
        include 'pages/products/create.php';
        break;
    case 'products/edit':
        include 'pages/products/edit.php';
        break;
    case 'orders/list':
        include 'pages/orders/list.php';
        break;
    case 'orders/view':
        include 'pages/orders/view.php';
        break;
    case 'blog/list':
        include 'pages/blog/list.php';
        break;
    case 'blog/create':
        include 'pages/blog/create.php';
        break;
    case 'blog/edit':
        include 'pages/blog/edit.php';
        break;
    case 'users/list':
        include 'pages/users/list.php';
        break;
    case 'users/create':
        include 'pages/users/create.php';
        break;
    case 'users/edit':
        include 'pages/users/edit.php';
        break;
    case 'badges/list':
        include 'pages/badges/list.php';
        break;
    case 'badges/create':
        include 'pages/badges/create.php';
        break;
    case 'badges/edit':
        include 'pages/badges/edit.php';
        break;
    case 'settings':
        include 'pages/settings.php';
        break;
    case 'reports':
        include 'pages/reports.php';
        break;
    case 'notifications':
        include 'pages/notifications.php';
        break;
    case 'export':
        include 'pages/export.php';
        break;
    default:
        include 'pages/dashboard.php';
}
?>