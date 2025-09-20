<!-- includes/functions.php -->
<?php

//error_reporting(E_ALL & ~E_WARNING);

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate slug
function create_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = trim($string);
    $string = preg_replace('/\s/', '-', $string);
    return $string;
}

// Function to format date
function format_date($date) {
    return date('M j, Y', strtotime($date));
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

// Function to send email
function send_email($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . ADMIN_EMAIL . "\r\n";
    return mail($to, $subject, $message, $headers);
}

// Function to check if user is admin
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
}

// Function to require admin authentication
function require_admin_auth() {
    if (!is_admin_logged_in()) {
        header("Location: ?page=admin/login");
        exit;
    }
}

// Function to format currency
function format_currency($amount) {
    return 'KES ' . number_format($amount, 2);
}

// Function to calculate progress percentage
function calculate_progress($current, $target) {
    if ($target <= 0) return 0;
    return min(100, round(($current / $target) * 100));
}
if (!function_exists('get_user_avatar')) {
    function get_user_avatar($name) {
        // If name is empty, fallback to "GU" (Guest User)
        if (empty($name)) {
            return 'GU';
        }

        // Split name into words
        $words = preg_split('/\s+/', trim($name));

        // Take first two words, or duplicate if only one word exists
        $first = strtoupper(substr($words[0], 0, 1));
        $second = isset($words[1]) 
            ? strtoupper(substr($words[1], 0, 1)) 
            : strtoupper(substr($words[0], 1, 1)); // Use 2nd letter of first word if no second word

        return $first . $second;
    }
}

?>