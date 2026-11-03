<?php
require_once "admin_auth.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    execute($conn, "DELETE FROM bookmark WHERE opportunity_id=?", "i", [$id]);
    execute($conn, "DELETE FROM opportunity WHERE opportunity_id=?", "i", [$id]);
    flash("Opportunity deleted successfully");
}
header("Location: admin_dashboard.php");
exit;
?>
