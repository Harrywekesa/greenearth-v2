<!-- admin/includes/functions.php -->
<?php
// Function to sanitize input
function sanitize_input($data) {
    if(is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to create slug
function create_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = trim($string);
    $string = preg_replace('/\s/', '-', $string);
    return $string;
}

// Function to format currency
function format_currency($amount) {
    return 'KES ' . number_format($amount, 2);
}

// Function to calculate progress
function calculate_progress($current, $target) {
    if ($target <= 0) return 0;
    return min(100, round(($current / $target) * 100));
}

// Function to get setting
function get_setting($key, $default = '') {
    global $connection;
    $stmt = $connection->prepare("SELECT value FROM settings WHERE `key` = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['value'];
    }
    return $default;
}

// Function to update setting
function update_setting($key, $value) {
    global $connection;
    $stmt = $connection->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
    $stmt->bind_param("sss", $key, $value, $value);
    return $stmt->execute();
}
?>