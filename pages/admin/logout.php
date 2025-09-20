<!-- pages/admin/logout.php -->
<?php
// Session already started in index.php
if (isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
}

header("Location: ?page=admin/login");
exit;
?>