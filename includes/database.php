<!-- includes/database.php -->
<?php
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Set charset
$connection->set_charset("utf8");
?>