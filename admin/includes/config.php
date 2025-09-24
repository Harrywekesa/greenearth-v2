<!-- admin/includes/config.php -->
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'greenearth');

// Site configuration
define('SITE_URL', 'http://localhost/greenearth/admin');
define('ADMIN_EMAIL', 'admin@greenearth.com');

// Connect to database
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Set charset
$connection->set_charset("utf8");

// Include functions
require_once 'functions.php';
?>