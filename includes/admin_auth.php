<!-- includes/admin_auth.php -->
<?php
// This file handles admin authentication WITHOUT HTML OUTPUT
// It should be included at the top of admin pages

// Function to require admin authentication
function require_admin_auth() {
    if(!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        // Store intended page in session
        if(isset($_GET['page'])) {
            $_SESSION['redirect_after_login'] = '?page=' . $_GET['page'];
        }
        header("Location: ?page=admin/login");
        exit;
    }
}

// Function to require partner authentication
function require_partner_auth() {
    if(!isset($_SESSION['partner_logged_in']) || !$_SESSION['partner_logged_in']) {
        // Store intended page in session
        if(isset($_GET['page'])) {
            $_SESSION['redirect_after_login'] = '?page=' . $_GET['page'];
        }
        header("Location: ?page=admin/login");
        exit;
    }
}

// Function to require either admin or partner authentication
function require_admin_or_partner_auth() {
    if((!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) && 
       (!isset($_SESSION['partner_logged_in']) || !$_SESSION['partner_logged_in'])) {
        // Store intended page in session
        if(isset($_GET['page'])) {
            $_SESSION['redirect_after_login'] = '?page=' . $_GET['page'];
        }
        header("Location: ?page=admin/login");
        exit;
    }
}
?>