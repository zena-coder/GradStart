<?php
require_once "auth.php";
if ($_SESSION['is_admin'] == 1) {
    header("Location: admin_change_password.php");
    exit;
}
$error = "";
$errorFields = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old = isset($_POST['old_password']) ? $_POST['old_password'] : "";
    $new = isset($_POST['new_password']) ? $_POST['new_password'] : "";
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

    if ($old === "") {
        $errorFields[] = "old_password";
    }
    if ($new === "") {
        $errorFields[] = "new_password";
    }
    if ($confirm === "") {
        $errorFields[] = "confirm_password";
    }

    if (count($errorFields) > 0) {
        $error = "Please fill in all required fields";
    } else {
        $user = fetch_one($conn, "SELECT password_hash FROM user WHERE user_id=?", "i", [$_SESSION['user_id']]);
        if (!$user || !password_verify($old, $user['password_hash'])) {
            $error = "Your current password is incorrect";
            $errorFields = ["old_password"];
        } elseif (strlen($new) < 8) {
            $error = "New password must be at least 8 characters";
            $errorFields = ["new_password"];
        } elseif ($new !== $confirm) {
            $error = "New passwords do not match";
            $errorFields = ["new_password", "confirm_password"];
        } elseif ($new === $old) {
            $error = "New password must be different from your current password";
            $errorFields = ["new_password"];
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            execute($conn, "UPDATE user SET password_hash=? WHERE user_id=?", "si", [$hashed, $_SESSION['user_id']]);
            flash("Password changed successfully");
            header("Location: profile.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Change Password - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
     <div class="breadcrumb">
        <a href="index.php">Home</a><span>/</span><a href="profile.php">Profile</a><span>/</span>Change Password
    </div>
    <div class="form-box">
        <h2>Change Password</h2>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <form method="POST" action="change_password.php" novalidate>
            <label>Current Password</label>
            <div class="password-wrap">
                <input type="password" name="old_password" class="<?php echo ec('old_password', $errorFields); ?>">
                <button type="button" class="toggle-password"></button>
            </div>
            <label>New Password</label>
            <div class="password-wrap">
                <input type="password" name="new_password" class="<?php echo ec('new_password', $errorFields); ?>">
                <button type="button" class="toggle-password"></button>
            </div>
            <label>Confirm New Password</label>
            <div class="password-wrap">
                <input type="password" name="confirm_password" class="<?php echo ec('confirm_password', $errorFields); ?>">
                <button type="button" class="toggle-password"></button>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Password</button>
                <a href="profile.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include "footer.php"; ?>
<script src="script.js"></script>
</body>
</html>
