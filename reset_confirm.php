<?php
require_once "config.php";
$error = "";
$errorFields = [];
$token = isset($_GET['token']) ? $_GET['token'] : "";
$reset = fetch_one($conn, "SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()", "s", [$token]);

if ($reset && $_SERVER["REQUEST_METHOD"] == "POST") {
    $password = isset($_POST['new_password']) ? $_POST['new_password'] : "";
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

    if ($password === "") {
        $errorFields[] = "new_password";
    }
    if ($confirm === "") {
        $errorFields[] = "confirm_password";
    }

    if (count($errorFields) > 0) {
        $error = "Please fill in all required fields";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
        $errorFields = ["new_password"];
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
        $errorFields = ["new_password", "confirm_password"];
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        execute($conn, "UPDATE user SET password_hash=? WHERE email=?", "ss", [$hashed, $reset['email']]);
        execute($conn, "DELETE FROM password_resets WHERE email=?", "s", [$reset['email']]);
        flash("Password updated successfully! You can now log in.");
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Set New Password - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <img src="logo.png" class="auth-logo" alt="GradStart">
        <h2>Choose a new password</h2>
        <?php if (!$reset) { ?>
            <div class="error">This reset link is invalid or has expired</div>
            <div class="links"><a href="reset_password.php">Request a new link</a></div>
        <?php } else { ?>
            <p class="subtitle">Your new password must be at least 8 characters.</p>
            <?php if ($error != "") { ?>
                <div class="error"><?php echo h($error); ?></div>
            <?php } ?>
            <form method="POST" action="reset_confirm.php?token=<?php echo h(urlencode($token)); ?>" novalidate>
                <label>New Password</label>
                <div class="password-wrap">
                    <input type="password" name="new_password" class="<?php echo ec('new_password', $errorFields); ?>">
                    <button type="button" class="toggle-password">Show</button>
                </div>
                <label>Confirm New Password</label>
                <div class="password-wrap">
                    <input type="password" name="confirm_password" class="<?php echo ec('confirm_password', $errorFields); ?>">
                    <button type="button" class="toggle-password">Show</button>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </form>
        <?php } ?>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
