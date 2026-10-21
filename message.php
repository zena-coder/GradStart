<?php
if (isset($_SESSION['message'])) {
    echo '<div class="success">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}
?>
