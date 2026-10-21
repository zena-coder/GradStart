<?php
require_once "config.php";
$error = "";
$errorFields = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";

    if ($email === "") {
        $errorFields[] = "email";
    }
    if ($password === "") {
        $errorFields[] = "password";
    }

    if (count($errorFields) > 0) {
        $error = "Please fill in all required fields";
    } else {
        $user = fetch_one($conn, "SELECT * FROM user WHERE email=?", "s", [$email]);
        $isAdmin = ($user && fetch_one($conn, "SELECT user_id FROM admin WHERE user_id=?", "i", [$user['user_id']])) ? 1 : 0;
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['name'];
            $_SESSION['is_admin'] = $isAdmin;
            if ($isAdmin == 1) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
        $error = "Incorrect email or password";
        $errorFields = ["email", "password"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log In - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <img src="logo.png" class="auth-logo" alt="GradStart">
        <h2>Welcome, future professional! 🚀</h2>
        <p class="subtitle">Log in and discover the training opportunities made for you.</p>
        <?php include "message.php"; ?>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <form method="POST" action="login.php" novalidate>
            <label>Email</label>
            <input type="email" name="email" class="<?php echo ec('email', $errorFields); ?>" value="<?php echo h(isset($email) ? $email : ''); ?>">
            <label>Password</label>
            <div class="password-wrap">
                <input type="password" name="password" class="<?php echo ec('password', $errorFields); ?>">
                <button type="button" class="toggle-password">Show</button>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>
        <div class="links">
            <a href="reset_password.php">Forgot password?</a>
        </div>
        <div class="links">
            New here? <a href="register.php">Create your account</a>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
