<?php
require_once "config.php";
require_once "mailer.php";
$error = "";
$success = "";
$errorFields = [];
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";

    if ($email === "") {
        $error = "Please fill in all required fields";
        $errorFields = ["email"];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email";
        $errorFields = ["email"];
    } else {
        $user = fetch_one($conn, "SELECT user_id FROM user WHERE email=?", "s", [$email]);
        if (!$user) {
            $error = "We couldn't send a reset link. Please check your email and try again.";
            $errorFields = ["email"];
        } else {
            $token = bin2hex(random_bytes(16));
            execute($conn, "DELETE FROM password_resets WHERE email=?", "s", [$email]);
            execute($conn, "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))", "ss", [$email, $token]);
            $link = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), "/\\") . "/reset_confirm.php?token=" . $token;
            $body = "<p>Hello,</p><p>We received a request to reset your GradStart password. Click the link below to choose a new one:</p>"
                . "<p><a href=\"" . $link . "\">Reset my password</a></p>"
                . "<p>This link is valid for 1 hour. If you didn't ask for this, you can safely ignore this email.</p>";
            if (send_email($email, "Reset your GradStart password", $body)) {
                $success = "A reset link has been sent to your email. Please check your inbox.";
            } elseif (SMTP_USER == "your-email@gmail.com") {
                header("Location: reset_confirm.php?token=" . $token);
                exit;
            } else {
                file_put_contents(__DIR__ . "/reset_links.txt", $email . " " . $link . "\n", FILE_APPEND);
                $error = "We couldn't send a reset link. Please check your email and try again.";
                $errorFields = ["email"];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Forgot Password - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <img src="logo.png" class="auth-logo" alt="GradStart">
        <h2>Forgot your password?</h2>
        <p class="subtitle">No worries! Enter your email and we'll send you a link to set a new one.</p>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <?php if ($success != "") { ?>
            <div class="success"><?php echo h($success); ?></div>
        <?php } ?>
        <form method="POST" action="reset_password.php" novalidate>
            <label>Email</label>
            <input type="email" name="email" class="<?php echo ec('email', $errorFields); ?>" value="<?php echo h($email); ?>">
            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
        </form>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
