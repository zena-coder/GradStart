<?php
session_start();
ob_start();
mysqli_report(MYSQLI_REPORT_OFF);
ini_set('display_errors', '0');

define('MAINTENANCE_MODE', false);
define('MAINTENANCE_BACK', 'Sunday, 25 October 2026 at 10:00 AM');

function h($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function ec($name, $fields)
{
    return in_array($name, $fields) ? "field-error" : "";
}

function bookmark_icon($filled)
{
    $fill = $filled ? "currentColor" : "none";
    return '<svg class="bookmark-icon" viewBox="0 0 24 24" width="24" height="24" fill="' . $fill . '" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M6 3h12a1 1 0 0 1 1 1v17l-7-4.5L5 21V4a1 1 0 0 1 1-1z"/></svg>';
}

function flash($text)
{
    $_SESSION['message'] = $text;
}

function show_message_page($title, $text, $code = 500, $linkText = "", $linkUrl = "")
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($code);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>' . h($title) . ' - GradStart</title>';
    echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">';
    echo '<link rel="stylesheet" href="style.css"></head><body><div class="auth-wrapper"><div class="auth-card message-card">';
    echo '<img src="logo.png" class="auth-logo" alt="GradStart"><h2>' . h($title) . '</h2><p>' . h($text) . '</p>';
    if ($linkUrl != "") {
        echo '<a href="' . h($linkUrl) . '" class="btn btn-primary btn-block">' . h($linkText) . '</a>';
    }
    echo '</div></div></body></html>';
    exit;
}

set_exception_handler(function ($e) {
    show_message_page("Something went wrong", "We're sorry, something went wrong on our side. Please try again in a few minutes.", 500);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        show_message_page("Something went wrong", "We're sorry, something went wrong on our side. Please try again in a few minutes.", 500);
    }
});

if (MAINTENANCE_MODE) {
    show_message_page("We'll be back soon", "GradStart is down for scheduled maintenance. We expect to be back on " . MAINTENANCE_BACK . ".", 503);
}

$conn = @mysqli_connect("localhost", "root", "root", "gradstart");
if (!$conn) {
    show_message_page("Something went wrong", "We're sorry, we couldn't load the page right now. Please try again in a few minutes.", 500);
}
mysqli_set_charset($conn, "utf8mb4");

function run($conn, $sql, $types = "", $params = [])
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Query failed");
    }
    if ($types !== "") {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Query failed");
    }
    return $stmt;
}

function fetch_all($conn, $sql, $types = "", $params = [])
{
    $stmt = run($conn, $sql, $types, $params);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function fetch_one($conn, $sql, $types = "", $params = [])
{
    $rows = fetch_all($conn, $sql, $types, $params);
    return count($rows) > 0 ? $rows[0] : null;
}

function execute($conn, $sql, $types = "", $params = [])
{
    $stmt = run($conn, $sql, $types, $params);
    mysqli_stmt_close($stmt);
}

function apply_url($link)
{
    $link = trim($link);
    if (filter_var($link, FILTER_VALIDATE_EMAIL)) {
        return "mailto:" . $link;
    }
    if (!preg_match('#^https?://#i', $link)) {
        return "https://" . $link;
    }
    return $link;
}
?>
