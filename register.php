<?php
require_once "config.php";
$error = "";
$errorFields = [];
$full_name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

    if ($full_name === "") {
        $errorFields[] = "full_name";
    }
    if ($email === "") {
        $errorFields[] = "email";
    }
    if ($password === "") {
        $errorFields[] = "password";
    }
    if ($confirm === "") {
        $errorFields[] = "confirm_password";
    }

    if (count($errorFields) > 0) {
        $error = "Please fill in all required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email";
        $errorFields = ["email"];
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
        $errorFields = ["password"];
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
        $errorFields = ["password", "confirm_password"];
    } elseif (fetch_one($conn, "SELECT user_id FROM user WHERE email=?", "s", [$email])) {
        $error = "Something went wrong creating your account. If you already registered, try logging in instead.";
        $errorFields = ["email"];
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        execute($conn, "INSERT INTO user (name, email, password_hash) VALUES (?, ?, ?)", "sss", [$full_name, $email, $hashed]);
        execute($conn, "INSERT INTO graduate (user_id) VALUES (?)", "i", [mysqli_insert_id($conn)]);
        flash("Account created successfully! Log in to start exploring.");
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
<title>Register - GradStart</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <img src="logo.png" class="auth-logo" alt="GradStart">
        <h2>Your career starts here! 🎓</h2>
        <p class="subtitle">Create your free account and find the training that fits your goals.</p>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <form method="POST" action="register.php" novalidate>
            <label>Full Name</label>
            <input type="text" name="full_name" class="<?php echo ec('full_name', $errorFields); ?>" value="<?php echo h($full_name); ?>">
            <label>Email</label>
            <input type="email" name="email" class="<?php echo ec('email', $errorFields); ?>" value="<?php echo h($email); ?>">
            <label>Password</label>
            <div class="password-wrap">
                <input type="password" name="password" class="<?php echo ec('password', $errorFields); ?>">
                <button type="button" class="toggle-password">Show</button>
            </div>
            <label>Confirm Password</label>
            <div class="password-wrap">
                <input type="password" name="confirm_password" class="<?php echo ec('confirm_password', $errorFields); ?>">
                <button type="button" class="toggle-password">Show</button>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <div class="links">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
