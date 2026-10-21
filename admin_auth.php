<?php
require_once "config.php";
if (!isset($_SESSION['user_id'])) {
    show_message_page("Access denied", "You need to log in as an administrator to view this page.", 403, "Go to Login", "login.php");
}
if ($_SESSION['is_admin'] != 1) {
    show_message_page("Access denied", "You don't have permission to view this page.", 403, "Back to Home", "index.php");
}
?>
